<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();
$stmt = $db->query("SELECT * FROM audit_logs WHERE action IN ('EMAIL_DISPATCH', 'APPLICANT_REGISTER') ORDER BY id DESC LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
