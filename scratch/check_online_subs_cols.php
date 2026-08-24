<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$cols = $db->query("DESCRIBE online_payment_submissions")->fetchAll();
foreach ($cols as $c) {
    echo "{$c['Field']} ({$c['Type']})\n";
}
