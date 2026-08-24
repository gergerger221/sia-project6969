<?php
// backend/controllers/AdmissionController.php
namespace App\Controllers;

use App\Config\Database;
use App\Config\Response;
use App\Helpers\Auth;
use App\Helpers\FileUpload;
use PDO;

class AdmissionController {
    /**
     * Get applicant's admission profile, application form, and uploaded documents.
     */
    public function getMyApplication(): void {
        $user = Auth::requireAuth();
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT a.*, 
                   gl.name as grade_level_name, gl.category as grade_category,
                   t.name as track_name, t.code as track_code,
                   s.name as strand_name, s.code as strand_code,
                   sy.name as school_year_name, sy.active_semester
            FROM admission_applications a
            LEFT JOIN grade_levels gl ON a.grade_level_id = gl.id
            LEFT JOIN tracks t ON a.track_id = t.id
            LEFT JOIN strands s ON a.strand_id = s.id
            JOIN school_years sy ON a.school_year_id = sy.id
            WHERE a.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $user['id']]);
        $application = $stmt->fetch();

        if (!$application) {
            Response::error('Admission application record not found.', 404);
        }

        // Fetch uploaded documents
        $docStmt = $db->prepare("
            SELECT id, document_type, file_path, original_filename, file_size, status, verification_notes, uploaded_at
            FROM admission_documents
            WHERE application_id = :app_id
            ORDER BY id ASC
        ");
        $docStmt->execute(['app_id' => $application['id']]);
        $application['documents'] = $docStmt->fetchAll();

        // Check if queued or enrolled
        $queueStmt = $db->prepare("
            SELECT q.*, sec.name as section_name, sec.room
            FROM enrollment_queues q
            LEFT JOIN sections sec ON q.assigned_section_id = sec.id
            WHERE q.application_id = :app_id
            LIMIT 1
        ");
        $queueStmt->execute(['app_id' => $application['id']]);
        $application['queue_info'] = $queueStmt->fetch() ?: null;

        // Check if assessment created
        $assStmt = $db->prepare("
            SELECT sa.*, e.enrollment_no, e.student_no, e.status as enrollment_status, e.student_id as official_student_id,
                   u.student_id as student_number, u.username as student_username
            FROM enrollments e
            LEFT JOIN student_assessments sa ON e.id = sa.enrollment_id
            LEFT JOIN users u ON e.student_id = u.id
            WHERE e.application_id = :app_id
            LIMIT 1
        ");
        $assStmt->execute(['app_id' => $application['id']]);
        $application['assessment_info'] = $assStmt->fetch() ?: null;

        // Fetch latest online payment submission if any
        $opsStmt = $db->prepare("
            SELECT * FROM online_payment_submissions
            WHERE application_id = :app_id
            ORDER BY id DESC LIMIT 1
        ");
        $opsStmt->execute(['app_id' => $application['id']]);
        $application['online_payment_submission'] = $opsStmt->fetch() ?: null;

        Response::success('Application retrieved successfully', $application);
    }

    /**
     * Update applicant's multi-step admission information.
     */
    public function updateApplication(): void {
        $user = Auth::requireAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $db = Database::getConnection();
        $appStmt = $db->prepare("SELECT id, status FROM admission_applications WHERE user_id = :user_id LIMIT 1");
        $appStmt->execute(['user_id' => $user['id']]);
        $app = $appStmt->fetch();

        if (!$app) {
            Response::error('Application not found.');
        }

        if (in_array($app['status'], ['Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled'])) {
            Response::error('Your application has already been processed and cannot be edited directly.');
        }

        // Strict Mobile number validation
        $rawContact = trim($input['contact_number'] ?? '');
        $cleanContact = preg_replace('/\D/', '', $rawContact);
        if (!preg_match('/^09\d{9}$/', $cleanContact)) {
            Response::error('Must be an 11-digit Philippine mobile number starting with 09.');
        }

        // Guardian mobile number validation if provided
        $rawGuardianContact = trim($input['guardian_contact'] ?? '');
        if (!empty($rawGuardianContact)) {
            $cleanGuardianContact = preg_replace('/\D/', '', $rawGuardianContact);
            if (!preg_match('/^09\d{9}$/', $cleanGuardianContact)) {
                Response::error('Guardian contact must be an 11-digit Philippine mobile number starting with 09.');
            }
        } else {
            $cleanGuardianContact = $cleanContact;
        }

        // Global LRN Normalization and Uniqueness Check
        $rawLrn = trim($input['lrn'] ?? '');
        $cleanLrn = preg_replace('/\D/', '', $rawLrn);
        if (!empty($cleanLrn)) {
            if (strlen($cleanLrn) !== 12) {
                Response::error('DepEd Learner Reference Number (LRN) must be exactly 12 numeric digits.');
            }
            // Check global uniqueness across all other applications and enrollments
            $chkLrn = $db->prepare("
                SELECT id FROM admission_applications WHERE lrn = :lrn1 AND id != :curr_app_id
                UNION
                SELECT id FROM enrollments WHERE lrn = :lrn2 AND application_id != :curr_app_id2
                LIMIT 1
            ");
            $chkLrn->execute([
                'lrn1'          => $cleanLrn,
                'curr_app_id'   => $app['id'],
                'lrn2'          => $cleanLrn,
                'curr_app_id2'  => $app['id']
            ]);
            if ($chkLrn->fetch()) {
                Response::error('This LRN is already registered in the system. Please verify your LRN.');
            }
        }

        $fields = [
            'applicant_type'        => in_array($input['applicant_type'] ?? '', ['New Student', 'Transferee']) ? $input['applicant_type'] : 'New Student',
            'lrn'                   => $cleanLrn,
            'first_name'            => trim($input['first_name'] ?? ''),
            'middle_name'           => trim($input['middle_name'] ?? ''),
            'last_name'             => trim($input['last_name'] ?? ''),
            'suffix'                => trim($input['suffix'] ?? ''),
            'gender'                => $input['gender'] ?? 'Male',
            'birthdate'             => $input['birthdate'] ?? '2010-01-01',
            'birthplace'            => trim($input['birthplace'] ?? ''),
            'civil_status'          => $input['civil_status'] ?? 'Single',
            'nationality'           => $input['nationality'] ?? 'Filipino',
            'religion'              => trim($input['religion'] ?? ''),
            'contact_number'        => $cleanContact,
            'email'                 => $user['email'], // Locked to the user's login account email
            'address_street'        => trim($input['address_street'] ?? ''),
            'address_barangay'      => trim($input['address_barangay'] ?? ''),
            'address_city'          => trim($input['address_city'] ?? ''),
            'address_province'      => trim($input['address_province'] ?? ''),
            'address_zip'           => trim($input['address_zip'] ?? ''),
            'guardian_name'         => trim($input['guardian_name'] ?? ''),
            'guardian_relationship' => trim($input['guardian_relationship'] ?? ''),
            'guardian_contact'      => $cleanGuardianContact,
            'guardian_occupation'   => trim($input['guardian_occupation'] ?? ''),
            'last_school_attended'  => trim($input['last_school_attended'] ?? ''),
            'last_school_type'      => $input['last_school_type'] ?? 'Public',
            'last_school_year'      => trim($input['last_school_year'] ?? ''),
            'last_grade_completed'  => trim($input['last_grade_completed'] ?? ''),
            'grade_level_id'        => !empty($input['grade_level_id']) ? (int)$input['grade_level_id'] : null,
            'track_id'              => !empty($input['track_id']) ? (int)$input['track_id'] : null,
            'strand_id'             => !empty($input['strand_id']) ? (int)$input['strand_id'] : null,
            'voucher_status'        => $input['voucher_status'] ?? 'None',
            'id'                    => (int)$app['id']
        ];

        // Minimum age validation (High school applicants must be at least 11 years old)
        if (!empty($fields['birthdate'])) {
            try {
                $bdate = new \DateTime($fields['birthdate']);
                $now = new \DateTime();
                $age = $now->diff($bdate)->y;
                if ($age < 11) {
                    Response::error('Applicant must be at least 11 years old for high school admission.');
                }
            } catch (\Exception $e) {
                Response::error('Invalid birthdate format.');
            }
        }

        $updateQuery = "
            UPDATE admission_applications SET
                applicant_type = :applicant_type, lrn = :lrn, first_name = :first_name, middle_name = :middle_name,
                last_name = :last_name, suffix = :suffix, gender = :gender, birthdate = :birthdate,
                birthplace = :birthplace, civil_status = :civil_status, nationality = :nationality,
                religion = :religion, contact_number = :contact_number, email = :email,
                address_street = :address_street, address_barangay = :address_barangay, address_city = :address_city,
                address_province = :address_province, address_zip = :address_zip, guardian_name = :guardian_name,
                guardian_relationship = :guardian_relationship, guardian_contact = :guardian_contact,
                guardian_occupation = :guardian_occupation, last_school_attended = :last_school_attended,
                last_school_type = :last_school_type, last_school_year = :last_school_year,
                last_grade_completed = :last_grade_completed, grade_level_id = :grade_level_id,
                track_id = :track_id, strand_id = :strand_id, voucher_status = :voucher_status
            WHERE id = :id
        ";

        $stmt = $db->prepare($updateQuery);
        $stmt->execute($fields);

        // Also update user_profile
        $profStmt = $db->prepare("
            UPDATE user_profiles SET
                first_name = :first_name, middle_name = :middle_name, last_name = :last_name,
                suffix = :suffix, gender = :gender, birthdate = :birthdate, contact_number = :contact_number,
                address = :address
            WHERE user_id = :user_id
        ");
        $profStmt->execute([
            'first_name'     => $fields['first_name'],
            'middle_name'    => $fields['middle_name'],
            'last_name'      => $fields['last_name'],
            'suffix'         => $fields['suffix'],
            'gender'         => $fields['gender'],
            'birthdate'      => $fields['birthdate'],
            'contact_number' => $fields['contact_number'],
            'address'        => "{$fields['address_barangay']}, {$fields['address_city']}, {$fields['address_province']}",
            'user_id'        => $user['id']
        ]);

        Response::success('Admission application saved successfully');
    }

    /**
     * Upload an admission requirement file (PSA, SF9, Good Moral, 2x2 Photo, Voucher).
     */
    public function uploadDocument(): void {
        $user = Auth::requireAuth();
        $documentType = $_POST['document_type'] ?? '';

        if (!$documentType || !isset($_FILES['file'])) {
            Response::error('Document type and file are required.');
        }

        $db = Database::getConnection();
        $appStmt = $db->prepare("SELECT id, status FROM admission_applications WHERE user_id = :user_id LIMIT 1");
        $appStmt->execute(['user_id' => $user['id']]);
        $app = $appStmt->fetch();

        if (!$app) {
            Response::error('Application not found.');
        }

        $uploaded = FileUpload::upload($_FILES['file']);

        // Check if this document type was already uploaded; update if so, else insert
        $checkDoc = $db->prepare("SELECT id, status FROM admission_documents WHERE application_id = :app_id AND document_type = :doc_type");
        $checkDoc->execute(['app_id' => $app['id'], 'doc_type' => $documentType]);
        $existing = $checkDoc->fetch();

        if ($existing) {
            if ($existing['status'] === 'Verified') {
                Response::error('This document has already been verified by the Registrar and cannot be replaced.');
            }
            $docStmt = $db->prepare("
                UPDATE admission_documents SET
                    file_path = :file_path, original_filename = :orig_name, file_size = :file_size,
                    status = 'Pending', verification_notes = NULL, uploaded_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            $docStmt->execute([
                'file_path' => $uploaded['file_path'],
                'orig_name' => $uploaded['original_name'],
                'file_size' => $uploaded['file_size'],
                'id'        => $existing['id']
            ]);
            $docId = $existing['id'];
        } else {
            $docStmt = $db->prepare("
                INSERT INTO admission_documents (application_id, document_type, file_path, original_filename, file_size, status)
                VALUES (:app_id, :doc_type, :file_path, :orig_name, :file_size, 'Pending')
            ");
            $docStmt->execute([
                'app_id'    => $app['id'],
                'doc_type'  => $documentType,
                'file_path' => $uploaded['file_path'],
                'orig_name' => $uploaded['original_name'],
                'file_size' => $uploaded['file_size']
            ]);
            $docId = $db->lastInsertId();
        }

        Auth::logAudit('DOCUMENT_UPLOAD', "Uploaded requirement {$documentType} for App #{$app['id']}", $user['id']);

        Response::success("{$documentType} uploaded successfully", [
            'document_id' => $docId,
            'file_path'   => $uploaded['file_path'],
            'status'      => 'Pending'
        ]);
    }

    /**
     * Submit completed application for Registrar review.
     */
    public function submitApplication(): void {
        $user = Auth::requireAuth();
        $db = Database::getConnection();

        $appStmt = $db->prepare("
            SELECT a.*, gl.category as grade_category
            FROM admission_applications a
            LEFT JOIN grade_levels gl ON a.grade_level_id = gl.id
            WHERE a.user_id = :user_id 
            LIMIT 1
        ");
        $appStmt->execute(['user_id' => $user['id']]);
        $app = $appStmt->fetch();

        if (!$app) {
            Response::error('Application not found.');
        }

        $isSHS = ($app['grade_category'] === 'SHS' || (int)$app['grade_level_id'] >= 5);
        $isTransferee = ($app['applicant_type'] === 'Transferee');
        $isVoucher = $isSHS && !empty($app['voucher_status']) && $app['voucher_status'] !== 'None';

        // Base requirements for all applicants
        $mandatoryDocs = [
            'PSA Birth Certificate',
            'SF9 / Form 138 (Report Card)',
            'Certificate of Good Moral Character',
            '2x2 ID Picture'
        ];

        // Transferees require Transfer Credential / Honorable Dismissal
        if ($isTransferee) {
            $mandatoryDocs[] = 'Certificate of Transfer Credential / Honorable Dismissal';
        }

        // SHS Non-Transferee JHS Completers require JHS Completion Certificate
        if ($isSHS && !$isTransferee) {
            $mandatoryDocs[] = 'Certificate of JHS Completion';
        }

        // SHS Voucher Grantees require Voucher Certificate (never required for JHS or Non-Voucher SHS)
        if ($isVoucher) {
            $mandatoryDocs[] = 'ESC Certificate / Voucher Cert';
        }

        $uploadedStmt = $db->prepare("
            SELECT DISTINCT document_type 
            FROM admission_documents 
            WHERE application_id = :app_id AND status != 'Rejected' AND status != 'Deficient'
        ");
        $uploadedStmt->execute(['app_id' => $app['id']]);
        $uploadedTypes = $uploadedStmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];

        $missing = array_diff($mandatoryDocs, $uploadedTypes);
        if (!empty($missing)) {
            Response::error('All mandatory admission requirements must be uploaded before submission. Missing: ' . implode(', ', $missing));
        }

        $update = $db->prepare("UPDATE admission_applications SET status = 'Under Review' WHERE id = :id");
        $update->execute(['id' => $app['id']]);

        Auth::logAudit('APPLICATION_SUBMIT', "Application #{$app['id']} submitted for evaluation", $user['id']);

        Response::success('Application submitted successfully! Our Registrar team is now evaluating your documents.');
    }



    /**
     * Delete an uploaded requirement document.
     */
    public function deleteDocument(): void {
        $user = Auth::requireAuth();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $docId = (int)($input['document_id'] ?? 0);

        if (!$docId) {
            Response::error('Document ID is required.');
        }

        $db = Database::getConnection();

        // 1. Verify application ownership
        $appStmt = $db->prepare("SELECT id, status FROM admission_applications WHERE user_id = :user_id LIMIT 1");
        $appStmt->execute(['user_id' => $user['id']]);
        $app = $appStmt->fetch();

        if (!$app) {
            Response::error('Application not found.');
        }

        if (in_array($app['status'], ['Approved', 'Queued for Enrollment', 'Assessed', 'Enrolled'])) {
            Response::error('Cannot delete documents because your application has already been processed.');
        }

        // 2. Fetch document record
        $docStmt = $db->prepare("SELECT id, file_path, document_type, status FROM admission_documents WHERE id = :id AND application_id = :app_id LIMIT 1");
        $docStmt->execute(['id' => $docId, 'app_id' => $app['id']]);
        $doc = $docStmt->fetch();

        if (!$doc) {
            Response::error('Document not found or does not belong to your application.');
        }

        if ($doc['status'] === 'Verified') {
            Response::error('This requirement has already been verified and accepted by the Registrar and cannot be removed.');
        }

        // 3. Delete physical file from uploads folder if exists
        $uploadBasePath = realpath(__DIR__ . '/../');
        $fullFilePath = $uploadBasePath . '/' . $doc['file_path'];
        if (file_exists($fullFilePath) && is_file($fullFilePath)) {
            unlink($fullFilePath);
        }

        // 4. Delete row from database
        $delStmt = $db->prepare("DELETE FROM admission_documents WHERE id = :id");
        $delStmt->execute(['id' => $docId]);

        // 5. If application was Under Review, revert status to Draft so user must upload replacement and re-submit
        if ($app['status'] === 'Under Review') {
            $revStmt = $db->prepare("UPDATE admission_applications SET status = 'Draft' WHERE id = :id");
            $revStmt->execute(['id' => $app['id']]);
        }

        Auth::logAudit('DOCUMENT_DELETED', "Deleted requirement {$doc['document_type']} (ID: {$docId}) for App #{$app['id']}", $user['id']);

        Response::success("{$doc['document_type']} removed successfully.");
    }

    /**
     * Get academic options (Grade levels, Tracks, Strands, and Subjects).
     */
    public function getAcademicOptions(): void {
        $db = Database::getConnection();

        $gradeLevels = $db->query("SELECT * FROM grade_levels ORDER BY sequence_order ASC")->fetchAll();
        $tracks = $db->query("SELECT * FROM tracks WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        $strands = $db->query("
            SELECT s.*, t.name as track_name, t.code as track_code 
            FROM strands s
            JOIN tracks t ON s.track_id = t.id
            WHERE s.status = 'Active' AND s.is_active = 1
            ORDER BY s.track_id ASC, s.id ASC
        ")->fetchAll();

        $subjects = $db->query("
            SELECT sub.*, gl.name as grade_level_name, gl.category as grade_category,
                   s.name as strand_name, s.code as strand_code,
                   pre.code as prerequisite_code, pre.title as prerequisite_title
            FROM subjects sub
            JOIN grade_levels gl ON sub.grade_level_id = gl.id
            LEFT JOIN strands s ON sub.strand_id = s.id
            LEFT JOIN subjects pre ON sub.prerequisite_id = pre.id
            WHERE sub.is_active = 1
            ORDER BY sub.grade_level_id ASC, sub.strand_id ASC, sub.semester ASC, sub.code ASC
        ")->fetchAll();

        $schoolYear = $db->query("SELECT * FROM school_years WHERE is_active = 1 LIMIT 1")->fetch();

        Response::success('Academic options retrieved', [
            'grade_levels' => $gradeLevels,
            'tracks'       => $tracks,
            'strands'      => $strands,
            'subjects'     => $subjects,
            'school_year'  => $schoolYear
        ]);
    }

    /**
     * Checkout / Pay Tuition Downpayment via PayMongo Online Simulation or Cashier Walk-in Ticket.
     */
    public function checkoutPayment(): void {
        $user = Auth::requireRole(['applicant', 'student']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $db = Database::getConnection();

        // 1. Fetch user's admission application
        $appStmt = $db->prepare("SELECT * FROM admission_applications WHERE user_id = :user_id LIMIT 1");
        $appStmt->execute(['user_id' => $user['id']]);
        $app = $appStmt->fetch();

        if (!$app) {
            Response::error('Admission application not found.', 404);
        }

        // STRICT ENROLLMENT WORKFLOW: REQUIREMENTS MUST BE APPROVED BEFORE PAYMENT
        if (!in_array($app['status'], ['Approved', 'Queued for Enrollment', 'Assessed'])) {
            Response::error('Payment is not available yet. Your application must first be approved by the Registrar.', 403);
        }

        // Guard 1: Officially Enrolled
        if ($app['status'] === 'Enrolled') {
            Response::error('You are already officially enrolled. No additional payment is required.', 422);
        }

        // 2. Fetch associated enrollment & assessment
        $enrStmt = $db->prepare("
            SELECT e.*, sa.id as assessment_id, sa.net_payable, sa.remaining_balance, sa.total_paid, sa.status as assessment_status, sa.minimum_downpayment
            FROM enrollments e
            LEFT JOIN student_assessments sa ON e.id = sa.enrollment_id
            WHERE e.application_id = :app_id
            LIMIT 1
        ");
        $enrStmt->execute(['app_id' => $app['id']]);
        $enr = $enrStmt->fetch();

        if (!$enr || empty($enr['assessment_id'])) {
            Response::error('Assessment record not found for your application. Please wait for Registrar processing.', 422);
        }

        // Guard 2: Online Payment Pending Verification
        $chkActiveSub = $db->prepare("
            SELECT id, reference_no, amount_submitted FROM online_payment_submissions 
            WHERE application_id = :app_id AND status = 'Pending Verification'
            LIMIT 1
        ");
        $chkActiveSub->execute(['app_id' => $app['id']]);
        $activeSub = $chkActiveSub->fetch();
        if ($activeSub) {
            Response::error("Your online payment (Ref: {$activeSub['reference_no']}) is already submitted and currently awaiting Treasury verification. You cannot submit another payment or switch payment methods while under review.", 422);
        }

        $paymentType = $input['payment_type'] ?? ($_POST['payment_type'] ?? 'online'); // 'online' or 'walkin'

        $db->beginTransaction();
        try {
            if ($paymentType === 'walkin') {
                $location = 'Main Cashier Office, Bldg A, 123 Education Blvd, U-Belt, Manila';
                $amountDue = (float)($input['amount'] ?? $_POST['amount'] ?? $enr['minimum_downpayment'] ?? 3000.00);
                $ticketNo = 'PAY-' . date('Y') . '-' . str_pad((string)((int)$app['id'] + 101), 4, '0', STR_PAD_LEFT);

                // Update application status
                $db->prepare("UPDATE admission_applications SET status = 'Walk-in Payment Scheduled' WHERE id = :id")->execute(['id' => $app['id']]);

                $db->commit();
                Auth::logAudit('WALKIN_TICKET_GENERATED', "Generated walk-in payment ticket {$ticketNo} for App #{$app['application_no']}", $user['id']);

                Response::success('Walk-in Cashier Payment Ticket generated successfully!', [
                    'payment_type'    => 'walkin',
                    'ticket_number'   => $ticketNo,
                    'walkin_ticket_no'=> $ticketNo,
                    'location'        => $location,
                    'amount_due'      => $amountDue,
                    'net_payable'     => (float)$enr['net_payable'],
                    'student_name'    => "{$app['first_name']} {$app['last_name']}",
                    'application_no'  => $app['application_no'],
                    'status'          => 'Walk-in Payment Scheduled / Awaiting Payment',
                    'instructions'    => 'Present your printed Blue Form at the Main Cashier Window.'
                ]);
                return;
            }

            // ONLINE PAYMENT (PayMongo Simulation with Reference Verification)
            $paymentChannel = trim($input['payment_channel'] ?? $_POST['payment_channel'] ?? 'GCash');
            $amountSubmitted = (float)($input['amount'] ?? $_POST['amount'] ?? 3000.00);
            $referenceNo = trim($input['reference_no'] ?? $_POST['reference_no'] ?? '');
            $accountName = trim($input['account_name'] ?? $_POST['account_name'] ?? "{$app['first_name']} {$app['last_name']}");
            $accountNumber = trim($input['account_number'] ?? $_POST['account_number'] ?? ($app['contact_number'] ?? ''));

            if (!$referenceNo) {
                $db->rollBack();
                Response::error('Payment Reference Number / Transaction ID is required to verify your payment.', 422);
            }

            if ($amountSubmitted <= 0) {
                $db->rollBack();
                Response::error('Please specify a valid payment amount.', 422);
            }

            // Payment Amount Limiter Validation (Minimum Downpayment & Maximum Outstanding Balance)
            $maxPayable = (float)($enr['remaining_balance'] > 0 ? $enr['remaining_balance'] : $enr['net_payable']);
            $minRequired = min((float)($enr['minimum_downpayment'] ?? 3000.00), $maxPayable);

            if ($amountSubmitted < $minRequired) {
                $db->rollBack();
                Response::error("Payment amount (₱" . number_format($amountSubmitted, 2) . ") is below the minimum required amount of ₱" . number_format($minRequired, 2) . ".", 422);
            }

            if ($maxPayable > 0 && $amountSubmitted > $maxPayable) {
                $db->rollBack();
                Response::error("Payment amount (₱" . number_format($amountSubmitted, 2) . ") exceeds the maximum settle limit / remaining balance of ₱" . number_format($maxPayable, 2) . ". You cannot overpay your assessment.", 422);
            }

            // Prevent duplicate reference numbers across existing verified payments and active submissions
            $chkDupPay = $db->prepare("SELECT id FROM payments WHERE reference_no = :ref LIMIT 1");
            $chkDupPay->execute(['ref' => $referenceNo]);
            if ($chkDupPay->fetch()) {
                $db->rollBack();
                Response::error("Payment Reference Number '{$referenceNo}' has already been verified and processed.", 422);
            }

            $chkDupSub = $db->prepare("
                SELECT id, application_id FROM online_payment_submissions 
                WHERE reference_no = :ref AND status != 'Rejected' AND application_id != :app_id 
                LIMIT 1
            ");
            $chkDupSub->execute(['ref' => $referenceNo, 'app_id' => $app['id']]);
            if ($chkDupSub->fetch()) {
                $db->rollBack();
                Response::error("Payment Reference Number '{$referenceNo}' has already been submitted by another enrollee. Please check your transaction receipt.", 422);
            }

            // Handle optional receipt screenshot/PDF file upload
            $receiptFilePath = null;
            $receiptOriginalName = null;
            if (isset($_FILES['receipt']) || isset($_FILES['file'])) {
                $file = $_FILES['receipt'] ?? $_FILES['file'];
                if ($file['size'] > 0) {
                    $uploadedReceipt = FileUpload::upload($file);
                    $receiptFilePath = $uploadedReceipt['file_path'];
                    $receiptOriginalName = $uploadedReceipt['original_name'];
                }
            }

            // Check if there is an existing submission for this application
            $chkExisting = $db->prepare("
                SELECT id FROM online_payment_submissions 
                WHERE application_id = :app_id 
                ORDER BY id DESC LIMIT 1
            ");
            $chkExisting->execute(['app_id' => $app['id']]);
            $existingSub = $chkExisting->fetch();

            if ($existingSub) {
                // Update submission
                $upSub = $db->prepare("
                    UPDATE online_payment_submissions SET
                        assessment_id = :ass_id,
                        enrollment_id = :enr_id,
                        payment_channel = :channel,
                        amount_paid = :amount,
                        amount_submitted = :amount2,
                        payment_date = CURRENT_DATE,
                        reference_no = :ref,
                        account_name = :acc_name,
                        account_number = :acc_no,
                        receipt_file_path = COALESCE(:rec_path, receipt_file_path),
                        receipt_original_name = COALESCE(:rec_name, receipt_original_name),
                        status = 'Pending Verification',
                        rejection_reason = NULL,
                        created_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ");
                $upSub->execute([
                    'ass_id'   => $enr['assessment_id'],
                    'enr_id'   => $enr['id'],
                    'channel'  => $paymentChannel,
                    'amount'   => $amountSubmitted,
                    'amount2'  => $amountSubmitted,
                    'ref'      => $referenceNo,
                    'acc_name' => $accountName,
                    'acc_no'   => $accountNumber,
                    'rec_path' => $receiptFilePath,
                    'rec_name' => $receiptOriginalName,
                    'id'       => $existingSub['id']
                ]);
                $submissionId = $existingSub['id'];
            } else {
                // Insert new submission
                $insSub = $db->prepare("
                    INSERT INTO online_payment_submissions (
                        assessment_id, enrollment_id, application_id, payment_channel,
                        amount_paid, amount_submitted, payment_date, reference_no, account_name, account_number,
                        receipt_file_path, receipt_original_name, status
                    ) VALUES (
                        :ass_id, :enr_id, :app_id, :channel,
                        :amount, :amount2, CURRENT_DATE, :ref, :acc_name, :acc_no,
                        :rec_path, :rec_name, 'Pending Verification'
                    )
                ");
                $insSub->execute([
                    'ass_id'   => $enr['assessment_id'],
                    'enr_id'   => $enr['id'],
                    'app_id'   => $app['id'],
                    'channel'  => $paymentChannel,
                    'amount'   => $amountSubmitted,
                    'amount2'  => $amountSubmitted,
                    'ref'      => $referenceNo,
                    'acc_name' => $accountName,
                    'acc_no'   => $accountNumber,
                    'rec_path' => $receiptFilePath,
                    'rec_name' => $receiptOriginalName
                ]);
                $submissionId = $db->lastInsertId();
            }

            // Update Application status to Awaiting Verification
            $db->prepare("UPDATE admission_applications SET status = 'Payment Submitted – Awaiting Verification' WHERE id = :id")->execute(['id' => $app['id']]);

            $db->commit();
            Auth::logAudit('ONLINE_PAYMENT_SUBMITTED', "Submitted online payment of ₱{$amountSubmitted} (Ref: {$referenceNo}) for App #{$app['application_no']}", $user['id']);

            Response::success('Payment details submitted successfully! Your transaction is now awaiting Treasury verification.', [
                'submission_id'   => $submissionId,
                'payment_type'    => 'online',
                'payment_channel' => $paymentChannel,
                'reference_no'    => $referenceNo,
                'amount_submitted'=> $amountSubmitted,
                'status'          => 'Payment Submitted – Awaiting Verification',
                'notice'          => 'Your payment has been logged. Our Treasury team will verify the transaction before releasing your Official Student ID and COR.'
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            Response::error('Payment submission failed: ' . $e->getMessage());
        }
    }

    /**
     * Switch payment mode between Online PayMongo and Walk-in Cashier (Allowed ONLY before payment submitted or after rejection)
     */
    public function switchPaymentMode(): void {
        $user = Auth::requireRole(['applicant', 'student']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $targetMode = trim($input['payment_mode'] ?? 'online'); // 'online' or 'walkin'

        $db = Database::getConnection();
        $appStmt = $db->prepare("SELECT * FROM admission_applications WHERE user_id = :user_id LIMIT 1");
        $appStmt->execute(['user_id' => $user['id']]);
        $app = $appStmt->fetch();

        if (!$app) {
            Response::error('Application not found.', 404);
        }

        if ($app['status'] === 'Enrolled') {
            Response::error('You are already officially enrolled. No payment method change is allowed.', 422);
        }

        // Check if there is an active pending online payment
        $chkActiveSub = $db->prepare("
            SELECT id, reference_no FROM online_payment_submissions 
            WHERE application_id = :app_id AND status = 'Pending Verification'
            LIMIT 1
        ");
        $chkActiveSub->execute(['app_id' => $app['id']]);
        if ($chkActiveSub->fetch()) {
            Response::error('You cannot switch payment methods while your online payment is awaiting Treasury verification.', 422);
        }

        $db->beginTransaction();
        try {
            if ($targetMode === 'online') {
                $db->prepare("
                    UPDATE student_assessments sa
                    JOIN enrollments e ON sa.enrollment_id = e.id
                    SET sa.payment_mode = 'Online PayMongo',
                        sa.status = CASE WHEN sa.status = 'Walk-in Payment Scheduled' THEN 'Unpaid' ELSE sa.status END
                    WHERE e.application_id = :app_id
                ")->execute(['app_id' => $app['id']]);

                if ($app['status'] === 'Walk-in Payment Scheduled') {
                    $db->prepare("UPDATE admission_applications SET status = 'Approved' WHERE id = :id")->execute(['id' => $app['id']]);
                }
            } else {
                $db->prepare("
                    UPDATE student_assessments sa
                    JOIN enrollments e ON sa.enrollment_id = e.id
                    SET sa.payment_mode = 'Walk-in Cashier'
                    WHERE e.application_id = :app_id
                ")->execute(['app_id' => $app['id']]);
            }

            $db->commit();
            Auth::logAudit('PAYMENT_MODE_SWITCHED', "Switched payment mode to {$targetMode} for App #{$app['application_no']}", $user['id']);

            Response::success("Payment mode switched to {$targetMode} successfully.", [
                'payment_mode' => $targetMode
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            Response::error('Failed to switch payment mode: ' . $e->getMessage());
        }
    }
}

