<?php
$sourceBase = 'c:/xampp/htdocs/sia-project-ui';
$targetBase = 'c:/xampp/htdocs/sia-project';

$views = [
    'frontend/src/views/applicant/AdmissionProcedureView.vue',
    'frontend/src/views/treasury/TreasuryDashboardView.vue',
    'backend/controllers/TreasuryController.php'
];

foreach ($views as $f) {
    $src = "{$sourceBase}/{$f}";
    $dst = "{$targetBase}/{$f}";
    if (file_exists($src)) {
        copy($src, $dst);
        echo "Copied base: {$f} (" . filesize($dst) . " bytes)\n";
    }
}
