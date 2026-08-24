<?php
$files = [
    'frontend/src/App.vue',
    'frontend/src/views/public/HomeView.vue',
    'frontend/src/views/public/LoginView.vue',
    'frontend/src/views/public/RegisterView.vue',
    'frontend/src/views/public/StaffLoginView.vue',
    'frontend/tailwind.config.js',
    'frontend/index.html'
];

foreach ($files as $f) {
    $pathA = "c:/xampp/htdocs/sia-project/{$f}";
    $pathB = "c:/xampp/htdocs/sia-project-ui/{$f}";
    
    echo "==================================================\n";
    echo "FILE: {$f}\n";
    echo "Exists in A: " . (file_exists($pathA) ? 'YES' : 'NO') . " (" . (file_exists($pathA) ? filesize($pathA) : 0) . " bytes)\n";
    echo "Exists in B: " . (file_exists($pathB) ? 'YES' : 'NO') . " (" . (file_exists($pathB) ? filesize($pathB) : 0) . " bytes)\n";
    echo "==================================================\n";
}
