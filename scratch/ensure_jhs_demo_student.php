<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$jhsUser = $db->query("SELECT * FROM users WHERE student_id = '2026-JHS-0001' OR username = 'student_jhs'")->fetch();
$hash = password_hash('password123', PASSWORD_BCRYPT);
if (!$jhsUser) {
    $db->prepare("
        INSERT INTO users (role_id, username, email, password, student_id, status)
        VALUES (7, 'student_jhs', 'jhs.student@student.jjkings.edu.ph', :p, '2026-JHS-0001', 'Active')
    ")->execute(['p' => $hash]);
    $uid = $db->lastInsertId();
    $db->prepare("
        INSERT INTO user_profiles (user_id, first_name, middle_name, last_name, contact_number)
        VALUES (:uid, 'Julian', 'Felipe', 'Cruz', '09171234567')
    ")->execute(['uid' => $uid]);
    echo "Created JHS Demo Student User (student_jhs / 2026-JHS-0001)\n";
} else {
    $db->prepare("UPDATE users SET password = :p, student_id = '2026-JHS-0001', status = 'Active' WHERE id = :id")->execute(['p' => $hash, 'id' => $jhsUser['id']]);
    echo "Updated JHS Demo Student User\n";
}
