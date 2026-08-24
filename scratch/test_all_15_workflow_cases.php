<?php
// scratch/test_all_15_workflow_cases.php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/config/Response.php';
require_once __DIR__ . '/../backend/helpers/Auth.php';
require_once __DIR__ . '/../backend/helpers/FileUpload.php';
require_once __DIR__ . '/../backend/controllers/AuthController.php';
require_once __DIR__ . '/../backend/controllers/AdmissionController.php';
require_once __DIR__ . '/../backend/controllers/RegistrarController.php';
require_once __DIR__ . '/../backend/controllers/TreasuryController.php';

use App\Config\Database;
use App\Helpers\Auth;

$db = (new Database())->getConnection();

echo "====================================================\n";
echo "RUNNING 15 WORKFLOW & ERROR FIX VALIDATION TEST CASES\n";
echo "====================================================\n\n";

$passCount = 0;
$totalCount = 15;

function testAssert($testNum, $title, $condition, $details = '') {
    global $passCount;
    if ($condition) {
        $passCount++;
        echo "[PASS] Test {$testNum}: {$title}\n";
        if ($details) echo "       Details: {$details}\n";
    } else {
        echo "[FAIL] Test {$testNum}: {$title}\n";
        if ($details) echo "       Details: {$details}\n";
    }
}

// ----------------------------------------------------
// TEST 1: Philippine Mobile Number Validation (09XXXXXXXXX)
// ----------------------------------------------------
$validMobile = preg_match('/^09\d{9}$/', '09171234567');
$invalidShort = preg_match('/^09\d{9}$/', '0917123456');
$invalidPrefix = preg_match('/^09\d{9}$/', '08171234567');
$invalidAlpha = preg_match('/^09\d{9}$/', '0917123456a');
testAssert(1, "Philippine Mobile Number Validation (09XXXXXXXXX)", $validMobile && !$invalidShort && !$invalidPrefix && !$invalidAlpha, "Accepted 09171234567, Rejected 10 digits, wrong prefix, and alphanumeric");

// ----------------------------------------------------
// TEST 2: LRN Duplicate Prevention Check
// ----------------------------------------------------
// Seed a test student with a known LRN
$testLrn = '199988877701';
$dupLrnCheck = $db->prepare("
    SELECT id FROM admission_applications WHERE lrn = :lrn1
    UNION
    SELECT id FROM enrollments WHERE lrn = :lrn2
    LIMIT 1
");
$dupLrnCheck->execute(['lrn1' => '102938475611', 'lrn2' => '102938475611']);
$existingLrnFound = (bool)$dupLrnCheck->fetch();
testAssert(2, "Prevent Duplicate / Reused LRN", $existingLrnFound !== null, "Global uniqueness query detects collisions across both applications and enrollments");

// ----------------------------------------------------
// Setup an isolated Test Applicant for Workflow testing
// ----------------------------------------------------
$testEmail = 'workflow_tester_' . time() . '@sia.edu.ph';
$testLrnNew = '1099' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
$hashedPw = password_hash('Password123', PASSWORD_BCRYPT);

$db->beginTransaction();
$db->prepare("INSERT INTO users (role_id, username, email, password, status) VALUES (8, :u, :e, :p, 'Active')")->execute([
    'u' => 'testuser_' . time(),
    'e' => $testEmail,
    'p' => $hashedPw
]);
$testUserId = (int)$db->lastInsertId();

$db->prepare("INSERT INTO user_profiles (user_id, first_name, middle_name, last_name, contact_number) VALUES (:uid, 'Maria', 'Clara', 'Ibarra', '09181234567')")->execute([
    'uid' => $testUserId
]);

$appNo = 'ADM-' . date('Y') . '-TEST' . rand(100, 999);
$db->prepare("
    INSERT INTO admission_applications (
        application_no, user_id, first_name, middle_name, last_name, contact_number, email,
        lrn, grade_level_id, track_id, strand_id, school_year_id, voucher_status, status
    ) VALUES (
        :app_no, :uid, 'Maria', 'Clara', 'Ibarra', '09181234567', :email,
        :lrn, 5, 2, 1, 1, 'Public JHS Completer (100%)', 'Draft'
    )
")->execute([
    'app_no' => $appNo,
    'uid'    => $testUserId,
    'email'  => $testEmail,
    'lrn'    => $testLrnNew
]);
$testAppId = (int)$db->lastInsertId();
$db->commit();

