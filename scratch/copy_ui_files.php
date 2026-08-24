<?php
$sourceBase = 'c:/xampp/htdocs/sia-project-ui';
$targetBase = 'c:/xampp/htdocs/sia-project';

$filesToCopy = [
    'frontend/src/views/public/HomeView.vue',
    'frontend/src/views/public/StaffLoginView.vue',
    'frontend/src/views/public/RegisterView.vue',
    'frontend/src/views/public/LoginView.vue',
    'frontend/src/App.vue',
    'frontend/src/router/index.js',
    'frontend/index.html'
];

foreach ($filesToCopy as $f) {
    $src = "{$sourceBase}/{$f}";
    $dst = "{$targetBase}/{$f}";
    if (file_exists($src)) {
        $dir = dirname($dst);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        copy($src, $dst);
        echo "Copied: {$f} (" . filesize($dst) . " bytes)\n";
    } else {
        echo "NOT FOUND: {$src}\n";
    }
}
