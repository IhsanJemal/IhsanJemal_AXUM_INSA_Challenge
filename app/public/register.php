<?php
require_once __DIR__ . '/../src/bootstrap.php';

$error = ""; // make sure it's always defined

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $display_name = trim($_POST['display_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // ----- VALIDATION -----

    // Username required
    if ($username === '' || $password === '') {
        $error = "Username and password are required.";
    }
    // Username format: letters, numbers, underscores, 3–20 chars
    elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username must be 3-20 characters and contain only letters, numbers or _";
    }
    // Password minimum length
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must be 8+ chars with upper, lower, number.";
    }

    // If no validation errors, process registration
    if ($error === "") {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Sanitize optional fields
            $display_name = htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8');
            $bio = htmlspecialchars($bio, ENT_QUOTES, 'UTF-8');

            $stmt = $GLOBALS['pdo']->prepare("
                INSERT INTO users (username, password_hash, display_name, bio)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$username, $hash, $display_name, $bio]);

            header('Location: login.php?registered=1');
            exit;

        } catch (Exception $e) {
            $error = "Registration failed (username may already exist).";
        }
    }
}

?>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<h3>Create account</h3>
<form method="post">
  <input name="username" placeholder="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
  <input type="password" name="password" placeholder="password" required>
  <input name="display_name" placeholder="display name" value="<?= htmlspecialchars($_POST['display_name'] ?? '') ?>">
  <textarea name="bio" placeholder="bio" rows="3"><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
  <button>Register</button>
</form>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>Already have an account? <a href="login.php">Login</a></p>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
