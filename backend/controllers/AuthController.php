<?php
// backend/controllers/AuthController.php
namespace App\Controllers;

use App\Config\Database;
use App\Config\Response;
use App\Helpers\Auth;
use PDO;

class AuthController {
    /**
     * User Login (Supports staff, student, and temp applicant)
     */
    public function login(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $identity = trim($input['username'] ?? $input['email'] ?? '');
        $password = trim($input['password'] ?? '');

        if (!$identity || !$password) {
            Response::error('Please provide your username/email and password.');
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, r.name as role_name, r.slug as role_slug,
                   p.first_name, p.middle_name, p.last_name, p.contact_number
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN user_profiles p ON u.id = p.user_id
            WHERE u.username = :ident1 OR u.email = :ident2 OR u.student_id = :ident3
            LIMIT 1
        ");
        $stmt->execute([
            'ident1' => $identity,
            'ident2' => $identity,
            'ident3' => $identity
        ]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            Response::error('Invalid username or password.', 401);
        }

        if ($user['status'] !== 'Active') {
            Response::error('Your account is ' . strtolower($user['status']) . '. Please contact the registrar.', 403);
        }

        // STRICT SERVER-SIDE PORTAL SEPARATION ENFORCEMENT
        $portalType = trim($input['portal_type'] ?? '');
        $staffRoles = ['admin', 'coordinator', 'registrar', 'treasury', 'records', 'teacher', 'scheduler'];

        if ($portalType === 'student') {
            if ($user['role_slug'] !== 'student') {
                if (in_array($user['role_slug'], $staffRoles)) {
                    Response::error('This login is for enrolled students only. Please use the Staff Login.', 403);
                } elseif ($user['role_slug'] === 'applicant') {
                    Response::error('This login is for officially enrolled students. If you are an applicant, please check your application status or continue your admission procedure.', 403);
                } else {
                    Response::error('This login is for enrolled students only. Please use the Staff Login.', 403);
                }
            }
        } elseif ($portalType === 'staff') {
            if (!in_array($user['role_slug'], $staffRoles)) {
                if ($user['role_slug'] === 'student') {
                    Response::error('This portal is for authorized faculty and staff personnel only. Students must log in via the Student Portal.', 403);
                } elseif ($user['role_slug'] === 'applicant') {
                    Response::error('This portal is for authorized faculty and staff personnel only. Applicants must log in via the Admission Portal.', 403);
                } else {
                    Response::error('This portal is for authorized faculty and staff personnel only.', 403);
                }
            }
        } elseif ($portalType === 'applicant') {
            if ($user['role_slug'] !== 'applicant') {
                if ($user['role_slug'] === 'student') {
                    Response::error('Your enrollment has already been approved! Please sign in using the Student Portal.', 403);
                } elseif (in_array($user['role_slug'], $staffRoles)) {
                    Response::error('This portal is for student applicants. Please use the Staff Login.', 403);
                }
            }
        }

        $token = Auth::generateToken((int)$user['id']);
        Auth::logAudit('LOGIN', "User {$user['username']} logged in", (int)$user['id']);

        unset($user['password'], $user['remember_token']);
        $user['token'] = $token;

        Response::success('Login successful', $user);
    }

    /**
     * Register a Temporary Admission Account for Applicants
     * Home Page > Admission > (Temp Admission Account)
     */
    public function registerApplicant(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $middleName = trim($input['middle_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $rawContact = trim($input['contact_number'] ?? '');
        $contactNumber = preg_replace('/\D/', '', $rawContact);
        $password = trim($input['password'] ?? '');

        if (!$firstName || !$lastName || !$middleName || !$email || !$password) {
            Response::error('First name, middle name, last name, email, and password are required.');
        }

        if (!preg_match('/^09\d{9}$/', $contactNumber)) {
            Response::error('Must be an 11-digit Philippine mobile number starting with 09.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email address format.');
        }

        $db = Database::getConnection();

        // Check if optional LRN passed at registration is unique
        $rawLrn = trim($input['lrn'] ?? '');
        $cleanLrn = preg_replace('/\D/', '', $rawLrn);
        if (!empty($cleanLrn)) {
            if (strlen($cleanLrn) !== 12) {
                Response::error('DepEd Learner Reference Number (LRN) must be exactly 12 numeric digits.');
            }
            $chkLrn = $db->prepare("
                SELECT id FROM admission_applications WHERE lrn = :lrn1
                UNION
                SELECT id FROM enrollments WHERE lrn = :lrn2
                LIMIT 1
            ");
            $chkLrn->execute(['lrn1' => $cleanLrn, 'lrn2' => $cleanLrn]);
            if ($chkLrn->fetch()) {
                Response::error('This LRN is already registered in the system. Please verify your LRN.');
            }
        }

        // Check if email already exists
        $check = $db->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            Response::error('An account with this email address already exists. Please login instead.');
        }

        // Generate a username for the applicant
        $baseUser = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . '.' . $lastName));
        $username = $baseUser . rand(100, 999);

        // Get applicant role_id
        $roleStmt = $db->query("SELECT id FROM roles WHERE slug = 'applicant' LIMIT 1");
        $role = $roleStmt->fetch();
        $roleId = $role ? (int)$role['id'] : 8;

        $db->beginTransaction();
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $userStmt = $db->prepare("
                INSERT INTO users (role_id, username, email, password, status)
                VALUES (:role_id, :username, :email, :password, 'Active')
            ");
            $userStmt->execute([
                'role_id'  => $roleId,
                'username' => $username,
                'email'    => $email,
                'password' => $hashedPassword
            ]);
            $userId = (int)$db->lastInsertId();

            $profStmt = $db->prepare("
                INSERT INTO user_profiles (user_id, first_name, middle_name, last_name, contact_number)
                VALUES (:user_id, :first_name, :middle_name, :last_name, :contact_number)
            ");
            $profStmt->execute([
                'user_id'        => $userId,
                'first_name'     => $firstName,
                'middle_name'    => $middleName,
                'last_name'      => $lastName,
                'contact_number' => $contactNumber
            ]);

            // Generate Application Reference Number (e.g. ADM-2026-0001)
            $appCountStmt = $db->query("SELECT COUNT(*) FROM admission_applications");
            $nextAppNum = (int)$appCountStmt->fetchColumn() + 1;
            $appNo = 'ADM-' . date('Y') . '-' . str_pad((string)$nextAppNum, 4, '0', STR_PAD_LEFT);

            // Fetch active School Year
            $syStmt = $db->query("SELECT id FROM school_years WHERE is_active = 1 LIMIT 1");
            $activeSy = $syStmt->fetch();
            $syId = $activeSy ? (int)$activeSy['id'] : 1;

            // Create initial pending application record
            $appStmt = $db->prepare("
                INSERT INTO admission_applications (
                    application_no, user_id, first_name, middle_name, last_name,
                    gender, birthdate, contact_number, email, address_barangay, address_city,
                    address_province, guardian_name, guardian_relationship, guardian_contact,
                    school_year_id, grade_level_id, status
                ) VALUES (
                    :app_no, :user_id, :first_name, :middle_name, :last_name,
                    'Male', '2010-01-01', :contact_number, :email, 'Barangay 1', 'Manila',
                    'Metro Manila', 'Guardian', 'Parent', :guardian_contact,
                    :sy_id, NULL, 'Pending'
                )
            ");
            $appStmt->execute([
                'app_no'           => $appNo,
                'user_id'          => $userId,
                'first_name'       => $firstName,
                'middle_name'      => $middleName,
                'last_name'        => $lastName,
                'contact_number'   => $contactNumber,
                'email'            => $email,
                'guardian_contact' => $contactNumber,
                'sy_id'            => $syId
            ]);

            $token = Auth::generateToken($userId);
            $db->commit();

            Auth::logAudit('APPLICANT_REGISTER', "New applicant registered: {$appNo} ({$email})", $userId);

            Response::success('Admission account created successfully!', [
                'user_id'        => $userId,
                'username'       => $username,
                'email'          => $email,
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'role_slug'      => 'applicant',
                'role_name'      => 'Applicant',
                'application_no' => $appNo,
                'token'          => $token
            ], 201);
        } catch (\Exception $e) {
            $db->rollBack();
            Response::error('Failed to create account: ' . $e->getMessage());
        }
    }

    /**
     * Get Current Authenticated User Session
     */
    public function me(): void {
        $user = Auth::requireAuth();
        $db = Database::getConnection();

        // If applicant, attach their application details
        if ($user['role_slug'] === 'applicant') {
            $appStmt = $db->prepare("SELECT * FROM admission_applications WHERE user_id = :user_id LIMIT 1");
            $appStmt->execute(['user_id' => $user['id']]);
            $user['application'] = $appStmt->fetch() ?: null;
        }

        // If student, attach their enrollment details
        if ($user['role_slug'] === 'student') {
            $enrStmt = $db->prepare("
                SELECT e.*, g.name as grade_name, s.name as strand_name, sec.name as section_name
                FROM enrollments e
                JOIN grade_levels g ON e.grade_level_id = g.id
                LEFT JOIN strands s ON e.strand_id = s.id
                JOIN sections sec ON e.section_id = sec.id
                WHERE e.student_id = :user_id
                ORDER BY e.id DESC LIMIT 1
            ");
            $enrStmt->execute(['user_id' => $user['id']]);
            $user['enrollment'] = $enrStmt->fetch() ?: null;
        }

        Response::success('User authenticated', $user);
    }

    /**
     * Logout
     */
    public function logout(): void {
        $user = Auth::user();
        if ($user) {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE id = :id");
            $stmt->execute(['id' => $user['id']]);
            Auth::logAudit('LOGOUT', "User logged out", (int)$user['id']);
        }
        Response::success('Logged out successfully');
    }
}
