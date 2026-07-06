<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Models\Trainer;
use App\Modules\Families\Repositories\MemberRepository;
use App\Modules\Families\Repositories\TrainerRepository;
use App\Modules\Families\Validators\TrainerValidator;
use Illuminate\Database\Capsule\Manager as DB;

class TrainerService
{
    public function __construct(
        private readonly TrainerRepository  $trainerRepo,
        private readonly MemberRepository   $memberRepo,
        private readonly AddressService     $addressService,
        private readonly TrainerValidator   $validator,
        private readonly ActivityLogService $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->trainerRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Trainer
    {
        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $profileId = (int) $data['profile_id'];
        $profile   = $this->memberRepo->findById($profileId);
        if (!$profile) {
            throw new NotFoundException('Member profile not found.');
        }

        $existingTrainer = $this->trainerRepo->findByProfileId($profileId);
        if ($existingTrainer) {
            throw new DuplicateException('This profile is already registered as a trainer.');
        }

        return DB::transaction(function () use ($data, $profileId, $jwt) {
            $addressId = null;
            if (!empty($data['address'])) {
                $address   = $this->addressService->createAddress($data['address']);
                $addressId = $address->id;
            }

            $trainer = Trainer::create([
                'profile_id'       => $profileId,
                'trainer_code'     => 'TR-TEMP-' . time(),
                'specialization'   => $data['specialization'] ?? null,
                'experience_years' => isset($data['experience_years']) ? (int) $data['experience_years'] : null,
                'bio'              => $data['bio'] ?? null,
                'joined_at'        => $data['joined_at'] ?? null,
                'address_id'       => $addressId,
                'status'           => 'active',
            ]);

            $trainer->update(['trainer_code' => TrainerRepository::generateTrainerCode($trainer->id)]);
            $trainer = $this->trainerRepo->findWithRelations($trainer->id);

            $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
            $this->activityLog->log($actorId, 'create', 'trainers', $trainer->id, null, $trainer->toArray());

            return $trainer;
        });
    }

    public function show(int $id, array $jwt): Trainer
    {
        $trainer = $this->trainerRepo->findWithRelations($id);
        if (!$trainer) {
            throw new NotFoundException('Trainer not found.');
        }

        $roles     = $jwt['roles'] ?? [];
        $isAdmin   = !empty(array_intersect($roles, ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']));
        $isTrainer = in_array('trainer', $roles, true);
        $isOwnProfile = (int) ($jwt['profile_id'] ?? 0) === (int) $trainer->profile_id;

        if (!$isAdmin && !($isTrainer && $isOwnProfile)) {
            throw new UnauthorizedException();
        }

        return $trainer;
    }

    public function update(int $id, array $data, array $jwt): Trainer
    {
        $trainer = $this->trainerRepo->findWithRelations($id);
        if (!$trainer) {
            throw new NotFoundException('Trainer not found.');
        }

        $roles             = $jwt['roles'] ?? [];
        $isElevatedAdmin   = !empty(array_intersect($roles, ['admin_super', 'admin_program_manager']));
        $isOwnTrainerProfile = (int) ($jwt['profile_id'] ?? 0) === (int) $trainer->profile_id
            && in_array('trainer', $roles, true);

        if (!$isElevatedAdmin && !$isOwnTrainerProfile) {
            throw new UnauthorizedException();
        }

        // Trainers updating their own profile can only change bio and specialization
        if (!$isElevatedAdmin && $isOwnTrainerProfile) {
            $data = array_intersect_key($data, array_flip(['bio', 'specialization']));
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues = $trainer->toArray();

        return DB::transaction(function () use ($id, $data, $trainer, $oldValues, $jwt) {
            if (!empty($data['address'])) {
                if ($trainer->address_id) {
                    $this->addressService->updateAddress($trainer->address, $data['address']);
                } else {
                    $address = $this->addressService->createAddress($data['address']);
                    $trainer->update(['address_id' => $address->id]);
                }
                unset($data['address']);
            }

            $allowed    = ['specialization', 'experience_years', 'bio', 'joined_at', 'status'];
            $updateData = array_intersect_key($data, array_flip($allowed));
            if (!empty($updateData)) {
                $this->trainerRepo->update($trainer, $updateData);
            }

            $updated = $this->trainerRepo->findWithRelations($id);

            $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
            $this->activityLog->log($actorId, 'update', 'trainers', $id, $oldValues, $updated->toArray());

            return $updated;
        });
    }
}
