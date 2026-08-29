<?php
require_once __DIR__ . '/../backend/config/Database.php';
require_once __DIR__ . '/../backend/config/Response.php';
require_once __DIR__ . '/../backend/helpers/Auth.php';
require_once __DIR__ . '/../backend/controllers/AuthController.php';

$testAccounts = [
    ['identity' => 'admin', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'admin'],
    ['identity' => 'maria_registrar', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'registrar'],
    ['identity' => 'registrar', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'registrar'],
    ['identity' => 'maria_coordinator', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'coordinator'],
    ['identity' => 'coordinator', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'coordinator'],
    ['identity' => 'maria_treasury', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'treasury'],
    ['identity' => 'treasury', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'treasury'],
    ['identity' => 'maria_records', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'records'],
    ['identity' => 'records', 'password' => 'password123', 'portal' => 'staff', 'expected_role' => 'records'],
    ['identity' => 'student2026', 'password' => 'password123', 'portal' => 'student', 'expected_role' => 'student'],
    ['identity' => '2026-SHS-0001', 'password' => 'password123', 'portal' => 'student', 'expected_role' => 'student'],
    ['identity' => 'student_jhs', 'password' => 'password123', 'portal' => 'student', 'expected_role' => 'student'],
    ['identity' => '2026-JHS-0001', 'password' => 'password123', 'portal' => 'student', 'expected_role' => 'student'],
];

echo "=== TESTING COMPREHENSIVE AUTHENTICATION & ALIASES ===\n\n";

$db = App\Config\Database::getConnection();

foreach ($testAccounts as $t) {
    $identity = $t['identity'];
    $password = $t['password'];
    
    $stmt = $db->prepare("
        SELECT u.*, r.name as role_name, r.slug as role_slug
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.username = :ident1 
           OR u.email = :ident2 
           OR u.student_id = :ident3
           OR (r.slug = :ident4 AND r.slug IN ('admin', 'coordinator', 'registrar', 'treasury', 'records'))
        LIMIT 1
    ");
    $stmt->execute([
        'ident1' => $identity,
        'ident2' => $identity,
        'ident3' => $identity,
        'ident4' => $identity
    ]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "❌ [FAILED - User Not Found] Input: '{$identity}'\n";
        continue;
    }
    
    if (!password_verify($password, $user['password'])) {
        echo "❌ [FAILED - Password Mismatch] User: '{$user['username']}', Input: '{$identity}'\n";
        continue;
    }
    
    if ($user['role_slug'] === $t['expected_role']) {
        echo "✅ [SUCCESS] Input: '{$identity}' -> Matched User: '{$user['username']}' (Role: {$user['role_name']})\n";
    } else {
        echo "⚠️ [ROLE MISMATCH] Input: '{$identity}' -> Matched: '{$user['username']}', Role: {$user['role_slug']} vs expected {$t['expected_role']}\n";
    }
}
