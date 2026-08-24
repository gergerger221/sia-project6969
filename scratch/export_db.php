<?php
require_once __DIR__ . '/../backend/config/Database.php';

$db = (new \App\Config\Database())->getConnection();
$tables = [
    'roles',
    'users',
    'user_profiles',
    'audit_logs',
    'school_years',
    'tracks',
    'strands',
    'grade_levels',
    'subjects',
    'sections',
    'section_schedules',
    'applications',
    'application_documents',
    'enrollments',
    'enrolled_subjects',
    'fee_structures',
    'student_fees',
    'payments',
    'school_events',
    'document_requests'
];

$sql = "-- SIA High School Enrollment Management System Database Dump\n";
$sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $tbl) {
    // Check if table exists
    $check = $db->query("SHOW TABLES LIKE '{$tbl}'")->fetch();
    if (!$check) continue;

    $createRow = $db->query("SHOW CREATE TABLE `{$tbl}`")->fetch(PDO::FETCH_NUM);
    $sql .= "DROP TABLE IF EXISTS `{$tbl}`;\n";
    $sql .= $createRow[1] . ";\n\n";

    $rows = $db->query("SELECT * FROM `{$tbl}`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $cols = array_keys($rows[0]);
        $colNames = implode('`, `', $cols);
        $sql .= "INSERT INTO `{$tbl}` (`{$colNames}`) VALUES\n";
        $valsArr = [];
        foreach ($rows as $r) {
            $rowVals = [];
            foreach ($r as $val) {
                if ($val === null) {
                    $rowVals[] = 'NULL';
                } else {
                    $rowVals[] = $db->quote($val);
                }
            }
            $valsArr[] = "(" . implode(', ', $rowVals) . ")";
        }
        $sql .= implode(",\n", $valsArr) . ";\n\n";
    }
}

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents(__DIR__ . '/../backend/database/enrollment_system.sql', $sql);
echo "Exported database successfully to backend/database/enrollment_system.sql (" . strlen($sql) . " bytes)\n";
