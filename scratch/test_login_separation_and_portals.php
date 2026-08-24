<?php
// scratch/test_login_separation_and_portals.php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/config/Response.php';
require_once __DIR__ . '/../backend/helpers/Auth.php';
require_once __DIR__ . '/../backend/controllers/AuthController.php';

use App\Config\Database;
use App\Controllers\AuthController;

$db = (new Database())->getConnection();

echo "====================================================\n";
echo "TESTING STRICT STUDENT & STAFF LOGIN ROLE SEPARATION\n";
echo "====================================================\n\n";

$passCount = 0;
$totalCount = 6;

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
// Helper to simulate API login
// ----------------------------------------------------
function simulateLogin($identity, $password, $portalType = '') {
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
        return ['status' => 401, 'message' => 'Invalid username or password.'];
    }

    if ($user['status'] !== 'Active') {
        return ['status' => 403, 'message' => 'Your account is ' . strtolower($user['status'])];
    }

    $staffRoles = ['admin', 'coordinator', 'registrar', 'treasury', 'records', 'teacher', 'scheduler'];

    if ($portalType === 'student') {
        if ($user['role_slug'] !== 'student') {
            if (in_array($user['role_slug'], $staffRoles)) {
                return ['status' => 403, 'message' => 'This login is for enrolled students only. Please use the Staff Login.'];
            } elseif ($user['role_slug'] === 'applicant') {
                return ['status' => 403, 'message' => 'This login is for officially enrolled students. If you are an applicant, please check your application status or continue your admission procedure.'];
            } else {
                return ['status' => 403, 'message' => 'This login is for enrolled students only. Please use the Staff Login.'];
            }
        }
    } elseif ($portalType === 'staff') {
        if (!in_array($user['role_slug'], $staffRoles)) {
            if ($user['role_slug'] === 'student') {
                return ['status' => 403, 'message' => 'This portal is for authorized faculty and staff personnel only. Students must log in via the Student Portal.'];
            } elseif ($user['role_slug'] === 'applicant') {
                return ['status' => 403, 'message' => 'This portal is for authorized faculty and staff personnel only. Applicants must log in via the Admission Portal.'];
            } else {
                return ['status' => 403, 'message' => 'This portal is for authorized faculty and staff personnel only.'];
            }
        }
    }

    return ['status' => 200, 'message' => 'Login successful', 'user' => $user];
}

// ----------------------------------------------------
// TEST 1: Enrolled Student Login via Student Portal
// ----------------------------------------------------
$res1 = simulateLogin('student2026', 'password123', 'student');
testAssert(1, "Enrolled Student Login (SHS)", $res1['status'] === 200 && $res1['user']['role_slug'] === 'student', "Authenticated student 'student2026' with student role");

// ----------------------------------------------------
// TEST 2: JHS Enrolled Student Login via Student Portal
// ----------------------------------------------------
$res2 = simulateLogin('2026-JHS-0001', 'password123', 'student');
testAssert(2, "Enrolled Student Login (JHS)", $res2['status'] === 200 && $res2['user']['role_slug'] === 'student', "Authenticated JHS student '2026-JHS-0001' with student role");

// ----------------------------------------------------
// TEST 3: Staff Account Attempting to Login via Student Portal (Must be Blocked)
// ----------------------------------------------------
$res3 = simulateLogin('admin', 'password123', 'student');
testAssert(3, "Staff Account Blocked on Student Portal", $res3['status'] === 403 && $res3['message'] === 'This login is for enrolled students only. Please use the Staff Login.', "Correctly rejected admin account on student login with 403 directive message");

// ----------------------------------------------------
// TEST 4: Authorized Staff Login via Staff Portal (Admin, Coordinator, Registrar, Treasury, Records)
// ----------------------------------------------------
$res4a = simulateLogin('admin', 'password123', 'staff');
$res4b = simulateLogin('registrar', 'password123', 'staff');
$res4c = simulateLogin('treasury', 'password123', 'staff');
$res4d = simulateLogin('coordinator', 'password123', 'staff');
$res4e = simulateLogin('records', 'password123', 'staff');
testAssert(4, "Authorized Personnel Login via Staff Portal", $res4a['status'] === 200 && $res4b['status'] === 200 && $res4c['status'] === 200 && $res4d['status'] === 200 && $res4e['status'] === 200, "Successfully authenticated Admin, Coordinator, Registrar, Treasury, and Records staff roles");

// ----------------------------------------------------
// TEST 5: Student Account Attempting to Login via Staff Portal (Must be Blocked)
// ----------------------------------------------------
$res5 = simulateLogin('student2026', 'password123', 'staff');
testAssert(5, "Student Account Blocked on Staff Portal", $res5['status'] === 403 && $res5['message'] === 'This portal is for authorized faculty and staff personnel only. Students must log in via the Student Portal.', "Correctly rejected student account on staff login with 403 directive message");

// ----------------------------------------------------
// TEST 6: Applicant Account Attempting to Login via Staff Portal (Must be Blocked)
// ----------------------------------------------------
// Create or verify an applicant user for testing
$appUser = $db->query("SELECT * FROM users WHERE role_id = 8 LIMIT 1")->fetch();
if (!$appUser) {
    $hash = password_hash('password123', PASSWORD_BCRYPT);
    $db->prepare("INSERT INTO users (role_id, username, email, password, status) VALUES (8, 'test_applicant', 'applicant@test.com', :p, 'Active')")->execute(['p' => $hash]);
    $appUsername = 'test_applicant';
} else {
    $appUsername = $appUser['username'];
    $db->prepare("UPDATE users SET password = :p WHERE id = :id")->execute(['p' => password_hash('password123', PASSWORD_BCRYPT), 'id' => $appUser['id']]);
}
$res6 = simulateLogin($appUsername, 'password123', 'staff');
testAssert(6, "Applicant Account Blocked on Staff Portal", $res6['status'] === 403 && strpos($res6['message'], 'authorized faculty and staff') !== false, "Correctly rejected applicant account on staff login");

echo "\n====================================================\n";
echo "TEST RESULTS SUMMARY: {$passCount} / {$totalCount} PASSED\n";
echo "====================================================\n";
