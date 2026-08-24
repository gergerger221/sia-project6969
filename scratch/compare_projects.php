<?php
$dirA = 'c:/xampp/htdocs/sia-project';
$dirB = 'c:/xampp/htdocs/sia-project-ui';

function scanAllFiles($dir) {
    $files = [];
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iter as $item) {
        $path = $item->getPathname();
        if (strpos($path, 'node_modules') !== false || strpos($path, '.git') !== false || strpos($path, 'dist') !== false) continue;
        $rel = str_replace('\\', '/', substr($path, strlen($dir) + 1));
        $files[$rel] = md5_file($path);
    }
    return $files;
}

$filesA = scanAllFiles($dirA);
$filesB = scanAllFiles($dirB);

$onlyInA = array_diff_key($filesA, $filesB);
$onlyInB = array_diff_key($filesB, $filesA);
$modified = [];

foreach ($filesA as $rel => $hashA) {
    if (isset($filesB[$rel]) && $filesB[$rel] !== $hashA) {
        $modified[] = $rel;
    }
}

echo "=== ONLY IN sia-project (" . count($onlyInA) . ") ===\n";
print_r(array_keys($onlyInA));

echo "\n=== ONLY IN sia-project-ui (" . count($onlyInB) . ") ===\n";
print_r(array_keys($onlyInB));

echo "\n=== MODIFIED / DIFFERENT FILES (" . count($modified) . ") ===\n";
print_r($modified);
