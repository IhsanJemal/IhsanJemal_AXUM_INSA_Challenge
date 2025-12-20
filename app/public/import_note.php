<?php
require_once __DIR__ . '/../src/bootstrap.php';

header("Content-Type: application/json");

// Validate session
$sid = $_COOKIE['SID'] ?? '';
$user = null;

if ($sid) {
    $json = $redis->get("session_$sid");
    if ($json) {
        $user = json_decode($json, true);
    }
}

if (!$user) {
    echo json_encode(["status" => "error", "msg" => "Not logged in"]);
    exit;
}

$owner = $user['user'];
$url = trim($_POST['url'] ?? '');

if ($url === '') {
    echo json_encode(["status" => "error", "msg" => "No URL provided"]);
    exit;
}

// Perform SSRF-like fetch
$options = [
    "http" => ["method" => "GET", "timeout" => 6, "follow_location" => true]
];
$ctx = stream_context_create($options);
$content = @file_get_contents($url, false, $ctx);

if ($content === false) {
    echo json_encode([
        "status" => "error",
        "msg" => "Failed to fetch content"
    ]);
    exit;
}

// Extract readable text
$clean = trim(strip_tags($content));
if ($clean === '') {
    $clean = "No readable text extracted from URL: $url";
}

// Save to database
$stmt = $pdo->prepare("INSERT INTO notes (owner, content) VALUES (?, ?)");
$stmt->execute([$owner, $clean]);

echo json_encode([
    "status" => "ok",
    "msg" => "Imported successfully",
    "url" => $url,
    "length" => strlen($clean)
]);
exit;