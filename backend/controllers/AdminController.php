<?php
// backend/controllers/AdminController.php
namespace App\Controllers;

use App\Config\Database;
use App\Config\Response;
use App\Helpers\Auth;
use PDO;

class AdminController {
    /**
     * Get System Dashboard Stats.
     */
    public function getDashboardStats(): void {
        Auth::requireRole(['admin', 'coordinator']);
        $db = Database::getConnection();

        $applicantCount = $db->query("SELECT COUNT(*) FROM admission_applications")->fetchColumn();
        $pendingApps = $db->query("SELECT COUNT(*) FROM admission_applications WHERE status IN ('Pending', 'Under Review')")->fetchColumn();
        $enrolledJHS = $db->query("SELECT COUNT(*) FROM enrollments e JOIN grade_levels gl ON e.grade_level_id = gl.id WHERE e.status = 'Officially Enrolled' AND gl.category = 'JHS'")->fetchColumn();
        $enrolledSHS = $db->query("SELECT COUNT(*) FROM enrollments e JOIN grade_levels gl ON e.grade_level_id = gl.id WHERE e.status = 'Officially Enrolled' AND gl.category = 'SHS'")->fetchColumn();
        $totalCollected = $db->query("SELECT COALESCE(SUM(amount_paid), 0) FROM payments")->fetchColumn();
        $staffCount = $db->query("SELECT COUNT(*) FROM users WHERE role_id IN (1, 2, 3, 4, 5, 6)")->fetchColumn();

        $activeSy = $db->query("SELECT * FROM school_years WHERE is_active = 1 LIMIT 1")->fetch();

        // Recent audit logs
        $logs = $db->query("
            SELECT al.*, u.username
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.id DESC
            LIMIT 15
        ")->fetchAll();

        Response::success('Dashboard statistics loaded', [
            'total_applicants' => (int)$applicantCount,
            'pending_review'   => (int)$pendingApps,
            'enrolled_jhs'     => (int)$enrolledJHS,
            'enrolled_shs'     => (int)$enrolledSHS,
            'total_revenue'    => (float)$totalCollected,
            'total_staff'      => (int)$staffCount,
            'active_school_year' => $activeSy,
            'recent_logs'      => $logs
        ]);
    }

    /**
     * Get all School Years & Toggle School Year Lock.
     */
    public function getSchoolYears(): void {
        Auth::requireRole(['admin', 'coordinator']);
        $db = Database::getConnection();
        $schoolYears = $db->query("SELECT * FROM school_years ORDER BY id DESC")->fetchAll();
        Response::success('School years loaded', $schoolYears);
    }

    public function toggleSchoolYearLock(): void {
        $user = Auth::requireRole(['admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['school_year_id'] ?? 0);

        if (!$id) {
            Response::error('School year ID is required.');
        }

        $db = Database::getConnection();
        $sy = $db->prepare("SELECT is_locked, name FROM school_years WHERE id = :id");
        $sy->execute(['id' => $id]);
        $row = $sy->fetch();

        if (!$row) {
            Response::error('School year not found.');
        }

        $newLock = $row['is_locked'] ? 0 : 1;
        $stmt = $db->prepare("UPDATE school_years SET is_locked = :lock WHERE id = :id");
        $stmt->execute(['lock' => $newLock, 'id' => $id]);

        $actionText = $newLock ? "LOCKED (Enrollment closed)" : "UNLOCKED (Enrollment open)";
        Auth::logAudit('SCHOOL_YEAR_LOCK', "School Year {$row['name']} was {$actionText}", $user['id']);

        Response::success("School year is now {$actionText}", ['is_locked' => $newLock]);
    }

    public function toggleCurriculumLock(): void {
        $user = Auth::requireRole(['admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['school_year_id'] ?? 0);

        if (!$id) {
            Response::error('School year ID is required.');
        }

        $db = Database::getConnection();
        $sy = $db->prepare("SELECT curriculum_locked, name FROM school_years WHERE id = :id");
        $sy->execute(['id' => $id]);
        $row = $sy->fetch();

        if (!$row) {
            Response::error('School year not found.');
        }

        $newLock = !empty($row['curriculum_locked']) ? 0 : 1;
        $declAt = $newLock ? date('Y-m-d H:i:s') : null;

        $stmt = $db->prepare("
            UPDATE school_years 
            SET curriculum_locked = :lock, 
                curriculum_declared_at = :decl_at, 
                curriculum_declared_by = :uid 
            WHERE id = :id
        ");
        $stmt->execute([
            'lock'    => $newLock,
            'decl_at' => $declAt,
            'uid'     => $user['id'],
            'id'      => $id
        ]);

        $actionText = $newLock ? "OFFICIALLY DECLARED & LOCKED" : "UNLOCKED (DRAFT SETUP MODE)";
        Auth::logAudit('CURRICULUM_LOCK_TOGGLED', "School Year {$row['name']} curriculum was {$actionText} by {$user['username']}", $user['id']);

        Response::success("School year curriculum is now {$actionText}", [
            'curriculum_locked'      => $newLock,
            'curriculum_declared_at' => $declAt
        ]);
    }

    public function saveSchoolYear(): void {
        $user = Auth::requireRole(['admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = !empty($input['id']) ? (int)$input['id'] : null;
        $code = trim($input['code'] ?? '');
        $name = trim($input['name'] ?? '');
        $startDate = trim($input['start_date'] ?? '');
        $endDate = trim($input['end_date'] ?? '');
        $activeSemester = trim($input['active_semester'] ?? '1st Semester');
        $isActive = !empty($input['is_active']) ? 1 : 0;
        $isLocked = isset($input['is_locked']) ? (int)$input['is_locked'] : 1;
        $curriculumLocked = isset($input['curriculum_locked']) ? (int)$input['curriculum_locked'] : 0;

        if (!$code || !$name || !$startDate || !$endDate) {
            Response::error('Code, Name, Start Date, and End Date are required.');
        }

        $db = Database::getConnection();

        // Check duplicate code
        if ($id) {
            $dup = $db->prepare("SELECT id FROM school_years WHERE code = :code AND id != :id");
            $dup->execute(['code' => $code, 'id' => $id]);
        } else {
            $dup = $db->prepare("SELECT id FROM school_years WHERE code = :code");
            $dup->execute(['code' => $code]);
        }
        if ($dup->fetch()) {
            Response::error("School Year code '{$code}' already exists.");
        }

        if ($isActive) {
            $db->exec("UPDATE school_years SET is_active = 0");
        }

        if ($id) {
            $stmt = $db->prepare("
                UPDATE school_years 
                SET code = :code, name = :name, start_date = :start_date, end_date = :end_date, 
                    active_semester = :sem, is_active = :is_active
                WHERE id = :id
            ");
            $stmt->execute([
                'code'       => $code,
                'name'       => $name,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'sem'        => $activeSemester,
                'is_active'  => $isActive,
                'id'         => $id
            ]);
            Auth::logAudit('SCHOOL_YEAR_UPDATED', "School Year {$name} was updated", $user['id']);
            Response::success("School Year '{$name}' updated successfully.");
        } else {
            $stmt = $db->prepare("
                INSERT INTO school_years (code, name, start_date, end_date, active_semester, is_active, is_locked, curriculum_locked, created_at)
                VALUES (:code, :name, :start_date, :end_date, :sem, :is_active, :is_locked, :curriculum_locked, NOW())
            ");
            $stmt->execute([
                'code'              => $code,
                'name'              => $name,
                'start_date'        => $startDate,
                'end_date'          => $endDate,
                'sem'               => $activeSemester,
                'is_active'         => $isActive,
                'is_locked'         => $isLocked,
                'curriculum_locked' => $curriculumLocked
            ]);
            Auth::logAudit('SCHOOL_YEAR_CREATED', "New School Year {$name} ({$code}) created", $user['id']);
            Response::success("School Year '{$name}' created successfully.");
        }
    }

    public function setActiveSchoolYear(): void {
        $user = Auth::requireRole(['admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['school_year_id'] ?? 0);

        if (!$id) {
            Response::error('School year ID is required.');
        }

        $db = Database::getConnection();
        $sy = $db->prepare("SELECT id, name FROM school_years WHERE id = :id");
        $sy->execute(['id' => $id]);
        $row = $sy->fetch();
        if (!$row) {
            Response::error('School year not found.');
        }

        $db->exec("UPDATE school_years SET is_active = 0");
        $stmt = $db->prepare("UPDATE school_years SET is_active = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        Auth::logAudit('ACTIVE_SCHOOL_YEAR_CHANGED', "School Year {$row['name']} set as active", $user['id']);
        Response::success("School Year '{$row['name']}' is now the active School Year.");
    }

    /**
     * Get list of users / staff accounts.
     */
    public function getUsers(): void {
        Auth::requireRole(['admin']);
        $db = Database::getConnection();

        $roleSlug = $_GET['role'] ?? '';
        $sql = "
            SELECT u.id, u.role_id, u.username, u.email, u.student_id, u.status, u.created_at,
                   r.name as role_name, r.slug as role_slug,
                   p.first_name, p.middle_name, p.last_name, p.contact_number
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN user_profiles p ON u.id = p.user_id
            WHERE 1=1
        ";
        $params = [];
        if ($roleSlug) {
            $sql .= " AND r.slug = :role";
            $params['role'] = $roleSlug;
        }
        $sql .= " ORDER BY u.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        $roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();

        Response::success('Users loaded', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * Create or edit a Staff User account.
     */
    public function saveUser(): void {
        $admin = Auth::requireRole(['admin']);
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id = !empty($input['id']) ? (int)$input['id'] : null;
        $roleId = (int)($input['role_id'] ?? 2);
        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = trim($input['password'] ?? '');
        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $contactNumber = trim($input['contact_number'] ?? '');
        $status = $input['status'] ?? 'Active';

        if (!$username || !$email || !$firstName || !$lastName) {
            Response::error('Username, email, first name, and last name are required.');
        }

        $db = Database::getConnection();

        if ($id) {
            // Update user
            $sql = "UPDATE users SET role_id = :role_id, username = :username, email = :email, status = :status";
            $params = [
                'role_id'  => $roleId,
                'username' => $username,
                'email'    => $email,
                'status'   => $status,
                'id'       => $id
            ];
            if ($password) {
                $sql .= ", password = :password";
                $params['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            // Update profile
            $upProf = $db->prepare("
                UPDATE user_profiles SET
                    first_name = :first_name, last_name = :last_name, contact_number = :contact
                WHERE user_id = :id
            ");
            $upProf->execute([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'contact'    => $contactNumber,
                'id'         => $id
            ]);

            Auth::logAudit('USER_UPDATED', "Updated user {$username}", $admin['id']);
            Response::success('User account updated successfully');
        } else {
            // Create user
            if (!$password) {
                Response::error('Password is required for new accounts.');
            }
            $db->beginTransaction();
            try {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $ins = $db->prepare("
                    INSERT INTO users (role_id, username, email, password, status)
                    VALUES (:role_id, :username, :email, :password, :status)
                ");
                $ins->execute([
                    'role_id'  => $roleId,
                    'username' => $username,
                    'email'    => $email,
                    'password' => $hashed,
                    'status'   => $status
                ]);
                $newId = (int)$db->lastInsertId();

                $prof = $db->prepare("
                    INSERT INTO user_profiles (user_id, first_name, last_name, contact_number)
                    VALUES (:user_id, :first_name, :last_name, :contact)
                ");
                $prof->execute([
                    'user_id'    => $newId,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'contact'    => $contactNumber
                ]);

                $db->commit();
                Auth::logAudit('USER_CREATED', "Created user {$username}", $admin['id']);
                Response::success('User account created successfully', ['id' => $newId], 201);
            } catch (\Exception $e) {
                $db->rollBack();
                Response::error('Failed to create user: ' . $e->getMessage());
            }
        }
    }
}
