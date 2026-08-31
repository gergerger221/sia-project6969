<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = \App\Config\Database::getConnection();

echo "=== USER PROFILES COLUMNS ===\n";
print_r($db->query("DESCRIBE user_profiles")->fetchAll(PDO::FETCH_ASSOC));

echo "=== SAMPLE TEACHERS ===\n";
$q = $db->query("SELECT u.id, u.username, u.email, up.*, r.slug as role_slug 
                 FROM users u 
                 JOIN roles r ON u.role_id = r.id 
                 LEFT JOIN user_profiles up ON u.id = up.user_id 
                 WHERE r.slug = 'teacher' LIMIT 5");
print_r($q->fetchAll(PDO::FETCH_ASSOC));

echo "=== TEACHER ID USAGE IN SCHEDULES ===\n";
$s = $db->query("SELECT DISTINCT teacher_id FROM schedules WHERE teacher_id IS NOT NULL LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
print_r($s);
