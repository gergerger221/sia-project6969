<?php
$testLogins = [
    ['user' => 'admin', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'registrar', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'maria_registrar', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'coordinator', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'maria_coordinator', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'treasury', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'maria_treasury', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'records', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'maria_records', 'pass' => 'password123', 'portal' => 'staff'],
    ['user' => 'student2026', 'pass' => 'password123', 'portal' => 'student'],
    ['user' => '2026-JHS-0001', 'pass' => 'password123', 'portal' => 'student']
];

echo "=== TESTING API HTTP LOGIN ENDPOINTS ON APACHE ===\n\n";

foreach ($testLogins as $t) {
    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode([
                'username' => $t['user'],
                'password' => $t['pass'],
                'portal_type' => $t['portal']
            ]),
            'ignore_errors' => true,
            'timeout' => 5
        ]
    ]);
    
    $raw = @file_get_contents('http://localhost/sia-project2/backend/api/index.php?route=auth/login', false, $ctx);
    $res = json_decode($raw, true);
    
    if ($res && !empty($res['success'])) {
        echo "✅ [HTTP 200 OK] Input: '{$t['user']}' -> Logged in as [{$res['data']['role_name']}] (User: {$res['data']['username']})\n";
    } else {
        echo "❌ [HTTP FAIL] Input: '{$t['user']}' -> " . ($res['message'] ?? 'Connection error') . "\n";
    }
}
