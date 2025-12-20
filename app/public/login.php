<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password_hash'])) {
        $sid = bin2hex(random_bytes(8));
        $user_data = json_encode([
            'user' => $row['username'],
            'role' => $row['role']
        ]);
        $redis->set("session_$sid", $user_data);
        setcookie("SID", $sid, 0, "/");
        header('Location: profile.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<?php require_once __DIR__ . '/../templates/header.php'; ?>

<h3>Login</h3>
<?php if (isset($_GET['registered'])): ?><p style="color:green;">Registered successfully. Please log in.</p><?php endif; ?>
<form method="post">
  <input name="username" placeholder="username" required>
  <input type="password" name="password" placeholder="password" required>
  <button>Login</button>
</form>
<?php if (!empty($error)): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<p>No account? <a href="register.php">Register</a></p>
<?php require_once __DIR__ . '/../templates/footer.php'; ?> 
