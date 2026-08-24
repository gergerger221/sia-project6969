<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();
$indices = $db->query("SHOW INDEX FROM student_records")->fetchAll(PDO::FETCH_ASSOC);
print_r($indices);
