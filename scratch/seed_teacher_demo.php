<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = \App\Config\Database::getConnection();

// Hash password123
$hash = password_hash('password123', PASSWORD_DEFAULT);

// Update teachers password
$db->query("UPDATE users SET password = '$hash' WHERE role_id = (SELECT id FROM roles WHERE slug = 'teacher')");

// Ensure prof_delacruz is adviser of section 1 (Grade 7 - Diamond) or Grade 11 - STEM A
$delacruz = $db->query("SELECT id FROM users WHERE username = 'prof_delacruz'")->fetch(PDO::FETCH_ASSOC);
if ($delacruz) {
    $teacherId = $delacruz['id'];
    // Make prof_delacruz adviser of section 1
    $db->query("UPDATE sections SET adviser_id = $teacherId WHERE id = 1");
    // Ensure prof_delacruz has schedules assigned
    $schedCount = $db->query("SELECT COUNT(*) FROM schedules WHERE teacher_id = $teacherId")->fetchColumn();
    echo "prof_delacruz (ID $teacherId) has $schedCount schedule periods assigned.\n";
    if ($schedCount == 0) {
        // Assign some section 1 schedules to prof_delacruz
        $db->query("UPDATE schedules SET teacher_id = $teacherId WHERE section_id = 1 LIMIT 5");
        echo "Assigned 5 schedules in section 1 to prof_delacruz.\n";
    }
}

echo "Teachers password set to password123 successfully.\n";