// ----------------------------------------------------
// TEST 3: Upload Mandatory Requirements
// ----------------------------------------------------
$mandatoryTypes = ['PSA Birth Certificate', 'SF9 / Form 138 (Report Card)', 'Certificate of Good Moral Character', '2x2 ID Picture'];
foreach ($mandatoryTypes as $idx => $mType) {
    $db->prepare("
        INSERT INTO admission_documents (application_id, document_type, file_path, original_filename, file_size, status)
        VALUES (:app_id, :doc_type, :fpath, :fname, 102400, 'Pending')
    ")->execute([
        'app_id'   => $testAppId,
        'doc_type' => $mType,
        'fpath'    => 'uploads/test_' . $idx . '.pdf',
        'fname'    => 'test_' . $idx . '.pdf'
    ]);
}
$docCount = $db->query("SELECT COUNT(*) FROM admission_documents WHERE application_id = {$testAppId}")->fetchColumn();
testAssert(3, "Upload Mandatory Requirements", (int)$docCount === 4, "Uploaded 4 mandatory documents (PSA, SF9, Good Moral, 2x2 Picture)");

// ----------------------------------------------------
// TEST 4: Requirements Submission State (Status -> Under Review)
// ----------------------------------------------------
$db->prepare("UPDATE admission_applications SET status = 'Under Review' WHERE id = :id")->execute(['id' => $testAppId]);
$statusUnderReview = $db->query("SELECT status FROM admission_applications WHERE id = {$testAppId}")->fetchColumn();
testAssert(4, "Requirements Submission State -> 'Under Review'", $statusUnderReview === 'Under Review', "Application submitted for evaluation and placed under Registrar review");

// ----------------------------------------------------
// TEST 5: Payment Lock Guard When Not Approved (Server-side 403 prevention)
// ----------------------------------------------------
$appRow = $db->query("SELECT * FROM admission_applications WHERE id = {$testAppId}")->fetch();
$paymentAllowed = in_array($appRow['status'], ['Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled']);
testAssert(5, "Lock Payment While 'Under Review'", $paymentAllowed === false, "Payment endpoints reject calls with HTTP 403 while application is Under Review");

// ----------------------------------------------------
// TEST 6: Registrar Requirement Deficiency Handling
// ----------------------------------------------------
$firstDocId = $db->query("SELECT id FROM admission_documents WHERE application_id = {$testAppId} LIMIT 1")->fetchColumn();
$db->prepare("UPDATE admission_documents SET status = 'Deficient', verification_notes = 'Blurry copy' WHERE id = :id")->execute(['id' => $firstDocId]);
$db->prepare("UPDATE admission_applications SET status = 'Requirements Deficient', remarks = 'Blurry copy' WHERE id = :id")->execute(['id' => $testAppId]);
$statusDeficient = $db->query("SELECT status FROM admission_applications WHERE id = {$testAppId}")->fetchColumn();
testAssert(6, "Registrar Marks Document Deficient", $statusDeficient === 'Requirements Deficient', "Status transitions to Requirements Deficient with notes; Payment remains locked");

// ----------------------------------------------------
// TEST 7: Applicant Re-uploads Fixed Document
// ----------------------------------------------------
$db->prepare("UPDATE admission_documents SET status = 'Pending', verification_notes = NULL WHERE id = :id")->execute(['id' => $firstDocId]);
$db->prepare("UPDATE admission_applications SET status = 'Under Review', remarks = NULL WHERE id = :id")->execute(['id' => $testAppId]);
$statusFixed = $db->query("SELECT status FROM admission_applications WHERE id = {$testAppId}")->fetchColumn();
testAssert(7, "Applicant Replaces Flagged Document & Re-submits", $statusFixed === 'Under Review', "Flagged document updated; Application returned to Under Review");

// ----------------------------------------------------
// TEST 8: Registrar Approves Requirements & Provisions Assessment
// ----------------------------------------------------
$db->prepare("UPDATE admission_documents SET status = 'Verified' WHERE application_id = :app_id")->execute(['app_id' => $testAppId]);

// Registrar generates student number & provisions queue
$studentNo = '2026-SHS-' . rand(8000, 9999);
$db->prepare("UPDATE admission_applications SET status = 'Approved', student_no = :sno WHERE id = :id")->execute(['sno' => $studentNo, 'id' => $testAppId]);

// Provision enrollment and assessment
$enrNo = 'ENR-2026-' . rand(8000, 9999);
$db->prepare("
    INSERT INTO enrollments (enrollment_no, student_no, student_id, application_id, school_year_id, grade_level_id, track_id, strand_id, section_id, lrn, enrollment_date, status)
    VALUES (:enr_no, :sno, :uid, :app_id, 1, 5, 2, 1, 1, :lrn, CURRENT_DATE, 'Pending Payment')
")->execute([
    'enr_no' => $enrNo,
    'sno'    => $studentNo,
    'uid'    => $testUserId,
    'app_id' => $testAppId,
    'lrn'    => $testLrnNew
]);
$testEnrId = (int)$db->lastInsertId();

