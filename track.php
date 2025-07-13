<?php
session_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['prompt'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$prompt = trim($input['prompt']);
if ($prompt === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Empty prompt']);
    exit;
}

$statsFile = __DIR__ . '/data/stats.json';
$promptsFile = __DIR__ . '/data/prompts.txt';

$stats = json_decode(file_get_contents($statsFile), true) ?? [
    'totalGenerated' => 0,
    'successfulCount' => 0,
    'failedCount' => 0
];

// Increment total generated count
$stats['totalGenerated']++;

// Save prompt to text file
file_put_contents($promptsFile, date('Y-m-d H:i:s') . " - " . $prompt . PHP_EOL, FILE_APPEND);

// Update session generation count & check limit
if (!isset($_SESSION['gen_count'])) {
    $_SESSION['gen_count'] = 0;
    $_SESSION['gen_date'] = date('Y-m-d');
} elseif ($_SESSION['gen_date'] !== date('Y-m-d')) {
    $_SESSION['gen_count'] = 0;
    $_SESSION['gen_date'] = date('Y-m-d');
}

if (!($_SESSION['is_vip'] ?? false)) {
    $_SESSION['gen_count']++;
    if ($_SESSION['gen_count'] > 20) {
        http_response_code(429);
        echo json_encode(['error' => 'Free generation limit reached']);
        exit;
    }
}

file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
