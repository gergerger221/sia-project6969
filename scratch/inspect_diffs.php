<?php
// scratch/inspect_diffs.php
declare(strict_types=1);

$dirA = 'C:/xampp/htdocs/changes';
$dirB = 'C:/xampp/htdocs/sia-project';

$filesToCheck = [
    'backend/api/index.php',
    'backend/config/MailConfig.php',
    'backend/controllers/AdminController.php',
    'backend/controllers/AdmissionController.php',
    'backend/controllers/AuthController.php',
    'backend/controllers/TreasuryController.php',
    'backend/helpers/FileUpload.php',
    'backend/helpers/Mailer.php',
    'frontend/src/App.vue',
    'frontend/src/router/index.js',
    'frontend/src/views/public/HomeView.vue',
    'frontend/src/views/public/LoginView.vue',
    'frontend/src/views/public/RegisterView.vue',
    'frontend/src/views/public/StaffLoginView.vue',
    'frontend/src/views/admin/AdminDashboardView.vue',
    'frontend/src/views/coordinator/CoordinatorDashboardView.vue',
    'frontend/src/views/registrar/RegistrarDashboardView.vue',
    'frontend/src/views/treasury/TreasuryDashboardView.vue',
    'frontend/src/views/records/RecordsDashboardView.vue',
    'frontend/src/views/student/StudentDashboardView.vue',
];

foreach ($filesToCheck as $rel) {
    $pathA = "$dirA/$rel";
    $pathB = "$dirB/$rel";
    if (!file_exists($pathA) || !file_exists($pathB)) {
        echo "Missing file: $rel\n";
        continue;
    }
    $linesA = file($pathA);
    $linesB = file($pathB);
    echo "========================================================\n";
    echo "DIFF FOR: $rel (Changes: " . count($linesA) . " lines vs Sia-Project: " . count($linesB) . " lines)\n";
    echo "========================================================\n";

    // Simple line diff summary
    $diffOutput = [];
    exec("git diff --no-index \"$pathB\" \"$pathA\"", $diffOutput);
    echo implode("\n", array_slice($diffOutput, 0, 40)) . "\n";
    if (count($diffOutput) > 40) {
        echo "... [truncated " . (count($diffOutput) - 40) . " diff lines]\n";
    }
    echo "\n";
}
