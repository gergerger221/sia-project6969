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

        $fields = [
            'applicant_type'        => in_array($input['applicant_type'] ?? '', ['New Student', 'Transferee']) ? $input['applicant_type'] : 'New Student',
            'lrn'                   => trim($input['lrn'] ?? ''),
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
            'contact_number'        => trim($input['contact_number'] ?? ''),
            'email'                 => $user['email'], // Locked to the user's login account email
            'address_street'        => trim($input['address_street'] ?? ''),
            'address_barangay'      => trim($input['address_barangay'] ?? ''),
            'address_city'          => trim($input['address_city'] ?? ''),
            'address_province'      => trim($input['address_province'] ?? ''),
            'address_zip'           => trim($input['address_zip'] ?? ''),
            'guardian_name'         => trim($input['guardian_name'] ?? ''),
            'guardian_relationship' => trim($input['guardian_relationship'] ?? ''),
            'guardian_contact'      => trim($input['guardian_contact'] ?? ''),
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
}

