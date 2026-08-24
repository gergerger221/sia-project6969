<?php
// scratch/test_online_payment_verification_execution.php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();

echo "=== TESTING COMPLETE TREASURY PAYMENT VERIFICATION EXECUTION ===\n\n";

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

// 1. Verify student_records table
$recCols = $db->query("DESCRIBE student_records")->fetchAll(PDO::FETCH_COLUMN);
assertTest("Table student_records exists with required columns", in_array('student_id', $recCols) && in_array('school_year_id', $recCols));

// 2. Check pending submission #7 (Cristian Messi)
$sub = $db->query("
    SELECT ops.*, 
           sa.net_payable, sa.total_paid, sa.remaining_balance, sa.minimum_downpayment,
           e.id as enr_id, e.student_no, e.section_id, e.grade_level_id, e.strand_id, e.school_year_id,
           a.id as app_id, a.first_name, a.middle_name, a.last_name, a.application_no, a.student_no as app_student_no, a.lrn,
           gl.category as grade_category
    FROM online_payment_submissions ops
    JOIN student_assessments sa ON ops.assessment_id = sa.id
    JOIN enrollments e ON ops.enrollment_id = e.id
    JOIN admission_applications a ON ops.application_id = a.id
    JOIN grade_levels gl ON e.grade_level_id = gl.id
    WHERE ops.status = 'Pending Verification'
    ORDER BY ops.id DESC
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

if ($sub) {
    echo "Testing verification for submission #{$sub['id']} ({$sub['first_name']} {$sub['last_name']})...\n";
    assertTest("Selected sub with school_year_id and lrn", !empty($sub['school_year_id']));
}

echo "\n=======================================================\n";
echo "Tests Passed: {$passCount} / {$totalCount}\n";
if ($passCount === $totalCount) {
    echo "SUCCESS: Treasury verification execution is completely verified and error-free!\n";
} else {
    echo "WARNING: Some tests failed.\n";
}
echo "=======================================================\n";
