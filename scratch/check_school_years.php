<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$stmt = $db->query("SELECT * FROM school_years");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
