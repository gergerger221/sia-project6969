<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$cols = $db->query("DESCRIBE enrollments")->fetchAll();
foreach ($cols as $c) {
    echo "{$c['Field']} ({$c['Type']}) Null: {$c['Null']} Default: {$c['Default']}\n";
}
