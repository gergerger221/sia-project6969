<?php
// scratch/compare_changes.php
declare(strict_types=1);

$dirA = 'C:/xampp/htdocs/changes';
$dirB = 'C:/xampp/htdocs/sia-project';

function getFiles(string $dir, string $base = ''): array {
    $results = [];
    $scan = scandir($dir);
    foreach ($scan as $item) {
        if ($item === '.' || $item === '..' || $item === '.git' || $item === 'node_modules' || $item === '.system_generated' || $item === 'dist') {
            continue;
        }
        $fullPath = $dir . '/' . $item;
        $relPath = $base ? $base . '/' . $item : $item;
        if (is_dir($fullPath)) {
            $results = array_merge($results, getFiles($fullPath, $relPath));
        } else {
            $results[$relPath] = [
                'size' => filesize($fullPath),
                'md5'  => md5_file($fullPath),
                'time' => filemtime($fullPath)
            ];
        }
    }
    return $results;
}

echo "Scanning C:/xampp/htdocs/changes...\n";
$filesA = getFiles($dirA);

echo "Scanning C:/xampp/htdocs/sia-project...\n";
$filesB = getFiles($dirB);

$onlyInA = [];
$onlyInB = [];
$modified = [];
$identical = [];

foreach ($filesA as $path => $infoA) {
    if (!isset($filesB[$path])) {
        $onlyInA[] = $path;
    } else {
        if ($infoA['md5'] !== $filesB[$path]['md5']) {
            $modified[] = [
                'path' => $path,
                'sizeA' => $infoA['size'],
                'sizeB' => $filesB[$path]['size'],
                'timeA' => date('Y-m-d H:i:s', $infoA['time']),
                'timeB' => date('Y-m-d H:i:s', $filesB[$path]['time'])
            ];
        } else {
            $identical[] = $path;
        }
    }
}

foreach ($filesB as $path => $infoB) {
    if (!isset($filesA[$path])) {
        $onlyInB[] = $path;
    }
}

echo "\n=== SUMMARY OF DIFFERENCES ===\n";
echo "Files only in 'changes': " . count($onlyInA) . "\n";
echo "Files only in 'sia-project': " . count($onlyInB) . "\n";
echo "Modified files (content differs): " . count($modified) . "\n";
echo "Identical files: " . count($identical) . "\n";

echo "\n--- FILES ONLY IN 'changes' ---\n";
foreach ($onlyInA as $f) {
    echo " + $f\n";
}

echo "\n--- MODIFIED FILES (Differences) ---\n";
foreach ($modified as $m) {
    echo " * {$m['path']} (Changes: {$m['sizeA']}B @ {$m['timeA']} vs Current: {$m['sizeB']}B @ {$m['timeB']})\n";
}
