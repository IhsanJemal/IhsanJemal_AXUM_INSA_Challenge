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

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    header('Location: profile.php');
    exit;
}

// Ensure secure upload directory OUTSIDE web root
$uploadDir = '/var/www/data/avatars';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Strong MIME check (NO extension fallback)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($_FILES['avatar']['tmp_name']);
$allowed = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg'
];

if (!isset($allowed[$mime])) {
    header('Location: profile.php');
    exit;
}

// File size limit (2MB)
if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
    header('Location: profile.php');
    exit;
}

// Generate safe filename
$ext = $allowed[$mime];
$targetName = bin2hex(random_bytes(16)) . '.' . $ext;
$targetPath = $uploadDir . '/' . $targetName;

if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
    header('Location: profile.php');
    exit;
}

// Store a SAFE public path (served by Apache statically)
$relative = 'serve_avatar.php?file=' . urlencode($targetName);

// Update DB
$stmt = $GLOBALS['pdo']->prepare("UPDATE users SET avatar_path = ? WHERE username = ?");
$stmt->execute([$relative, $user['user']]);

header('Location: profile.php');
exit;
