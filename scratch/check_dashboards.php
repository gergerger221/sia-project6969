<?php
$views = [
    'frontend/src/views/admin/AdminDashboardView.vue',
    'frontend/src/views/coordinator/CoordinatorDashboardView.vue',
    'frontend/src/views/registrar/RegistrarDashboardView.vue',
    'frontend/src/views/treasury/TreasuryDashboardView.vue',
    'frontend/src/views/records/RecordsDashboardView.vue',
    'frontend/src/views/student/StudentDashboardView.vue',
    'frontend/src/views/applicant/AdmissionProcedureView.vue'
];

foreach ($views as $f) {
    $pathA = "c:/xampp/htdocs/sia-project/{$f}";
    $pathB = "c:/xampp/htdocs/sia-project-ui/{$f}";
    
    echo "==================================================\n";
    echo "FILE: {$f}\n";
    echo "Size in A (current): " . (file_exists($pathA) ? filesize($pathA) : 0) . " bytes\n";
    echo "Size in B (ui):      " . (file_exists($pathB) ? filesize($pathB) : 0) . " bytes\n";
}
