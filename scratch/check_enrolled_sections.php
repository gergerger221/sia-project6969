<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = \App\Config\Database::getConnection();

$enr = $db->query("SELECT id, student_no, section_id, status FROM enrollments")->fetchAll(PDO::FETCH_ASSOC);
print_r($enr);
