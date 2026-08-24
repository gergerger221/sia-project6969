<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
