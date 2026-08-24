<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$users = $db->query("
    SELECT u.id, u.username, u.email, u.student_id, u.status, r.name as role_name, r.slug as role_slug
    FROM users u
    JOIN roles r ON u.role_id = r.id
    LIMIT 20
")->fetchAll();

foreach ($users as $u) {
    echo "ID: {$u['id']} | User: {$u['username']} | Role: {$u['role_slug']} | Student ID: {$u['student_id']}\n";
}
