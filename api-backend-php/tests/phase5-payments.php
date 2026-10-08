<?php

declare(strict_types=1);

require __DIR__ . '/../config/autoload.php';
require __DIR__ . '/../services/payments.php';

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "phase-5 payment checks require PDO_SQLITE\n");
    exit(2);
}

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
$database = new Medoo\Medoo(['pdo' => $pdo, 'type' => 'sqlite', 'database' => ':memory:']);

$pdo->exec('CREATE TABLE families (id INTEGER PRIMARY KEY, status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE family_members (id INTEGER PRIMARY KEY, family_id INTEGER NOT NULL, profile_id INTEGER NOT NULL, member_role TEXT NOT NULL, status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE enrollments (id INTEGER PRIMARY KEY, family_id INTEGER NOT NULL, fee_amount DECIMAL(10,2) NOT NULL, payment_status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    family_id INTEGER NOT NULL,
    enrollment_id INTEGER NULL,
    payment_type TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method TEXT NOT NULL,
    transaction_reference TEXT NULL,
    status TEXT NOT NULL,
    notes TEXT NULL,
    paid_at TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)');
$pdo->exec('CREATE TABLE activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    actor_profile_id INTEGER NULL,
    action TEXT NOT NULL,
    entity_type TEXT NOT NULL,
    entity_id INTEGER NOT NULL,
    old_values TEXT NULL,
    new_values TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)');
$pdo->exec("INSERT INTO families (id, status) VALUES (1, 'active'), (2, 'inactive')");
$pdo->exec("INSERT INTO family_members (family_id, profile_id, member_role, status) VALUES (1, 10, 'primary', 'active'), (1, 11, 'normal', 'active'), (1, 12, 'primary', 'removed')");
$pdo->exec("INSERT INTO enrollments (id, family_id, fee_amount, payment_status) VALUES (1, 1, 100.00, 'unpaid'), (2, 2, 50.00, 'unpaid')");

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};
$insertPayment = static function (int $enrollmentId, string $type, string $amount, string $status = 'completed') use ($pdo): void {
    $statement = $pdo->prepare(
        'INSERT INTO payments (family_id, enrollment_id, payment_type, amount, payment_method, status) ' .
        'VALUES (1, :enrollment_id, :payment_type, :amount, :payment_method, :status)'
    );
    $statement->execute([
        ':enrollment_id' => $enrollmentId,
        ':payment_type' => $type,
        ':amount' => $amount,
        ':payment_method' => 'cash',
        ':status' => $status,
    ]);
};
$paymentStatus = static function () use ($pdo): string {
    return (string) $pdo->query('SELECT payment_status FROM enrollments WHERE id = 1')->fetchColumn();
};

$valid = [
    'family_id' => 1, 'enrollment_id' => 1, 'payment_type' => 'class_fee',
    'amount' => '25.00', 'payment_method' => 'cash', 'status' => 'pending',
];
$assert(kcdf_payment_validate_create($valid) === [], 'A valid class payment should pass validation.');
$assert(isset(kcdf_payment_validate_create(array_merge($valid, ['amount' => '0']))['amount']), 'Zero amount must fail validation.');
$assert(isset(kcdf_payment_validate_create(array_merge($valid, ['amount' => '1.001']))['amount']), 'Amounts with more than two decimals must fail validation.');
$assert(isset(kcdf_payment_validate_create(array_merge($valid, ['payment_method' => 'online']))['transaction_reference']), 'Non-cash payments must require a transaction reference.');
$assert(isset(kcdf_payment_validate_create(array_merge($valid, ['payment_type' => 'donation', 'enrollment_id' => null]))['enrollment_id']) === false, 'Non-class payments may omit an enrollment.');
$assert(kcdf_payment_is_accounts_admin(['roles' => ['admin_accounts']]), 'Accounts admins must be allowed to manage payments.');
$assert(!kcdf_payment_is_accounts_admin(['roles' => ['admin_program_manager']]), 'Program managers must not be allowed to manage payments.');
$assert(kcdf_payment_can_view($database, ['profile_id' => 10, 'roles' => ['family_primary']], 1), 'Active primary members must view family payments.');
$assert(!kcdf_payment_can_view($database, ['profile_id' => 11, 'roles' => ['family_normal']], 1), 'Normal family members must not view family payments.');
$assert(!kcdf_payment_can_view($database, ['profile_id' => 12, 'roles' => ['family_primary']], 1), 'Removed primary memberships must not grant access.');

