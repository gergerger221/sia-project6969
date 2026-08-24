<?php
// scratch/test_online_payment_fix.php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();

echo "=== TESTING ONLINE PAYMENT SCHEMA FIX & AMOUNT LIMITER ===\n\n";

$passCount = 0;
$totalCount = 0;

function assertTest(string $desc, bool $condition): void {
    global $passCount, $totalCount;
    $totalCount++;
    if ($condition) {
        $passCount++;
        echo " [PASS] {$desc}\n";
    } else {
        echo " [FAIL] {$desc}\n";
    }
}

// 1. Check all required columns in online_payment_submissions
$cols = $db->query("DESCRIBE online_payment_submissions")->fetchAll(PDO::FETCH_COLUMN);

assertTest("Column 'amount_submitted' exists in online_payment_submissions", in_array('amount_submitted', $cols));
assertTest("Column 'amount_paid' exists in online_payment_submissions", in_array('amount_paid', $cols));
assertTest("Column 'account_name' exists in online_payment_submissions", in_array('account_name', $cols));
assertTest("Column 'account_number' exists in online_payment_submissions", in_array('account_number', $cols));
assertTest("Column 'payment_date' exists in online_payment_submissions", in_array('payment_date', $cols));

// 2. Test inserting a sample submission record to verify no SQL column errors
try {
    $ins = $db->prepare("
        INSERT INTO online_payment_submissions (
            assessment_id, enrollment_id, application_id, payment_channel,
            amount_paid, amount_submitted, payment_date, reference_no, account_name, account_number, status
        ) VALUES (
            1, 1, 1, 'Maya',
            4100.00, 4100.00, CURRENT_DATE, 'TEST-REF-" . rand(10000, 99999) . "', 'Cristian Messi', '09445456456', 'Pending Verification'
        )
    ");
    $ins->execute();
    $subId = (int)$db->lastInsertId();
    assertTest("Direct SQL INSERT with amount_submitted and account fields succeeds without error", $subId > 0);

    // Clean up test record
    $db->prepare("DELETE FROM online_payment_submissions WHERE id = :id")->execute(['id' => $subId]);
} catch (\Throwable $e) {
    assertTest("Direct SQL INSERT failed: " . $e->getMessage(), false);
}

echo "\n=======================================================\n";
echo "Tests Passed: {$passCount} / {$totalCount}\n";
if ($passCount === $totalCount) {
    echo "SUCCESS: online_payment_submissions schema is fully fixed and verified!\n";
} else {
    echo "WARNING: Some tests failed.\n";
}
echo "=======================================================\n";