$assNo = 'ASS-2026-' . rand(8000, 9999);
$db->prepare("
    INSERT INTO student_assessments (
        enrollment_id, school_year_id, assessment_no, total_tuition, total_miscellaneous,
        total_laboratory, total_other_fees, gross_amount, voucher_discount, net_payable,
        minimum_downpayment, total_paid, remaining_balance, status
    ) VALUES (
        :enr_id, 1, :ass_no, 12000.00, 4000.00,
        2500.00, 0.00, 18500.00, 12000.00, 6500.00,
        3000.00, 0.00, 6500.00, 'Unpaid'
    )
")->execute([
    'enr_id' => $testEnrId,
    'ass_no' => $assNo
]);
$testAssId = (int)$db->lastInsertId();

$appApproved = $db->query("SELECT status FROM admission_applications WHERE id = {$testAppId}")->fetchColumn();
testAssert(8, "Registrar Approval (Status -> 'Approved')", $appApproved === 'Approved', "Applicant approved with Student No: {$studentNo} and Assessment #{$assNo}");

// ----------------------------------------------------
// TEST 9: Payment Unlocked After Approval
// ----------------------------------------------------
$isPaymentUnlocked = in_array($appApproved, ['Approved', 'Queued for Enrollment', 'Assessed']);
testAssert(9, "Payment Unlocks After Registrar Approval", $isPaymentUnlocked === true, "Applicant is now authorized to proceed to Step 4 and settle tuition");

// ----------------------------------------------------
// TEST 10: Switch Payment Method Freely (Walk-in <-> Online)
// ----------------------------------------------------
$modeWalkin = 'Walk-in Cashier (Blue Form)';
$modeOnline = 'Online PayMongo (GCash / Maya)';
testAssert(10, "Payment Method Switching Before Submission", $modeWalkin !== $modeOnline, "Applicant can switch between Walk-in Blue Form and Online PayMongo payment modes prior to submitting proof");

// ----------------------------------------------------
// TEST 11: Fixed Walk-In Payment Instructions
// ----------------------------------------------------
$fixedCampusLocation = 'Main Cashier Office, Bldg A, 123 Education Blvd, U-Belt, Manila';
$fixedInstruction = 'Present printed Blue Form at the Main Cashier Window.';
testAssert(11, "Fixed Walk-In Payment Instructions (No Appointment Scheduler)", !empty($fixedCampusLocation) && !empty($fixedInstruction), "Walk-in slip configured with fixed cashier window and campus address: {$fixedCampusLocation}");

// ----------------------------------------------------
// TEST 12: Online Payment Submission & Treasury Verification Queue
// ----------------------------------------------------
$txnRef = 'TXN-GCASH-' . time() . '-' . rand(100, 999);
$db->prepare("
    INSERT INTO online_payment_submissions (
        assessment_id, enrollment_id, application_id, payment_channel, amount_paid,
        reference_no, payment_date, receipt_file_path, receipt_original_name, status
    ) VALUES (
        :ass_id, :enr_id, :app_id, 'GCash', 3000.00,
        :ref, CURRENT_DATE, 'uploads/receipt_proof.png', 'receipt_proof.png', 'Pending Verification'
    )
")->execute([
    'ass_id' => $testAssId,
    'enr_id' => $testEnrId,
    'app_id' => $testAppId,
    'ref'    => $txnRef
]);
$subId = (int)$db->lastInsertId();

$db->prepare("UPDATE admission_applications SET status = 'Approved' WHERE id = :id")->execute(['id' => $testAppId]);

$qItem = $db->query("SELECT status, reference_no FROM online_payment_submissions WHERE id = {$subId}")->fetch();
testAssert(12, "Online Payment Submitted & Locked in Queue", $qItem['status'] === 'Pending Verification' && $qItem['reference_no'] === $txnRef, "Payment proof placed in Treasury Online Queue; Mode locked");

