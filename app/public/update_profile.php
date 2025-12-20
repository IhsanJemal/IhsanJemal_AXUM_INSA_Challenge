<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Require session
$sid = $_COOKIE['SID'] ?? '';
$user = null;
if ($sid) {
    $user_json = $redis->get("session_$sid");
    if ($user_json) $user = json_decode($user_json, true);
}
if (!$user) {
    header('Location: login.php');
    exit;
}

// Read and validate input
$display_name = trim($_POST['display_name'] ?? '');
$bio = trim($_POST['bio'] ?? '');

// Length / sanity limits
if (strlen($display_name) > 50) $display_name = substr($display_name, 0, 50);
if (strlen($bio) > 500) $bio = substr($bio, 0, 500);

// Sanitize HTML output
$display_name = htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8');
$bio = htmlspecialchars($bio, ENT_QUOTES, 'UTF-8');

// Update only allowed fields
$stmt = $GLOBALS['pdo']->prepare("
    UPDATE users SET display_name = ?, bio = ? WHERE username = ?
");
$stmt->execute([$display_name, $bio, $user['user']]);

header('Location: profile.php');
exit;
