<?php

declare(strict_types=1);

namespace App\Modules\Payments\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\Repositories\EnrollmentRepository;
use App\Modules\Families\Repositories\FamilyRepository;
use App\Modules\Payments\DTOs\CreatePaymentDTO;
use App\Modules\Payments\Models\Payment;
use App\Modules\Payments\Policies\PaymentPolicy;
use App\Modules\Payments\Repositories\PaymentRepository;
use App\Modules\Payments\Validators\PaymentValidator;
use Illuminate\Database\Capsule\Manager as DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepository    $paymentRepo,
        private readonly EnrollmentRepository $enrollmentRepo,
        private readonly FamilyRepository     $familyRepo,
        private readonly PaymentPolicy        $policy,
        private readonly PaymentValidator     $validator,
        private readonly ActivityLogService   $activityLog,
    ) {}

    public function list(array $filters, array $jwt): array
    {
        if (!$this->policy->canViewAll($jwt)) {
            throw new UnauthorizedException('You do not have permission to view all payments.');
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->paymentRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function listForFamily(int $familyId, array $filters, array $jwt): array
    {
        $family = $this->familyRepo->findById($familyId);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        if (!$this->policy->canViewFamilyPayments($jwt, $familyId)) {
            throw new UnauthorizedException('You do not have permission to view payments for this family.');
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->paymentRepo->paginateForFamily($familyId, $filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Payment
    {
        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (!$this->policy->canCreate($jwt)) {
            throw new UnauthorizedException('Only accounts admins and super admins can create payments.');
        }

        $dto = CreatePaymentDTO::fromArray($data);

        $family = $this->familyRepo->findById($dto->familyId);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }
        if ($family->status !== 'active') {
            throw new NotFoundException('Family is not active.');
        }

        if ($dto->enrollmentId !== null) {
            $enrollment = $this->enrollmentRepo->findWithRelations($dto->enrollmentId);
            if (!$enrollment) {
                throw new NotFoundException('Enrollment not found.');
            }
            if ((int) $enrollment->family_id !== $dto->familyId) {
                throw new NotFoundException('Enrollment does not belong to the specified family.');
            }
        }

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;

        return DB::transaction(function () use ($dto, $actorId) {
            $payment = Payment::create([
                'family_id'             => $dto->familyId,
                'enrollment_id'         => $dto->enrollmentId,
                'payment_type'          => $dto->paymentType,
                'amount'                => $dto->amount,
                'payment_method'        => $dto->paymentMethod,
                'transaction_reference' => $dto->transactionReference,
                'status'                => $dto->status,
                'notes'                 => $dto->notes,
                'paid_at'               => $dto->status === 'completed' ? now() : null,
            ]);

            if ($dto->enrollmentId !== null) {
                $this->recalculateEnrollmentPaymentStatus($dto->enrollmentId);
            }

            $this->activityLog->log(
                $actorId,
                'payment_recorded',
                'payments',
                $payment->id,
                null,
                [
                    'amount'       => $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'status'       => $payment->status,
                    'family_id'    => $payment->family_id,
                ]
            );

            return $payment->fresh();
        });
    }

    public function show(int $id, array $jwt): Payment
    {
        $payment = $this->paymentRepo->findById($id);
        if (!$payment) {
            throw new NotFoundException('Payment not found.');
        }

        if (!$this->policy->canView($jwt, $payment)) {
            throw new UnauthorizedException('You do not have permission to view this payment.');
        }

        return $payment;
    }

    public function update(int $id, array $data, array $jwt): Payment
    {
        if (!$this->policy->canUpdate($jwt)) {
            throw new UnauthorizedException('You do not have permission to update payments.');
        }

        $payment = $this->paymentRepo->findById($id);
        if (!$payment) {
            throw new NotFoundException('Payment not found.');
        }

        if ($payment->status === 'completed') {
            throw new BusinessRuleException(
                'PAYMENT_COMPLETED',
                'A completed payment cannot be edited. Create a refund payment instead.'
            );
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldStatus = $payment->status;
        $actorId   = (int) ($jwt['profile_id'] ?? 0) ?: null;

        return DB::transaction(function () use ($payment, $data, $oldStatus, $actorId) {
            $updates = [];

            if (isset($data['status'])) {
                $updates['status'] = $data['status'];
                if ($data['status'] === 'completed' && $payment->paid_at === null) {
                    $updates['paid_at'] = now();
                }
            }
            if (array_key_exists('transaction_reference', $data)) {
                $updates['transaction_reference'] = $data['transaction_reference'];
            }
            if (array_key_exists('notes', $data)) {
                $updates['notes'] = $data['notes'];
            }

            if (!empty($updates)) {
                $payment->update($updates);
            }

            if ($payment->enrollment_id !== null) {
                $this->recalculateEnrollmentPaymentStatus((int) $payment->enrollment_id);
            }

            $this->activityLog->log(
                $actorId,
                'payment_updated',
                'payments',
                $payment->id,
                ['status' => $oldStatus],
                [
                    'amount'       => $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'status'       => $payment->fresh()->status,
                    'family_id'    => $payment->family_id,
                ]
            );

            return $payment->fresh();
        });
    }

    public function recalculateEnrollmentPaymentStatus(int $enrollmentId): void
    {
        $enrollment = $this->enrollmentRepo->findById($enrollmentId);
        if (!$enrollment) {
            return;
        }

        $netPaid   = $this->paymentRepo->getNetPaidForEnrollment($enrollmentId);
        $feeAmount = (float) $enrollment->fee_amount;

        if ($netPaid <= 0) {
            $paymentStatus = 'unpaid';
        } elseif ($netPaid < $feeAmount) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'paid';
        }

        $enrollment->update(['payment_status' => $paymentStatus]);
    }
}
