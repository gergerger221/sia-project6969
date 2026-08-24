<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();
$users = $db->query("SELECT id, username, password FROM users WHERE username IN ('admin', 'coordinator', 'registrar', 'treasury', 'records', 'student2026')")->fetchAll();
$hash = password_hash('password123', PASSWORD_BCRYPT);
foreach ($users as $u) {
    $matches = password_verify('password123', $u['password']);
    echo "User: {$u['username']} | password123 matches: " . ($matches ? 'YES' : 'NO') . "\n";
    if (!$matches) {
        $db->prepare("UPDATE users SET password = :p WHERE id = :id")->execute(['p' => $hash, 'id' => $u['id']]);
        echo " -> Updated {$u['username']} password to password123\n";
    }
}
