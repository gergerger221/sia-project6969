<?php
// scratch/setup_records_tables.php
$pdo = new PDO("mysql:host=localhost;dbname=sia_highschool_db;charset=utf8mb4", 'root', '');

// 1. Create student_records table if not exists
$pdo->exec("
CREATE TABLE IF NOT EXISTS `student_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `lrn` VARCHAR(30) NULL,
  `school_year_id` INT NOT NULL,
  `grade_level_id` INT NOT NULL,
  `strand_id` INT NULL,
  `section_id` INT NULL,
  `general_average` DECIMAL(5,2) DEFAULT 0.00,
  `previous_school_f137_status` ENUM('Received / Verified','Pending Previous School','Not Required') DEFAULT 'Received / Verified',
  `promotion_status` ENUM('Promoted','Conditional','Retained','Graduated','Under Evaluation') DEFAULT 'Under Evaluation',
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_stud_sy` (`student_id`, `school_year_id`),
  KEY `idx_sec` (`section_id`),
  KEY `idx_gl` (`grade_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// 2. Create student_grades table if not exists
$pdo->exec("
CREATE TABLE IF NOT EXISTS `student_grades` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `school_year_id` INT NOT NULL,
  `semester` ENUM('1st Semester','2nd Semester','Full Year') DEFAULT 'Full Year',
  `q1` DECIMAL(5,2) NULL,
  `q2` DECIMAL(5,2) NULL,
  `q3` DECIMAL(5,2) NULL,
  `q4` DECIMAL(5,2) NULL,
  `final_grade` DECIMAL(5,2) NULL,
  `remarks` ENUM('Passed','Failed','Incomplete','Ongoing') DEFAULT 'Ongoing',
  `encoded_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_stud_sub_sy_sem` (`student_id`, `subject_id`, `school_year_id`, `semester`),
  KEY `idx_stud` (`student_id`),
  KEY `idx_sub` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

// 3. Seed student_records for currently enrolled students
$enrolled = $pdo->query("
    SELECT e.student_id, e.school_year_id, e.grade_level_id, e.strand_id, e.section_id, a.lrn
    FROM enrollments e
    LEFT JOIN admission_applications a ON e.application_id = a.id
    WHERE e.status = 'Officially Enrolled' AND e.student_id IS NOT NULL
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($enrolled as $enr) {
    $chk = $pdo->prepare("SELECT id FROM student_records WHERE student_id = ? AND school_year_id = ?");
    $chk->execute([$enr['student_id'], $enr['school_year_id']]);
    if (!$chk->fetch()) {
        $ins = $pdo->prepare("
            INSERT INTO student_records (student_id, lrn, school_year_id, grade_level_id, strand_id, section_id, general_average, promotion_status)
            VALUES (?, ?, ?, ?, ?, ?, 88.50, 'Promoted')
        ");
        $ins->execute([
            $enr['student_id'],
            $enr['lrn'],
            $enr['school_year_id'],
            $enr['grade_level_id'],
            $enr['strand_id'],
            $enr['section_id']
        ]);
    }
}

echo "student_records and student_grades setup successfully!\n";
