<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$roles = $db->query("SELECT * FROM roles")->fetchAll();
foreach ($roles as $r) {
    echo "ID: {$r['id']} | Name: {$r['name']} | Slug: {$r['slug']}\n";
}
