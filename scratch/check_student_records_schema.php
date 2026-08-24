<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();
$cols = $db->query("DESCRIBE student_records")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
