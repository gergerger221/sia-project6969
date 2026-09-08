<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = App\Config\Database::getConnection();
$sections = $db->query("SELECT id, name, grade_level_id, adviser_id FROM sections")->fetchAll();
echo "Total sections: " . count($sections) . "\n";
foreach ($sections as $s) {
    echo "ID: {$s['id']} | Name: {$s['name']} | Adviser ID: {$s['adviser_id']}\n";
}
$users = $db->query("SELECT u.id, u.username, r.name as role FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'teacher'")->fetchAll();
echo "\nTeachers:\n";
foreach ($users as $u) {
    echo "ID: {$u['id']} | Username: {$u['username']} | Role: {$u['role']}\n";
}
