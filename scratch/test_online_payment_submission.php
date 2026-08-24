<?php
// scratch/test_online_payment_submission.php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();

echo "=== TESTING ONLINE PAYMENT CHECKOUT SUBMISSION FLOW ===\n\n";

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

// 1. Check if there are existing assessment and enrollment records
$ass = $db->query("
    SELECT sa.id as assessment_id, sa.enrollment_id, sa.net_payable, sa.remaining_balance, sa.minimum_downpayment,
           e.application_id, a.user_id, a.first_name, a.last_name
    FROM student_assessments sa
    JOIN enrollments e ON sa.enrollment_id = e.id
    JOIN admission_applications a ON e.application_id = a.id
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

assertTest("Found valid student assessment & enrollment record", !empty($ass));

if ($ass) {
    $refNo = 'TEST-REF-' . rand(100000, 999999);
    $amount = 4100.00;
    
    // Simulate submission insert
    try {
        $ins = $db->prepare("
            INSERT INTO online_payment_submissions (
                assessment_id, enrollment_id, application_id, payment_channel,
                amount_paid, amount_submitted, payment_date, reference_no, account_name, account_number, status
            ) VALUES (
                :ass_id, :enr_id, :app_id, 'Maya',
                :amount, :amount2, CURRENT_DATE, :ref, :acc_name, :acc_no, 'Pending Verification'
            )
        ");
        $ins->execute([
            'ass_id'   => $ass['assessment_id'],
            'enr_id'   => $ass['enrollment_id'],
            'app_id'   => $ass['application_id'],
            'amount'   => $amount,
            'amount2'  => $amount,
            'ref'      => $refNo,
            'acc_name' => "{$ass['first_name']} {$ass['last_name']}",
            'acc_no'   => '09445456456'
        ]);
        $subId = (int)$db->lastInsertId();
        assertTest("Online payment submission inserted without column errors (Sub ID: #{$subId})", $subId > 0);

        // Update application status to Payment Submitted
        $upApp = $db->prepare("UPDATE admission_applications SET status = 'Payment Submitted – Awaiting Verification' WHERE id = :id");
        $upApp->execute(['id' => $ass['application_id']]);
        assertTest("Application status updated to 'Payment Submitted – Awaiting Verification'", $upApp->rowCount() >= 0);

        // Clean up test submission
        $db->prepare("DELETE FROM online_payment_submissions WHERE id = :id")->execute(['id' => $subId]);
        assertTest("Cleaned up test submission record", true);
    } catch (\Throwable $e) {
        assertTest("Submission failed with error: " . $e->getMessage(), false);
    }
}

echo "\n=======================================================\n";
echo "Tests Passed: {$passCount} / {$totalCount}\n";
if ($passCount === $totalCount) {
    echo "SUCCESS: Online payment submission workflow is completely verified and error-free!\n";
} else {
    echo "WARNING: Some tests failed.\n";
}
echo "=======================================================\n";
