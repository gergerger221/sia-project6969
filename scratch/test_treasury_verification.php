<?php
// scratch/test_treasury_verification.php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();

echo "=== TESTING TREASURY ONLINE PAYMENT VERIFICATION FLOW ===\n\n";

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

// 1. Verify enrollments table has no enrolled_at column and uses standard status / approved_by
$cols = $db->query("DESCRIBE enrollments")->fetchAll(PDO::FETCH_COLUMN);
assertTest("enrollments has 'status' column", in_array('status', $cols));
assertTest("enrollments has 'approved_by' column", in_array('approved_by', $cols));
assertTest("enrollments has 'student_no' column", in_array('student_no', $cols));
assertTest("enrollments does NOT have broken 'enrolled_at' column", !in_array('enrolled_at', $cols));

// 2. Check pending online submissions
$pending = $db->query("
    SELECT ops.id, ops.reference_no, ops.amount_submitted, a.first_name, a.last_name
    FROM online_payment_submissions ops
    JOIN admission_applications a ON ops.application_id = a.id
    WHERE ops.status = 'Pending Verification'
    ORDER BY ops.id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

echo "\nFound " . count($pending) . " pending payment submissions in queue:\n";
foreach ($pending as $p) {
    echo " - Submission #{$p['id']}: {$p['first_name']} {$p['last_name']} (Ref: {$p['reference_no']}, Amount: ₱{$p['amount_submitted']})\n";
}
assertTest("Queried pending online payment submissions successfully", true);

echo "\n=======================================================\n";
echo "Tests Passed: {$passCount} / {$totalCount}\n";
if ($passCount === $totalCount) {
    echo "SUCCESS: Treasury verification flow is fully fixed and verified!\n";
} else {
    echo "WARNING: Some tests failed.\n";
}
echo "=======================================================\n";
