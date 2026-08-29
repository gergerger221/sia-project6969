<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();

echo "=== UPDATING STAFF USERNAMES IN DATABASE ===\n";

$updates = [
    2 => 'coordinator',
    3 => 'registrar',
    4 => 'treasury',
    5 => 'records'
];

foreach ($updates as $userId => $newUsername) {
    $stmt = $db->prepare("UPDATE users SET username = :uname WHERE id = :id");
    $stmt->execute(['uname' => $newUsername, 'id' => $userId]);
    echo "Updated User ID {$userId} to username '{$newUsername}'\n";
}

echo "\n=== CURRENT USERS LIST ===\n";
$stmt = $db->query("
    SELECT u.id, u.username, u.email, r.name as role_name, r.slug as role_slug 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    WHERE u.id <= 5
");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("ID: %d | Username: %-15s | Email: %-25s | Role: %s\n", $r['id'], $r['username'], $r['email'], $r['role_name']);
}
