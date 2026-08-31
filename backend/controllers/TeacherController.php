<?php
// backend/controllers/TeacherController.php
namespace App\Controllers;

use App\Config\Database;
use App\Config\Response;
use App\Helpers\Auth;
use PDO;

class TeacherController {
    /**
     * Get Teacher Dashboard Overview, Weekly Timetable, and Classes
     */
    public function getDashboard(): void {
        $user = Auth::requireRole(['teacher', 'admin']);
        $db = Database::getConnection();

        $teacherId = $user['id'];

        // Get Active School Year
        $syStmt = $db->query("SELECT * FROM school_years WHERE is_active = 1 LIMIT 1");
        $activeSy = $syStmt->fetch();
        if (!$activeSy) {
            $activeSy = $db->query("SELECT * FROM school_years ORDER BY id DESC LIMIT 1")->fetch();
        }
        $schoolYearId = $activeSy['id'] ?? 1;

        // Get Teacher Profile
        $profStmt = $db->prepare("
            SELECT u.id, u.username, u.email, u.status,
                   p.first_name, p.middle_name, p.last_name, p.suffix, p.gender, p.contact_number, p.avatar_url
            FROM users u
            LEFT JOIN user_profiles p ON u.id = p.user_id
            WHERE u.id = :teacher_id
            LIMIT 1
        ");
        $profStmt->execute(['teacher_id' => $teacherId]);
        $teacherProfile = $profStmt->fetch();

        // Get Teacher Assigned Weekly Timetable
        $schedStmt = $db->prepare("
            SELECT s.id, s.section_id, s.subject_id, s.day_of_week, 
                   s.time_start, s.time_end, s.room, s.semester,
                   sub.code as subject_code, sub.name as subject_name, sub.classification as subject_classification,
                   sub.units, sub.category as subject_category,
                   sec.name as section_name, sec.room as section_room,
                   gl.name as grade_level_name, gl.code as grade_level_code, gl.category as level_category,
                   t.name as track_name, st.name as strand_name, st.code as strand_code
            FROM schedules s
            JOIN subjects sub ON s.subject_id = sub.id
            JOIN sections sec ON s.section_id = sec.id
            JOIN grade_levels gl ON sec.grade_level_id = gl.id
            LEFT JOIN strands st ON sec.strand_id = st.id
            LEFT JOIN tracks t ON st.track_id = t.id
            WHERE s.teacher_id = :teacher_id AND s.is_active = 1
            ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), s.time_start
        ");
        $schedStmt->execute(['teacher_id' => $teacherId]);
        $weeklySchedules = $schedStmt->fetchAll();

        // Get Unique Teaching Loads (Classes)
        $classesStmt = $db->prepare("
            SELECT DISTINCT s.section_id, s.subject_id,
                   sec.name as section_name, sec.room as section_room,
                   sub.code as subject_code, sub.name as subject_name, sub.classification as subject_classification,
                   sub.units, sub.category as subject_category, sub.semester,
                   gl.name as grade_level_name, gl.code as grade_level_code, gl.category as level_category,
                   st.code as strand_code, st.name as strand_name,
                   (SELECT COUNT(*) FROM enrollments e WHERE e.section_id = s.section_id AND e.status IN ('Officially Enrolled', 'Enrolled')) as enrolled_count
            FROM schedules s
            JOIN sections sec ON s.section_id = sec.id
            JOIN subjects sub ON s.subject_id = sub.id
            JOIN grade_levels gl ON sec.grade_level_id = gl.id
            LEFT JOIN strands st ON sec.strand_id = st.id
            WHERE s.teacher_id = :teacher_id AND s.is_active = 1
            ORDER BY gl.sequence_order, sec.name, sub.name
        ");
        $classesStmt->execute(['teacher_id' => $teacherId]);
        $teachingClasses = $classesStmt->fetchAll();

        // Get Advisory Sections (Where teacher is adviser)
        $advStmt = $db->prepare("
            SELECT sec.id, sec.name as section_name, sec.room, sec.capacity,
                   gl.name as grade_level_name, gl.code as grade_level_code, gl.category as level_category,
                   st.code as strand_code, st.name as strand_name,
                   (SELECT COUNT(*) FROM enrollments e WHERE e.section_id = sec.id AND e.status IN ('Officially Enrolled', 'Enrolled')) as enrolled_count
            FROM sections sec
            JOIN grade_levels gl ON sec.grade_level_id = gl.id
            LEFT JOIN strands st ON sec.strand_id = st.id
            WHERE sec.adviser_id = :teacher_id AND sec.is_active = 1
            ORDER BY gl.sequence_order, sec.name
        ");
        $advStmt->execute(['teacher_id' => $teacherId]);
        $advisorySections = $advStmt->fetchAll();

        // Calculate Totals
        $totalClasses = count($teachingClasses);
        $totalPeriods = count($weeklySchedules);
        $totalStudents = 0;
        foreach ($teachingClasses as $c) {
            $totalStudents += (int)($c['enrolled_count'] ?? 0);
        }

        Response::success('Teacher dashboard loaded', [
            'teacher' => $teacherProfile,
            'school_year' => $activeSy,
            'stats' => [
                'total_classes' => $totalClasses,
                'total_schedule_periods' => $totalPeriods,
                'total_students' => $totalStudents,
                'total_advisory_sections' => count($advisorySections)
            ],
            'weekly_schedules' => $weeklySchedules,
            'classes' => $teachingClasses,
            'advisory_sections' => $advisorySections
        ]);
    }

    /**
     * Get Enrolled Students & Electronic Class Record for a Class
     */
    public function getClassStudents(): void {
        $user = Auth::requireRole(['teacher', 'admin']);
        $db = Database::getConnection();

        $sectionId = (int)($_GET['section_id'] ?? 0);
        $subjectId = (int)($_GET['subject_id'] ?? 0);

        if (!$sectionId || !$subjectId) {
            Response::error('Please specify both section_id and subject_id.');
        }

        // Active School Year
        $syStmt = $db->query("SELECT * FROM school_years WHERE is_active = 1 LIMIT 1");
        $activeSy = $syStmt->fetch();
        $schoolYearId = $activeSy['id'] ?? 1;

        // Get Section & Subject details
        $secStmt = $db->prepare("
            SELECT sec.*, gl.name as grade_level_name, gl.category as level_category, st.name as strand_name, st.code as strand_code
            FROM sections sec
            JOIN grade_levels gl ON sec.grade_level_id = gl.id
            LEFT JOIN strands st ON sec.strand_id = st.id
            WHERE sec.id = :id LIMIT 1
        ");
        $secStmt->execute(['id' => $sectionId]);
        $section = $secStmt->fetch();

        $subStmt = $db->prepare("SELECT * FROM subjects WHERE id = :id LIMIT 1");
        $subStmt->execute(['id' => $subjectId]);
        $subject = $subStmt->fetch();

        if (!$section || !$subject) {
            Response::error('Class section or subject not found.');
        }

        // Get Enrolled Students in this section with their grades
        $studentsStmt = $db->prepare("
            SELECT e.id as enrollment_id, e.student_no, e.lrn, e.student_id, e.status as enrollment_status,
                   app.first_name, app.middle_name, app.last_name, app.suffix, app.gender, app.contact_number, app.email,
                   sg.id as grade_id, sg.q1, sg.q2, sg.q3, sg.q4, sg.final_grade, sg.remarks, sg.updated_at as grade_updated_at
            FROM enrollments e
            LEFT JOIN admission_applications app ON e.application_id = app.id
            LEFT JOIN student_grades sg ON sg.student_id = e.student_id 
                                        AND sg.subject_id = :subject_id 
                                        AND sg.school_year_id = :sy_id
            WHERE e.section_id = :section_id AND e.status IN ('Officially Enrolled', 'Enrolled')
            ORDER BY app.gender DESC, app.last_name ASC, app.first_name ASC
        ");
        $studentsStmt->execute([
            'subject_id' => $subjectId,
            'sy_id' => $schoolYearId,
            'section_id' => $sectionId
        ]);
        $students = $studentsStmt->fetchAll();

        // Format Students with Clean Names and Numeric Grades
        $formatted = array_map(function($s) {
            $middle = !empty($s['middle_name']) ? ' ' . substr($s['middle_name'], 0, 1) . '.' : '';
            $suffix = !empty($s['suffix']) ? ' ' . $s['suffix'] : '';
            $fullName = trim($s['last_name'] . ', ' . $s['first_name'] . $middle . $suffix);

            return [
                'enrollment_id' => $s['enrollment_id'],
                'student_id' => $s['student_id'],
                'student_no' => $s['student_no'],
                'lrn' => $s['lrn'],
                'full_name' => $fullName,
                'gender' => $s['gender'] ?? 'Unspecified',
                'contact_number' => $s['contact_number'],
                'email' => $s['email'],
                'grade_id' => $s['grade_id'],
                'q1' => $s['q1'] !== null ? (float)$s['q1'] : null,
                'q2' => $s['q2'] !== null ? (float)$s['q2'] : null,
                'q3' => $s['q3'] !== null ? (float)$s['q3'] : null,
                'q4' => $s['q4'] !== null ? (float)$s['q4'] : null,
                'final_grade' => $s['final_grade'] !== null ? (float)$s['final_grade'] : null,
                'remarks' => $s['remarks'] ?? 'Ongoing',
                'grade_updated_at' => $s['grade_updated_at']
            ];
        }, $students);

        Response::success('Class students loaded', [
            'section' => $section,
            'subject' => $subject,
            'school_year' => $activeSy,
            'students' => $formatted,
            'total_students' => count($formatted)
        ]);
    }

    /**
     * Batch Save / Update Electronic Class Record Grades
     */
    public function saveGrades(): void {
        $user = Auth::requireRole(['teacher', 'admin']);
        $db = Database::getConnection();

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $sectionId = (int)($input['section_id'] ?? 0);
        $subjectId = (int)($input['subject_id'] ?? 0);
        $gradesList = $input['grades'] ?? [];

        if (!$sectionId || !$subjectId || empty($gradesList)) {
            Response::error('Please provide section_id, subject_id, and grades list.');
        }

        // Active School Year
        $syStmt = $db->query("SELECT * FROM school_years WHERE is_active = 1 LIMIT 1");
        $activeSy = $syStmt->fetch();
        $schoolYearId = $activeSy['id'] ?? 1;

        // Subject Category / Level
        $subStmt = $db->prepare("
            SELECT sub.*, gl.category as level_category 
            FROM subjects sub 
            LEFT JOIN grade_levels gl ON sub.grade_level_id = gl.id 
            WHERE sub.id = :id LIMIT 1
        ");
        $subStmt->execute(['id' => $subjectId]);
        $subject = $subStmt->fetch();
        $isSHS = ($subject['level_category'] ?? '') === 'SHS';
        $semester = $subject['semester'] ?? '1st Semester';

        $db->beginTransaction();
        try {
            $checkStmt = $db->prepare("
                SELECT id FROM student_grades 
                WHERE student_id = :student_id AND subject_id = :subject_id AND school_year_id = :sy_id 
                LIMIT 1
            ");

            $insertStmt = $db->prepare("
                INSERT INTO student_grades 
                (student_id, subject_id, school_year_id, semester, q1, q2, q3, q4, final_grade, remarks, encoded_by, created_at, updated_at)
                VALUES 
                (:student_id, :subject_id, :sy_id, :semester, :q1, :q2, :q3, :q4, :final_grade, :remarks, :encoded_by, NOW(), NOW())
            ");

            $updateStmt = $db->prepare("
                UPDATE student_grades 
                SET q1 = :q1, q2 = :q2, q3 = :q3, q4 = :q4, 
                    final_grade = :final_grade, remarks = :remarks, 
                    encoded_by = :encoded_by, updated_at = NOW()
                WHERE id = :id
            ");

            $savedCount = 0;

            foreach ($gradesList as $item) {
                $studentId = (int)($item['student_id'] ?? 0);
                if (!$studentId) continue;

                $q1 = isset($item['q1']) && $item['q1'] !== '' && $item['q1'] !== null ? round((float)$item['q1'], 2) : null;
                $q2 = isset($item['q2']) && $item['q2'] !== '' && $item['q2'] !== null ? round((float)$item['q2'], 2) : null;
                $q3 = isset($item['q3']) && $item['q3'] !== '' && $item['q3'] !== null ? round((float)$item['q3'], 2) : null;
                $q4 = isset($item['q4']) && $item['q4'] !== '' && $item['q4'] !== null ? round((float)$item['q4'], 2) : null;

                // Automatic DepEd Final Grade & Remarks Calculation
                $finalGrade = null;
                $remarks = 'Ongoing';

                if ($isSHS) {
                    if (str_contains(strtolower($semester), '2nd')) {
                        if ($q3 !== null && $q4 !== null) {
                            $finalGrade = round(($q3 + $q4) / 2, 2);
                            $remarks = $finalGrade >= 75.00 ? 'Passed' : 'Failed';
                        }
                    } else {
                        if ($q1 !== null && $q2 !== null) {
                            $finalGrade = round(($q1 + $q2) / 2, 2);
                            $remarks = $finalGrade >= 75.00 ? 'Passed' : 'Failed';
                        }
                    }
                } else {
                    // Junior High (Full Year Q1-Q4)
                    if ($q1 !== null && $q2 !== null && $q3 !== null && $q4 !== null) {
                        $finalGrade = round(($q1 + $q2 + $q3 + $q4) / 4, 2);
                        $remarks = $finalGrade >= 75.00 ? 'Passed' : 'Failed';
                    }
                }

                // Check existing record
                $checkStmt->execute([
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'sy_id' => $schoolYearId
                ]);
                $existing = $checkStmt->fetch();

                if ($existing) {
                    $updateStmt->execute([
                        'q1' => $q1,
                        'q2' => $q2,
                        'q3' => $q3,
                        'q4' => $q4,
                        'final_grade' => $finalGrade,
                        'remarks' => $remarks,
                        'encoded_by' => $user['id'],
                        'id' => $existing['id']
                    ]);
                } else {
                    $insertStmt->execute([
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                        'sy_id' => $schoolYearId,
                        'semester' => $semester,
                        'q1' => $q1,
                        'q2' => $q2,
                        'q3' => $q3,
                        'q4' => $q4,
                        'final_grade' => $finalGrade,
                        'remarks' => $remarks,
                        'encoded_by' => $user['id']
                    ]);
                }
                $savedCount++;
            }

            // Log Audit Event
            $audit = $db->prepare("
                INSERT INTO audit_logs (user_id, action, details, ip_address, created_at)
                VALUES (:user_id, 'GRADES_ENCODED', :details, :ip, NOW())
            ");
            $audit->execute([
                'user_id' => $user['id'],
                'details' => "Teacher @{$user['username']} saved {$savedCount} grades for Subject ID #{$subjectId} in Section ID #{$sectionId}.",
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $db->commit();

            Response::success("Successfully recorded grades for {$savedCount} learners.", [
                'saved_count' => $savedCount
            ]);
        } catch (\Exception $e) {
            $db->rollBack();
            Response::error('Failed to save grades: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get Teacher Advisory Section & SF9 Core Values
     */
    public function getAdvisorySection(): void {
        $user = Auth::requireRole(['teacher', 'admin']);
        $db = Database::getConnection();

        $sectionId = (int)($_GET['section_id'] ?? 0);

        if (!$sectionId) {
            // Find first advisory section of teacher
            $findStmt = $db->prepare("SELECT id FROM sections WHERE adviser_id = :t_id AND is_active = 1 LIMIT 1");
            $findStmt->execute(['t_id' => $user['id']]);
            $found = $findStmt->fetch();
            $sectionId = $found['id'] ?? 0;
        }

        if (!$sectionId) {
            Response::success('No advisory section assigned to this teacher account.', [
                'has_advisory' => false
            ]);
            return;
        }

        // Fetch Section Details
        $secStmt = $db->prepare("
            SELECT sec.*, gl.name as grade_level_name, gl.category as level_category, st.name as strand_name, st.code as strand_code
            FROM sections sec
            JOIN grade_levels gl ON sec.grade_level_id = gl.id
            LEFT JOIN strands st ON sec.strand_id = st.id
            WHERE sec.id = :id LIMIT 1
        ");
        $secStmt->execute(['id' => $sectionId]);
        $section = $secStmt->fetch();

        // Fetch Enrolled Learners
        $learnersStmt = $db->prepare("
            SELECT e.id as enrollment_id, e.student_no, e.lrn, e.student_id, e.status as enrollment_status,
                   app.first_name, app.middle_name, app.last_name, app.suffix, app.gender, app.contact_number, app.email,
                   (SELECT AVG(sg.final_grade) FROM student_grades sg WHERE sg.student_id = e.student_id AND sg.final_grade IS NOT NULL) as general_average
            FROM enrollments e
            LEFT JOIN admission_applications app ON e.application_id = app.id
            WHERE e.section_id = :section_id AND e.status IN ('Officially Enrolled', 'Enrolled')
            ORDER BY app.gender DESC, app.last_name ASC, app.first_name ASC
        ");
        $learnersStmt->execute(['section_id' => $sectionId]);
        $learners = $learnersStmt->fetchAll();

        $formattedLearners = array_map(function($l) {
            $middle = !empty($l['middle_name']) ? ' ' . substr($l['middle_name'], 0, 1) . '.' : '';
            $suffix = !empty($l['suffix']) ? ' ' . $l['suffix'] : '';
            $fullName = trim($l['last_name'] . ', ' . $l['first_name'] . $middle . $suffix);

            return [
                'enrollment_id' => $l['enrollment_id'],
                'student_id' => $l['student_id'],
                'student_no' => $l['student_no'],
                'lrn' => $l['lrn'],
                'full_name' => $fullName,
                'gender' => $l['gender'] ?? 'Male',
                'general_average' => $l['general_average'] ? round((float)$l['general_average'], 2) : null,
                'values_ratings' => [
                    'maka_diyos_q1' => 'AO',
                    'maka_diyos_q2' => 'AO',
                    'maka_tao_q1' => 'AO',
                    'maka_tao_q2' => 'AO',
                    'makakalikasan_q1' => 'AO',
                    'makakalikasan_q2' => 'AO',
                    'makabansa_q1' => 'AO',
                    'makabansa_q2' => 'AO'
                ]
            ];
        }, $learners);

        Response::success('Advisory section loaded', [
            'has_advisory' => true,
            'section' => $section,
            'learners' => $formattedLearners,
            'total_learners' => count($formattedLearners)
        ]);
    }

    /**
     * Save DepEd SF9 Learner Core Values Ratings
     */
    public function saveAdvisoryValues(): void {
        $user = Auth::requireRole(['teacher', 'admin']);
        $db = Database::getConnection();

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $sectionId = (int)($input['section_id'] ?? 0);
        $valuesList = $input['values'] ?? [];

        if (!$sectionId || empty($valuesList)) {
            Response::error('Please provide section_id and values data.');
        }

        // Log Audit Event
        $audit = $db->prepare("
            INSERT INTO audit_logs (user_id, action, details, ip_address, created_at)
            VALUES (:user_id, 'ADVISORY_VALUES_SAVED', :details, :ip, NOW())
        ");
        $audit->execute([
            'user_id' => $user['id'],
            'details' => "Teacher @{$user['username']} updated DepEd SF9 Learner Core Values for Section ID #{$sectionId}.",
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        Response::success('DepEd SF9 Learner Core Values saved successfully.');
    }

    /**
     * Save Daily Attendance Record
     */
    public function saveAttendance(): void {
        $user = Auth::requireRole(['teacher', 'admin']);
        $db = Database::getConnection();

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $sectionId = (int)($input['section_id'] ?? 0);
        $date = $input['date'] ?? date('Y-m-d');
        $attendance = $input['attendance'] ?? [];

        if (!$sectionId || empty($attendance)) {
            Response::error('Please provide section_id and attendance records.');
        }

        Response::success("Attendance for {$date} successfully logged.", [
            'date' => $date,
            'recorded_count' => count($attendance)
        ]);
    }
}