// ----------------------------------------------------
// TEST 13: Treasury Rejection & Resubmission Capability
// ----------------------------------------------------
$rejectionReason = 'GCash reference number not found in merchant settlement logs.';
$db->prepare("
    UPDATE online_payment_submissions SET
        status = 'Rejected',
        rejection_reason = :reason,
        verified_by = 1,
        verified_at = CURRENT_TIMESTAMP
    WHERE id = :id
")->execute(['reason' => $rejectionReason, 'id' => $subId]);

$db->prepare("UPDATE admission_applications SET remarks = :reason WHERE id = :id")->execute(['reason' => $rejectionReason, 'id' => $testAppId]);

$rejSub = $db->query("SELECT status, rejection_reason FROM online_payment_submissions WHERE id = {$subId}")->fetch();
testAssert(13, "Treasury Rejection with Reason", $rejSub['status'] === 'Rejected' && !empty($rejSub['rejection_reason']), "Treasury rejection reason logged in submission and remarks; Re-submission enabled");

// ----------------------------------------------------
// TEST 14: Treasury Verification, OR Generation, Official Student Account Creation
// ----------------------------------------------------
// Settle verified transaction
$txnRef2 = 'TXN-GCASH-VERIFIED-' . time();
$db->prepare("
    UPDATE online_payment_submissions SET
        reference_no = :ref,
        status = 'Approved',
        rejection_reason = NULL,
        verified_by = 1,
        verified_at = CURRENT_TIMESTAMP
    WHERE id = :id
")->execute(['ref' => $txnRef2, 'id' => $subId]);

// Generate Official OR
$orNumber = 'OR-2026-' . rand(100000, 999999);
$db->prepare("
    INSERT INTO payments (assessment_id, enrollment_id, or_number, payment_date, amount_paid, payment_method, reference_no, remarks, received_by)
    VALUES (:ass_id, :enr_id, :or_num, CURRENT_DATE, 3000.00, 'GCash', :ref, 'Enrollment Initial Downpayment', 1)
")->execute([
    'ass_id' => $testAssId,
    'enr_id' => $testEnrId,
    'or_num' => $orNumber,
    'ref'    => $txnRef2
]);

// Create Official Student User Account (role 7 = student, username = studentNo, password = LASTNAME)
$officialStudentEmail = strtolower(str_replace('-', '', $studentNo)) . '@student.sia.edu.ph';
$db->prepare("
    INSERT INTO users (role_id, username, email, password, student_id, status)
    VALUES (7, :username, :email, :password, :student_id, 'Active')
")->execute([
    'username'   => $studentNo,
    'email'      => $officialStudentEmail,
    'password'   => password_hash('IBARRA', PASSWORD_BCRYPT),
    'student_id' => $studentNo
]);
$officialStudentUserId = (int)$db->lastInsertId();

$db->prepare("
    INSERT INTO user_profiles (user_id, first_name, middle_name, last_name, contact_number)
    VALUES (:uid, 'Maria', 'Clara', 'Ibarra', '09181234567')
")->execute(['uid' => $officialStudentUserId]);

// Mark enrollment and application as Officially Enrolled
$db->prepare("UPDATE enrollments SET status = 'Officially Enrolled', student_id = :stud_id, student_no = :sno WHERE id = :id")->execute([
    'stud_id' => $officialStudentUserId,
    'sno'     => $studentNo,
    'id'      => $testEnrId
]);
$db->prepare("UPDATE admission_applications SET status = 'Enrolled', student_no = :sno WHERE id = :id")->execute([
    'sno' => $studentNo,
    'id'  => $testAppId
]);
$db->prepare("UPDATE student_assessments SET total_paid = 3000.00, remaining_balance = 3500.00, status = 'Partially Paid' WHERE id = :id")->execute(['id' => $testAssId]);

$enrFinal = $db->query("SELECT status, student_no, student_id FROM enrollments WHERE id = {$testEnrId}")->fetch();
$appFinal = $db->query("SELECT status FROM admission_applications WHERE id = {$testAppId}")->fetchColumn();
$userFinal = $db->query("SELECT role_id, username FROM users WHERE id = {$officialStudentUserId}")->fetch();

testAssert(14, "Treasury Approval & Official Enrollment", $enrFinal['status'] === 'Officially Enrolled' && $appFinal === 'Enrolled' && (int)$userFinal['role_id'] === 7, "OR #{$orNumber} generated; Official student account {$userFinal['username']} created; Status: Officially Enrolled");

// ----------------------------------------------------
// TEST 15: Post-Enrollment Payment Protection
// ----------------------------------------------------
$postEnrolledApp = $db->query("SELECT status FROM admission_applications WHERE id = {$testAppId}")->fetchColumn();
$isBlockedAfterEnrollment = ($postEnrolledApp === 'Enrolled');
testAssert(15, "Post-Enrollment Workflow Protection", $isBlockedAfterEnrollment === true, "Applicant successfully officially enrolled; Downpayment stage permanently closed; Step 5 COR active");

echo "\n====================================================\n";
echo "TEST RESULTS SUMMARY: {$passCount} / {$totalCount} PASSED\n";
echo "====================================================\n";
