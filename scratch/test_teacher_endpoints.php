<?php
// Test Teacher Authentication and Endpoints

$baseUrl = 'http://localhost/sia-project/backend/api/index.php';

function request($route, $method = 'GET', $data = null, $token = null) {
    global $baseUrl;
    $url = $baseUrl . '?route=' . $route;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['code' => $code, 'data' => json_decode($res, true)];
}

echo "=== TEST 1: Teacher Login (username: prof_delacruz) ===\n";
$login = request('auth/login', 'POST', [
    'username' => 'prof_delacruz',
    'password' => 'password123',
    'portal_type' => 'staff'
]);
echo "Status Code: {$login['code']}\n";
print_r($login['data']);

if (empty($login['data']['data']['token'])) {
    echo "FAILED: Could not login as teacher.\n";
    exit(1);
}
$token = $login['data']['data']['token'];

echo "\n=== TEST 2: Teacher Login with role slug alias (username: teacher) ===\n";
$login2 = request('auth/login', 'POST', [
    'username' => 'teacher',
    'password' => 'password123',
    'portal_type' => 'staff'
]);
echo "Status Code: {$login2['code']}\n";
echo "Role: " . ($login2['data']['data']['role_slug'] ?? 'N/A') . "\n";
$token = $login2['data']['data']['token'] ?? $token;

echo "\n=== TEST 3: Teacher Dashboard API ===\n";
$dash = request('teacher/dashboard', 'GET', null, $token);
echo "Status Code: {$dash['code']}\n";
print_r($dash['data']);

$classes = $dash['data']['data']['classes'] ?? [];
if (!empty($classes)) {
    $firstClass = $classes[0];
    $secId = $firstClass['section_id'];
    $subId = $firstClass['subject_id'];
    echo "Testing Class: Section {$secId} ({$firstClass['section_name']}), Subject {$subId} ({$firstClass['subject_name']})\n";

    echo "\n=== TEST 4: Class Students & Electronic Class Record ===\n";
    $classStudents = request("teacher/class-students&section_id={$secId}&subject_id={$subId}", 'GET', null, $token);
    echo "Status Code: {$classStudents['code']}\n";
    echo "Total Students in Class: " . ($classStudents['data']['data']['total_students'] ?? 0) . "\n";

    $students = $classStudents['data']['data']['students'] ?? [];
    if (!empty($students)) {
        $st1 = $students[0];
        echo "Sample student: {$st1['full_name']} (ID: {$st1['student_id']})\n";

        echo "\n=== TEST 5: Save Grades Batch ===\n";
        $saveGrades = request('teacher/save-grades', 'POST', [
            'section_id' => $secId,
            'subject_id' => $subId,
            'grades' => [
                [
                    'student_id' => $st1['student_id'],
                    'q1' => 88.50,
                    'q2' => 90.00,
                    'q3' => 91.00,
                    'q4' => 89.50
                ]
            ]
        ], $token);
        echo "Status Code: {$saveGrades['code']}\n";
        print_r($saveGrades['data']);
    }
}

echo "\n=== TEST 6: Advisory Section API ===\n";
$advisory = request('teacher/advisory-section', 'GET', null, $token);
echo "Status Code: {$advisory['code']}\n";
echo "Has Advisory: " . ($advisory['data']['data']['has_advisory'] ? 'YES' : 'NO') . "\n";
echo "Total Learners: " . ($advisory['data']['data']['total_learners'] ?? 0) . "\n";

echo "\n=== ALL TEACHER BACKEND TESTS COMPLETE ===\n";
