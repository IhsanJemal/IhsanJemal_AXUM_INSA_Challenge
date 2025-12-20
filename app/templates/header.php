<?php
require_once __DIR__ . '/../src/bootstrap.php'; // adjust path if needed

$sid = $_COOKIE['SID'] ?? '';
$user = null;

if ($sid) {
    $user_json = $redis->get("session_$sid");
    if ($user_json) {
        $user = json_decode($user_json, true);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Vulnerable App</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="script.js" defer></script>
</head>
<body>
    
  <nav>
    <a href="profile.php">Profile</a>
    <a href="notes.php">Notes</a>

    <?php
    // Only show Admin link if user is logged in AND role is admin
    if (isset($user) && ($user['role'] ?? '') === 'admin'): ?>
      <a href="admin.php">Admin</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