foreach ([
    [1, 'class_fee', '40.00', 'partial'],
    [1, 'class_fee', '60.00', 'paid'],
    [1, 'refund', '25.00', 'partial'],
    [1, 'refund', '75.00', 'unpaid'],
] as [$enrollmentId, $type, $amount, $expected]) {
    kcdf_payment_transaction($database, static function (PDO $connection) use ($insertPayment, $enrollmentId, $type, $amount): void {
        $insertPayment($enrollmentId, $type, $amount);
        kcdf_payment_recalculate_enrollment($connection, $enrollmentId, true);
    });
    $assert($paymentStatus() === $expected, "Expected enrollment payment status {$expected} after {$type} of {$amount}.");
}

$insertPayment(1, 'class_fee', '500.00', 'pending');
kcdf_payment_transaction($database, static function (PDO $connection): void {
    kcdf_payment_recalculate_enrollment($connection, 1, true);
});
$assert($paymentStatus() === 'unpaid', 'Pending payments must not contribute to the paid total.');

$assert(kcdf_payment_page($database, ['per_page' => 2], 1)['meta']['total'] === 5, 'Family payment filtering must return only that family’s payments.');
$assert(count(kcdf_payment_page($database, ['per_page' => 2], 1)['data']) === 2, 'Payment pagination must honor per_page.');

try {
    kcdf_payment_assert_editable(['status' => 'completed']);
    $assert(false, 'Completed payments must be immutable.');
} catch (KcdfPaymentException $exception) {
    $assert($exception->errorCode === 'PAYMENT_COMPLETED', 'Completed payment edits must use the PAYMENT_COMPLETED error code.');
}
kcdf_payment_assert_editable(['status' => 'pending']);
$assert(true, 'Pending payments must remain editable.');
$assert(kcdf_payment_validate_update(['status' => 'completed']) === [], 'Pending-to-completed must be a valid transition.');
$assert(isset(kcdf_payment_validate_update(['status' => 'refunded'])['status']), 'Refunded must not be a payment update status; refunds use a separate record.');
$completionUpdates = kcdf_payment_update_values(['status' => 'completed'], ['status' => 'pending', 'paid_at' => null], '2026-10-08 10:00:00');
$assert($completionUpdates === ['status' => 'completed', 'paid_at' => '2026-10-08 10:00:00'], 'Completing a payment must set paid_at.');
$assert(kcdf_payment_update_values(['status' => 'failed'], ['status' => 'pending', 'paid_at' => null], '2026-10-08 10:00:00') === ['status' => 'failed'], 'Non-completed transitions must not set paid_at.');

try {
    kcdf_payment_assert_family_active(['status' => 'inactive']);
    $assert(false, 'Inactive families must not receive payments.');
} catch (KcdfPaymentException $exception) {
    $assert($exception->getMessage() === 'Family is not active.', 'Inactive-family rejection must preserve the source error message.');
}
try {
    kcdf_payment_assert_enrollment_family(['family_id' => 2], 1);
    $assert(false, 'An enrollment from another family must be rejected.');
} catch (KcdfPaymentException $exception) {
    $assert($exception->getMessage() === 'Enrollment does not belong to the specified family.', 'Enrollment-family mismatch must preserve the source error message.');
}

$paymentsBeforeRollback = (int) $pdo->query('SELECT COUNT(*) FROM payments')->fetchColumn();
$logsBeforeRollback = (int) $pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn();
try {
    kcdf_payment_transaction($database, static function (PDO $connection) use ($database): void {
        $connection->exec("INSERT INTO payments (family_id, payment_type, amount, payment_method, status) VALUES (1, 'donation', 10, 'cash', 'completed')");
        kcdf_identity_log($database, [], 'payment_recorded', 'payments', (int) $connection->lastInsertId(), null, ['amount' => 10]);
        throw new RuntimeException('force rollback');
    });
    $assert(false, 'The rollback fixture should throw.');
} catch (RuntimeException $exception) {
    $assert($exception->getMessage() === 'force rollback', 'The original transaction error must be rethrown.');
}
$assert((int) $pdo->query('SELECT COUNT(*) FROM payments')->fetchColumn() === $paymentsBeforeRollback, 'A failed payment operation must roll back the payment.');
$assert((int) $pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn() === $logsBeforeRollback, 'A failed payment operation must roll back its activity log.');

echo "phase-5 payment checks passed ({$checks} assertions)\n";
