<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = App\Config\Database::getConnection();
echo "=== Database Connection Status: CONNECTED ===\n";
echo "Database Name: " . $db->query("SELECT DATABASE()")->fetchColumn() . "\n\n";

echo sprintf("%-4s | %-20s | %-32s | %-12s | %-12s | %-15s\n", "ID", "Username", "Email", "Role", "Pass=pass123", "Student ID");
echo str_repeat("-", 105) . "\n";

$stmt = $db->query("
    SELECT u.id, u.username, u.email, u.student_id, u.password, r.slug, r.name 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    ORDER BY u.id ASC
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $match = password_verify('password123', $row['password']) ? 'YES' : 'NO';
    echo sprintf(
        "%-4d | %-20s | %-32s | %-12s | %-12s | %-15s\n",
        $row['id'],
        $row['username'],
        $row['email'],
        $row['slug'],
        $match,
        $row['student_id'] ?? 'null'
    );
}
