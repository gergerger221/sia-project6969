<?php
require_once __DIR__ . '/../backend/config/Database.php';
$db = (new \App\Config\Database())->getConnection();

// Check if 2027-2028 already exists
$stmt = $db->prepare("SELECT id FROM school_years WHERE code = '2027-2028'");
$stmt->execute();
if (!$stmt->fetch()) {
    $ins = $db->prepare("
        INSERT INTO school_years (code, name, start_date, end_date, active_semester, is_active, is_locked, curriculum_locked, created_at)
        VALUES ('2027-2028', 'School Year 2027-2028', '2027-08-01', '2028-05-31', '1st Semester', 0, 1, 0, NOW())
    ");
    $ins->execute();
    echo "Inserted School Year 2027-2028 successfully.\n";
} else {
    echo "School Year 2027-2028 already exists.\n";
}

$stmt = $db->query("SELECT * FROM school_years ORDER BY id ASC");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
