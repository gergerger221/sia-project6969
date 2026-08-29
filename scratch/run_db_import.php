<?php
// scratch/run_db_import.php
declare(strict_types=1);

$sqlPath = 'c:/xampp/htdocs/sia-project/sia_highschool_complete_database.sql';
if (!file_exists($sqlPath)) {
    echo "SQL file not found at $sqlPath\n";
    exit(1);
}

$host = 'localhost';
$user = 'root';
$pass = '';

echo "Connecting to MySQL...\n";
$pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

echo "Importing complete database from $sqlPath...\n";
$sql = file_get_contents($sqlPath);

// Execute the SQL multi-queries
$pdo->exec($sql);

echo "Database imported successfully!\n\n";

// Switch to database and count records
$pdo->exec("USE `sia_highschool_db`");

$tables = [
    'users',
    'roles',
    'school_years',
    'grade_levels',
    'strands',
    'curriculum_subjects',
    'sections',
    'schedules',
    'school_events',
    'admission_applications',
    'student_demographics',
    'enrollments',
    'student_assessments',
    'student_payments',
    'online_payment_submissions',
    'student_records',
    'student_grades',
    'document_requests',
    'system_audit_logs'
];

echo "=== DATABASE TABLE RECORD COUNTS ===\n";
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$t`");
        $count = $stmt->fetchColumn();
        echo sprintf(" %-30s : %d records\n", $t, $count);
    } catch (Exception $e) {
        echo sprintf(" %-30s : [ERROR: %s]\n", $t, $e->getMessage());
    }
}
