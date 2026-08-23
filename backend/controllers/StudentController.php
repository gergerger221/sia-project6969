<?php
// backend/controllers/StudentController.php
namespace App\Controllers;

use App\Config\Database;
use App\Config\Response;
use App\Helpers\Auth;
use PDO;

class StudentController {
    /**
     * Get Student Dashboard Overview (Profile, Enrolled Section, Schedule, Balance, Events).
     */
    public function getDashboard(): void {
        $user = Auth::requireRole(['student', 'applicant']);
        $db = Database::getConnection();

        // 1. Get current active enrollment (by student user ID or linked applicant admission application)
        $enrStmt = $db->prepare("
            SELECT e.*, 
                   gl.name as grade_level_name, gl.category as grade_category,
                   t.name as track_name,
                   s.name as strand_name, s.code as strand_code,
                   sec.name as section_name, sec.room as section_room,
                   sy.name as school_year_name, sy.active_semester,
                   sa.assessment_no, sa.gross_amount, sa.voucher_discount, sa.net_payable, sa.total_paid, sa.remaining_balance, sa.status as payment_status,
                   u.student_id as official_student_no,
                   p.first_name as student_first_name, p.last_name as student_last_name, p.middle_name as student_middle_name
            FROM enrollments e
            JOIN grade_levels gl ON e.grade_level_id = gl.id
            LEFT JOIN tracks t ON e.track_id = t.id
            LEFT JOIN strands s ON e.strand_id = s.id
            JOIN sections sec ON e.section_id = sec.id
            JOIN school_years sy ON e.school_year_id = sy.id
            LEFT JOIN student_assessments sa ON e.id = sa.enrollment_id
            LEFT JOIN users u ON e.student_id = u.id
            LEFT JOIN user_profiles p ON u.id = p.user_id
            WHERE (e.student_id = :stud_id OR e.application_id IN (SELECT id FROM admission_applications WHERE user_id = :app_user_id))
            ORDER BY (CASE WHEN e.status = 'Officially Enrolled' THEN 1 ELSE 2 END) ASC, e.id DESC
            LIMIT 1
        ");
        $enrStmt->execute(['stud_id' => $user['id'], 'app_user_id' => $user['id']]);
        $enrollment = $enrStmt->fetch();

        // Fallback user display names if user profile is empty
        if (empty($user['first_name']) && $enrollment && !empty($enrollment['student_first_name'])) {
            $user['first_name'] = $enrollment['student_first_name'];
            $user['last_name'] = $enrollment['student_last_name'];
            $user['middle_name'] = $enrollment['student_middle_name'];
        }
        if (empty($user['student_id']) && $enrollment && !empty($enrollment['student_no'])) {
            $user['student_id'] = $enrollment['student_no'];
        }

        $subjects = [];
        $payments = [];

        if ($enrollment) {
            // 2. Fetch enrolled subjects and match with section timetable schedules & teachers
            $subStmt = $db->prepare("
                SELECT es.id as enrollment_subject_id, es.status as subject_status,
                       sub.id as subject_id, sub.code as subject_code, sub.title as subject_title, sub.category as subject_category, sub.units, sub.semester,
                       COALESCE(sch.day_of_week, auto_sch.day_of_week, 'Mon-Fri') as day_of_week,
                       COALESCE(sch.time_start, auto_sch.time_start, '08:00:00') as time_start,
                       COALESCE(sch.time_end, auto_sch.time_end, '09:00:00') as time_end,
                       COALESCE(sch.room, auto_sch.room, sec.room, 'Room 101') as room,
                       COALESCE(u.username, auto_u.username) as teacher_username,
                       COALESCE(tp.first_name, auto_tp.first_name, 'Faculty') as teacher_first,
                       COALESCE(tp.last_name, auto_tp.last_name, 'Teacher') as teacher_last
                FROM enrollment_subjects es
                JOIN subjects sub ON es.subject_id = sub.id
                JOIN enrollments e ON es.enrollment_id = e.id
                JOIN sections sec ON e.section_id = sec.id
                LEFT JOIN schedules sch ON es.schedule_id = sch.id
                LEFT JOIN users u ON sch.teacher_id = u.id
                LEFT JOIN user_profiles tp ON u.id = tp.user_id
                LEFT JOIN schedules auto_sch ON (auto_sch.section_id = e.section_id AND auto_sch.subject_id = sub.id AND auto_sch.is_active = 1)
                LEFT JOIN users auto_u ON auto_sch.teacher_id = auto_u.id
                LEFT JOIN user_profiles auto_tp ON auto_u.id = auto_tp.user_id
                WHERE es.enrollment_id = :enr_id
                ORDER BY sub.semester ASC, sub.code ASC
            ");
            $subStmt->execute(['enr_id' => $enrollment['id']]);
            $subjects = $subStmt->fetchAll();

            // 3. Fetch payment receipts
            if (!empty($enrollment['assessment_no'])) {
                $payStmt = $db->prepare("
                    SELECT p.*, u.username as received_by_user
                    FROM payments p
                    JOIN users u ON p.received_by = u.id
                    WHERE p.enrollment_id = :enr_id
                    ORDER BY p.id DESC
                ");
                $payStmt->execute(['enr_id' => $enrollment['id']]);
                $payments = $payStmt->fetchAll();
            }
        }

        // 4. Fetch School Events for Student Calendar Widget
        $events = $db->query("
            SELECT id, school_year_id, title, description, event_category, start_date, end_date, start_time, end_time, location, target_audience
            FROM school_events
            WHERE is_published = 1 AND (target_audience = 'All' OR target_audience = 'Students')
            ORDER BY start_date ASC
            LIMIT 10
        ")->fetchAll();

        Response::success('Student dashboard loaded', [
            'user'       => $user,
            'enrollment' => $enrollment ?: null,
            'subjects'   => $subjects,
            'payments'   => $payments,
            'events'     => $events
        ]);
    }
}
