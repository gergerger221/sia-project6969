<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();
$cols = $db->query("DESCRIBE student_assessments")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
