<?php
// backend/controllers/CoordinatorController.php
namespace App\Controllers;

use App\Config\Database;
use App\Config\Response;
use App\Helpers\Auth;
use PDO;

class CoordinatorController {
    /**
     * Get complete curriculum overview: Tracks, Strands, Grade Levels, and Subjects.
     */
    public function getCurriculum(): void {
        Auth::requireRole(['coordinator', 'admin', 'registrar']);
        $db = Database::getConnection();

        $tracks = $db->query("SELECT * FROM tracks ORDER BY id ASC")->fetchAll();
        $strands = $db->query("
            SELECT s.*, t.name as track_name, t.code as track_code,
                   (SELECT COUNT(*) FROM subjects sub WHERE sub.strand_id = s.id AND sub.is_active = 1) as curriculum_subjects_count,
                   (SELECT COUNT(*) FROM sections sec WHERE sec.strand_id = s.id AND sec.is_active = 1) as active_sections_count,
                   (SELECT COALESCE(SUM(sec.current_enrolled), 0) FROM sections sec WHERE sec.strand_id = s.id AND sec.is_active = 1) as enrolled_students_count
            FROM strands s
            JOIN tracks t ON s.track_id = t.id
            ORDER BY s.track_id ASC, s.id ASC
        ")->fetchAll();
        
        $gradeLevels = $db->query("SELECT * FROM grade_levels ORDER BY sequence_order ASC")->fetchAll();
        
        $subjects = $db->query("
            SELECT sub.*, gl.name as grade_level_name, gl.category as grade_category,
                   s.name as strand_name, s.code as strand_code,
                   pre.code as prerequisite_code, pre.title as prerequisite_title
            FROM subjects sub
            JOIN grade_levels gl ON sub.grade_level_id = gl.id
            LEFT JOIN strands s ON sub.strand_id = s.id
            LEFT JOIN subjects pre ON sub.prerequisite_id = pre.id
            ORDER BY sub.grade_level_id ASC, sub.strand_id ASC, sub.code ASC
        ")->fetchAll();

        Response::success('Curriculum details loaded', [
            'tracks'       => $tracks,
            'strands'      => $strands,
            'grade_levels' => $gradeLevels,
            'subjects'     => $subjects
        ]);
    }

    /**
     * Add or update a Subject in the curriculum.
     */
    public function saveSubject(): void {
        $user = Auth::requireRole(['coordinator', 'admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = !empty($input['id']) ? (int)$input['id'] : null;
        $code = trim($input['code'] ?? '');
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $category = $input['category'] ?? 'JHS Core';
        $gradeLevelId = (int)($input['grade_level_id'] ?? 1);
        $strandId = !empty($input['strand_id']) ? (int)$input['strand_id'] : null;
        $semester = $input['semester'] ?? 'Full Year';
        $lectureHours = (float)($input['lecture_hours'] ?? 4.0);
        $labHours = (float)($input['lab_hours'] ?? 0.0);
        $units = (float)($input['units'] ?? 1.0);
        $prereqId = !empty($input['prerequisite_id']) ? (int)$input['prerequisite_id'] : null;

        if (!$code || !$title) {
            Response::error('Subject code and title are required.');
        }

        $db = Database::getConnection();

        if ($id) {
            $stmt = $db->prepare("
                UPDATE subjects SET
                    code = :code, title = :title, description = :description, category = :category,
                    grade_level_id = :gl_id, strand_id = :strand_id, semester = :semester,
                    lecture_hours = :lec, lab_hours = :lab, units = :units, prerequisite_id = :prereq
                WHERE id = :id
            ");
            $stmt->execute([
                'code'        => $code,
                'title'       => $title,
                'description' => $description,
                'category'    => $category,
                'gl_id'       => $gradeLevelId,
                'strand_id'   => $strandId,
                'semester'    => $semester,
                'lec'         => $lectureHours,
                'lab'         => $labHours,
                'units'       => $units,
                'prereq'      => $prereqId,
                'id'          => $id
            ]);
            Auth::logAudit('SUBJECT_UPDATED', "Updated subject {$code}", $user['id']);
            Response::success('Subject updated successfully');
        } else {
            $stmt = $db->prepare("
                INSERT INTO subjects (code, title, description, category, grade_level_id, strand_id, semester, lecture_hours, lab_hours, units, prerequisite_id)
                VALUES (:code, :title, :description, :category, :gl_id, :strand_id, :semester, :lec, :lab, :units, :prereq)
            ");
            $stmt->execute([
                'code'        => $code,
                'title'       => $title,
                'description' => $description,
                'category'    => $category,
                'gl_id'       => $gradeLevelId,
                'strand_id'   => $strandId,
                'semester'    => $semester,
                'lec'         => $lectureHours,
                'lab'         => $labHours,
                'units'       => $units,
                'prereq'      => $prereqId
            ]);
            Auth::logAudit('SUBJECT_CREATED', "Created subject {$code}", $user['id']);
            Response::success('Subject added to curriculum successfully', ['id' => $db->lastInsertId()], 201);
        }
    }

    /**
     * Delete or Archive a Subject.
     */
    public function deleteSubject(): void {
        $user = Auth::requireRole(['coordinator', 'admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = (int)($input['id'] ?? 0);
        if (!$id) {
            Response::error('Valid subject ID is required.');
        }

        $db = Database::getConnection();

        // 1. Check if subject is referenced in enrollment_subjects or student_grades
        $chk1 = $db->prepare("SELECT COUNT(*) FROM enrollment_subjects WHERE subject_id = :id");
        $chk1->execute(['id' => $id]);
        $enrolledCount = (int)$chk1->fetchColumn();

        $chk2 = $db->prepare("SELECT COUNT(*) FROM student_grades WHERE subject_id = :id");
        $chk2->execute(['id' => $id]);
        $gradeCount = (int)$chk2->fetchColumn();

        $sub = $db->query("SELECT * FROM subjects WHERE id = {$id}")->fetch();
        if (!$sub) {
            Response::error('Subject not found.');
        }

        if ($enrolledCount > 0 || $gradeCount > 0) {
            // Cannot hard delete because students have academic history; archive it safely
            $db->prepare("UPDATE subjects SET is_active = 0 WHERE id = :id")->execute(['id' => $id]);
            Auth::logAudit('SUBJECT_ARCHIVED', "Archived subject {$sub['code']} ({$sub['title']}) due to active enrollment/grade records", $user['id']);
            Response::success("Subject {$sub['code']} has active enrollments and has been safely archived (deactivated) to protect student academic records.", ['archived' => true]);
        } else {
            // Clear prerequisite references pointing to this subject
            $db->prepare("UPDATE subjects SET prerequisite_id = NULL WHERE prerequisite_id = :id")->execute(['id' => $id]);
            $db->prepare("DELETE FROM schedules WHERE subject_id = :id")->execute(['id' => $id]);
            $db->prepare("DELETE FROM subjects WHERE id = :id")->execute(['id' => $id]);

            Auth::logAudit('SUBJECT_DELETED', "Deleted subject {$sub['code']} ({$sub['title']})", $user['id']);
            Response::success("Subject {$sub['code']} deleted successfully from curriculum.", ['deleted' => true]);
        }
    }

    /**
     * Create or update an Academic Strand.
     */
    public function saveStrand(): void {
        $user = Auth::requireRole(['coordinator', 'admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = !empty($input['id']) ? (int)$input['id'] : null;
        $trackId = (int)($input['track_id'] ?? 1);
        $code = strtoupper(trim($input['code'] ?? ''));
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $status = in_array($input['status'] ?? '', ['Active', 'Deactivated', 'Archived']) ? $input['status'] : 'Active';
        $isActive = ($status === 'Active') ? 1 : 0;
        $archivedAt = ($status === 'Archived') ? date('Y-m-d H:i:s') : null;

        if (!$code || !$name) {
            Response::error('Strand code and name are required.');
        }

        $db = Database::getConnection();

        if ($id) {
            $stmt = $db->prepare("
                UPDATE strands SET
                    track_id = :track_id, code = :code, name = :name, description = :description,
                    is_active = :is_active, status = :status, archived_at = :archived_at
                WHERE id = :id
            ");
            $stmt->execute([
                'track_id'    => $trackId,
                'code'        => $code,
                'name'        => $name,
                'description' => $description,
                'is_active'   => $isActive,
                'status'      => $status,
                'archived_at' => $archivedAt,
                'id'          => $id
            ]);
            Auth::logAudit('STRAND_UPDATED', "Updated strand {$code} (Status: {$status})", $user['id']);
            Response::success('Strand updated successfully');
        } else {
            $stmt = $db->prepare("
                INSERT INTO strands (track_id, code, name, description, is_active, status, archived_at)
                VALUES (:track_id, :code, :name, :description, :is_active, :status, :archived_at)
            ");
            $stmt->execute([
                'track_id'    => $trackId,
                'code'        => $code,
                'name'        => $name,
                'description' => $description,
                'is_active'   => $isActive,
                'status'      => $status,
                'archived_at' => $archivedAt
            ]);
            Auth::logAudit('STRAND_CREATED', "Created strand {$code}", $user['id']);
            Response::success('Strand added successfully', ['id' => $db->lastInsertId()], 201);
        }
    }

    /**
     * Toggle Strand status (Active, Deactivated, Archived).
     */
    public function toggleStrandStatus(): void {
        $user = Auth::requireRole(['coordinator', 'admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = (int)($input['id'] ?? 0);
        $status = $input['status'] ?? 'Active';

        if (!$id || !in_array($status, ['Active', 'Deactivated', 'Archived'])) {
            Response::error('Valid Strand ID and Status are required.');
        }

        $db = Database::getConnection();
        $isActive = ($status === 'Active') ? 1 : 0;
        $archivedAt = ($status === 'Archived') ? date('Y-m-d H:i:s') : null;

        $stmt = $db->prepare("
            UPDATE strands SET
                status = :status,
                is_active = :is_active,
                archived_at = :archived_at
            WHERE id = :id
        ");
        $stmt->execute([
            'status'      => $status,
            'is_active'   => $isActive,
            'archived_at' => $archivedAt,
            'id'          => $id
        ]);

        $st = $db->query("SELECT code FROM strands WHERE id = {$id}")->fetch();
        $code = $st ? $st['code'] : "ID #{$id}";

        Auth::logAudit('STRAND_STATUS_CHANGED', "Changed strand {$code} status to {$status}", $user['id']);
        Response::success("Strand {$code} status updated to {$status}.", ['status' => $status]);
    }

    /**
     * Delete or Remove a Strand (Guarded against active data references).
     */
    public function deleteStrand(): void {
        $user = Auth::requireRole(['coordinator', 'admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = (int)($input['id'] ?? 0);
        if (!$id) {
            Response::error('Valid Strand ID is required.');
        }

        $db = Database::getConnection();

        // 1. Check references across applications, enrollments, sections, and subjects
        $chkApp = $db->prepare("SELECT COUNT(*) FROM admission_applications WHERE strand_id = :id");
        $chkApp->execute(['id' => $id]);
        $appCount = (int)$chkApp->fetchColumn();

        $chkEnr = $db->prepare("SELECT COUNT(*) FROM enrollments WHERE strand_id = :id");
        $chkEnr->execute(['id' => $id]);
        $enrCount = (int)$chkEnr->fetchColumn();

        $chkSec = $db->prepare("SELECT COUNT(*) FROM sections WHERE strand_id = :id");
        $chkSec->execute(['id' => $id]);
        $secCount = (int)$chkSec->fetchColumn();

        $chkSub = $db->prepare("SELECT COUNT(*) FROM subjects WHERE strand_id = :id");
        $chkSub->execute(['id' => $id]);
        $subCount = (int)$chkSub->fetchColumn();

        $st = $db->query("SELECT * FROM strands WHERE id = {$id}")->fetch();
        if (!$st) {
            Response::error('Strand not found.');
        }

        $totalRefs = $appCount + $enrCount + $secCount + $subCount;

        if ($totalRefs > 0) {
            Response::error("Cannot delete strand '{$st['code']}' because it is currently linked to {$enrCount} enrolled students, {$secCount} sections, {$subCount} curriculum courses, and {$appCount} applications. Please deactivate or archive the strand instead.", 400);
        } else {
            $db->prepare("DELETE FROM fee_structures WHERE strand_id = :id")->execute(['id' => $id]);
            $db->prepare("DELETE FROM strands WHERE id = :id")->execute(['id' => $id]);

            Auth::logAudit('STRAND_DELETED', "Deleted unreferenced strand {$st['code']} ({$st['name']})", $user['id']);
            Response::success("Strand '{$st['code']}' has been permanently deleted.", ['deleted' => true]);
        }
    }

    /**
     * Get Sections and Schedules.
     */
    public function getSections(): void {
        Auth::requireRole(['coordinator', 'admin', 'registrar']);
        $db = Database::getConnection();

        $sections = $db->query("
            SELECT sec.*, gl.name as grade_level_name, gl.category as grade_category,
                   s.name as strand_name, s.code as strand_code,
                   u.username as adviser_username, p.first_name as adviser_first, p.last_name as adviser_last
            FROM sections sec
            JOIN grade_levels gl ON sec.grade_level_id = gl.id
            LEFT JOIN strands s ON sec.strand_id = s.id
            LEFT JOIN users u ON sec.adviser_id = u.id
            LEFT JOIN user_profiles p ON u.id = p.user_id
            WHERE sec.is_active = 1
            ORDER BY sec.grade_level_id ASC, sec.name ASC
        ")->fetchAll();

        $teachers = $db->query("
            SELECT u.id, u.username, p.first_name, p.last_name, p.contact_number
            FROM users u
            JOIN roles r ON u.role_id = r.id
            JOIN user_profiles p ON u.id = p.user_id
            WHERE r.slug IN ('teacher', 'coordinator') AND u.status = 'Active'
        ")->fetchAll();

        Response::success('Sections loaded', [
            'sections' => $sections,
            'teachers' => $teachers
        ]);
    }

    /**
     * Create or edit a Section.
     */
    public function saveSection(): void {
        $user = Auth::requireRole(['coordinator', 'admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = !empty($input['id']) ? (int)$input['id'] : null;
        $name = trim($input['name'] ?? '');
        $gradeLevelId = (int)($input['grade_level_id'] ?? 1);
        $strandId = !empty($input['strand_id']) ? (int)$input['strand_id'] : null;
        $maxCapacity = (int)($input['max_capacity'] ?? 45);
        $room = trim($input['room'] ?? '');
        $adviserId = !empty($input['adviser_id']) ? (int)$input['adviser_id'] : null;

        if (!$name) {
            Response::error('Section name is required.');
        }

        $db = Database::getConnection();
        $sy = $db->query("SELECT id FROM school_years WHERE is_active = 1 LIMIT 1")->fetch();
        $syId = $sy ? (int)$sy['id'] : 1;

        if ($id) {
            $stmt = $db->prepare("
                UPDATE sections SET
                    name = :name, grade_level_id = :gl_id, strand_id = :strand_id,
                    max_capacity = :cap, room = :room, adviser_id = :adv
                WHERE id = :id
            ");
            $stmt->execute([
                'name'      => $name,
                'gl_id'     => $gradeLevelId,
                'strand_id' => $strandId,
                'cap'       => $maxCapacity,
                'room'      => $room,
                'adv'       => $adviserId,
                'id'        => $id
            ]);
            Auth::logAudit('SECTION_UPDATED', "Updated section {$name}", $user['id']);
            Response::success('Section updated successfully');
        } else {
            $stmt = $db->prepare("
                INSERT INTO sections (school_year_id, grade_level_id, strand_id, name, max_capacity, room, adviser_id)
                VALUES (:sy_id, :gl_id, :strand_id, :name, :cap, :room, :adv)
            ");
            $stmt->execute([
                'sy_id'     => $syId,
                'gl_id'     => $gradeLevelId,
                'strand_id' => $strandId,
                'name'      => $name,
                'cap'       => $maxCapacity,
                'room'      => $room,
                'adv'       => $adviserId
            ]);
            Auth::logAudit('SECTION_CREATED', "Created section {$name}", $user['id']);
            Response::success('Section created successfully', ['id' => $db->lastInsertId()], 201);
        }
    }

    /**
     * Get list of students enrolled in a specific section.
     */
    public function getSectionStudents(int $sectionId): void {
        Auth::requireRole(['coordinator', 'admin', 'registrar']);
        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT e.id as enrollment_id, e.student_id as user_id, e.student_no, e.enrollment_no, e.status as enrollment_status,
                   u.username, u.student_id as official_student_id,
                   p.first_name, p.middle_name, p.last_name, p.gender, p.contact_number,
                   a.lrn, a.application_no, a.voucher_status,
                   sec.name as section_name, sec.room as section_room,
                   gl.name as grade_level_name, s.code as strand_code
            FROM enrollments e
            JOIN users u ON e.student_id = u.id
            JOIN user_profiles p ON u.id = p.user_id
            JOIN sections sec ON e.section_id = sec.id
            JOIN grade_levels gl ON e.grade_level_id = gl.id
            LEFT JOIN strands s ON e.strand_id = s.id
            LEFT JOIN admission_applications a ON e.application_id = a.id
            WHERE e.section_id = :sec_id AND e.status = 'Officially Enrolled'
            ORDER BY p.last_name ASC, p.first_name ASC
        ");
        $stmt->execute(['sec_id' => $sectionId]);
        $students = $stmt->fetchAll();

        Response::success('Section students loaded', $students);
    }

    /**
     * Transfer an enrolled student to another eligible section.
     */
    public function transferStudentSection(): void {
        $user = Auth::requireRole(['coordinator', 'admin', 'registrar']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $enrollmentId = (int)($input['enrollment_id'] ?? 0);
        $studentUserId = (int)($input['student_id'] ?? 0);
        $targetSectionId = (int)($input['target_section_id'] ?? 0);
        $reason = trim($input['reason'] ?? 'Requested Section Transfer');

        if ((!$enrollmentId && !$studentUserId) || !$targetSectionId) {
            Response::error('Valid Enrollment/Student ID and Target Section ID are required.');
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Fetch current active enrollment
            if ($enrollmentId) {
                $enrStmt = $db->prepare("SELECT * FROM enrollments WHERE id = :id FOR UPDATE");
                $enrStmt->execute(['id' => $enrollmentId]);
            } else {
                $enrStmt = $db->prepare("SELECT * FROM enrollments WHERE student_id = :uid ORDER BY id DESC LIMIT 1 FOR UPDATE");
                $enrStmt->execute(['uid' => $studentUserId]);
            }
            $enr = $enrStmt->fetch();

            if (!$enr) {
                Response::error('Active enrollment record not found.');
            }

            $currentSectionId = (int)$enr['section_id'];
            if ($currentSectionId === $targetSectionId) {
                Response::error('Student is already in this section.');
            }

            // 2. Validate Target Section
            $secStmt = $db->prepare("SELECT * FROM sections WHERE id = :id FOR UPDATE");
            $secStmt->execute(['id' => $targetSectionId]);
            $targetSec = $secStmt->fetch();

            if (!$targetSec || !$targetSec['is_active']) {
                Response::error('Target section is invalid or inactive.');
            }

            // Check Grade Level match
            if ((int)$targetSec['grade_level_id'] !== (int)$enr['grade_level_id']) {
                Response::error('Target section does not belong to the student\'s grade level.');
            }

            // Check Strand match for SHS
            if (!empty($enr['strand_id']) && (int)$targetSec['strand_id'] !== (int)$enr['strand_id']) {
                Response::error('Target section does not match the student\'s academic strand.');
            }

            // Check Capacity
            if ((int)$targetSec['current_enrolled'] >= (int)$targetSec['max_capacity']) {
                Response::error("Target section '{$targetSec['name']}' is already at maximum capacity ({$targetSec['max_capacity']} students).");
            }

            // 3. Update Seat Capacities
            if ($currentSectionId) {
                $db->prepare("UPDATE sections SET current_enrolled = GREATEST(0, current_enrolled - 1) WHERE id = :id")
                   ->execute(['id' => $currentSectionId]);
            }
            $db->prepare("UPDATE sections SET current_enrolled = current_enrolled + 1 WHERE id = :id")
               ->execute(['id' => $targetSectionId]);

            // 4. Update Enrollment record
            $db->prepare("UPDATE enrollments SET section_id = :sec_id WHERE id = :id")
               ->execute(['sec_id' => $targetSectionId, 'id' => $enr['id']]);

            // 5. Update Student Records & Queue
            $db->prepare("UPDATE student_records SET section_id = :sec_id WHERE student_id = :uid AND school_year_id = :sy_id")
               ->execute(['sec_id' => $targetSectionId, 'uid' => $enr['student_id'], 'sy_id' => $enr['school_year_id']]);

            $db->prepare("UPDATE enrollment_queues SET assigned_section_id = :sec_id WHERE application_id = :app_id")
               ->execute(['sec_id' => $targetSectionId, 'app_id' => $enr['application_id']]);

            // 6. Re-link Subject Schedules to the new section
            $subStmt = $db->prepare("SELECT id, subject_id FROM enrollment_subjects WHERE enrollment_id = :enr_id");
            $subStmt->execute(['enr_id' => $enr['id']]);
            $enrolledSubs = $subStmt->fetchAll();

            $findSch = $db->prepare("SELECT id FROM schedules WHERE section_id = :sec_id AND subject_id = :sub_id LIMIT 1");
            $upSch = $db->prepare("UPDATE enrollment_subjects SET schedule_id = :sch_id WHERE id = :es_id");

            foreach ($enrolledSubs as $es) {
                $findSch->execute(['sec_id' => $targetSectionId, 'sub_id' => $es['subject_id']]);
                $schId = $findSch->fetchColumn();
                $upSch->execute(['sch_id' => $schId ?: null, 'es_id' => $es['id']]);
            }

            $db->commit();

            Auth::logAudit('SECTION_TRANSFERRED', "Transferred student User #{$enr['student_id']} from Section #{$currentSectionId} to Section #{$targetSectionId} ({$targetSec['name']}). Reason: {$reason}", $user['id']);

            Response::success("Student successfully transferred to {$targetSec['name']}!", [
                'enrollment_id'   => $enr['id'],
                'new_section_id'  => $targetSectionId,
                'new_section_name'=> $targetSec['name']
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            Response::error('Failed to transfer section: ' . $e->getMessage(), 500);
        }
    }
}
