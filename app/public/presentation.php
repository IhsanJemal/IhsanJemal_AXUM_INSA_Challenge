<?php
// presentation.php - Professional Security Presentation
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSRF → Redis → RCE: From Zero to System Compromise</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .slide {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 50px;
            margin: 20px auto;
            max-width: 1300px;
            box-shadow: 0 25px 75px rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.1);
            display: none;
            min-height: 80vh;
            position: relative;
        }
        
        .slide.active { 
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h1 { 
            font-size: 4em; 
            margin-bottom: 30px;
            color: #00d4ff;
            text-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
            font-family: 'Roboto Mono', monospace;
            text-align: center;
        }
        
        h2 { 
            color: #00ff9d; 
            margin: 30px 0 20px;
            border-bottom: 3px solid #00ff9d;
            padding-bottom: 15px;
            font-size: 2.5em;
        }
        
        h3 { 
            color: #ff6b9d; 
            margin: 25px 0 15px;
            font-size: 1.8em;
        }
        
        p {
            font-size: 1.3em;
            line-height: 1.7;
            margin: 15px 0;
        }
        
        .highlight-box {
            background: rgba(0, 212, 255, 0.1);
            border: 2px solid #00d4ff;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
        }
        
        .danger-box {
            background: rgba(255, 107, 157, 0.1);
            border: 2px solid #ff6b9d;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 10px 30px rgba(255, 107, 157, 0.2);
        }
        
        .success-box {
            background: rgba(0, 255, 157, 0.1);
            border: 2px solid #00ff9d;
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 10px 30px rgba(0, 255, 157, 0.2);
        }
        
        .code-block {
            background: #1a1a2e;
            border: 2px solid #00d4ff;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            font-family: 'Roboto Mono', monospace;
            font-size: 1.1em;
            overflow-x: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
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
            height: 5px;
            background: linear-gradient(90deg, #ff0080, #00d4ff, #00ff9d);
            z-index: 0;
            border-radius: 3px;
        }
        
        .step {
            background: #162447;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            width: 200px;
            position: relative;
            z-index: 1;
            border: 3px solid transparent;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        
        .step:hover {
            transform: translateY(-15px);
            border-color: #00ff9d;
            box-shadow: 0 20px 40px rgba(0, 255, 157, 0.4);
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
            margin: 0 auto 20px;
            font-weight: bold;
            font-size: 1.5em;
            box-shadow: 0 5px 15px rgba(255, 0, 128, 0.5);
        }
        
        .demo-link {
            display: inline-block;
            background: linear-gradient(45deg, #ff0080, #00d4ff);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            margin: 15px;
            font-weight: bold;
            font-size: 1.2em;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(255, 0, 128, 0.4);
        }
        
        .demo-link:hover {
            transform: scale(1.1);
            box-shadow: 0 10px 30px rgba(255, 0, 128, 0.6);
        }
        
        .nav-buttons {
            position: fixed;
            bottom: 40px;
            right: 40px;
            display: flex;
            gap: 20px;
            z-index: 1000;
        }
        
        .nav-btn {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            font-size: 2em;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav-btn:hover {
            background: rgba(0, 212, 255, 0.6);
            transform: scale(1.2);
            border-color: #00d4ff;
        }
        
        .slide-counter {
            position: fixed;
            bottom: 40px;
            left: 40px;
            font-size: 1.5em;
            color: #00d4ff;
            background: rgba(0, 0, 0, 0.5);
            padding: 15px 25px;
            border-radius: 50px;
            border: 2px solid rgba(0, 212, 255, 0.3);
        }
        
        .presenter-name {
            position: fixed;
            top: 30px;
            right: 30px;
            background: rgba(0, 0, 0, 0.5);
            padding: 15px 25px;
            border-radius: 50px;
            color: #00ff9d;
            font-size: 1.2em;
            border: 2px solid rgba(0, 255, 157, 0.3);
        }
        
        .live-badge {
            position: fixed;
            top: 30px;
            left: 30px;
            background: #ff0080;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 0, 128, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(255, 0, 128, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 0, 128, 0); }
        }
        
        .icon {
            font-size: 3em;
            margin: 20px;
            display: inline-block;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 30px 0;
        }
        
        ul, ol {
            font-size: 1.3em;
            line-height: 1.8;
            margin-left: 30px;
            margin-bottom: 20px;
        }
        
        li {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="presenter-name">👨‍💻 Isan Jemal</div>
<div class="live-badge">🔴 LIVE DEMO</div>

<!-- Slide 1: Title -->
<div class="slide active" id="slide1">
    <h1>SSRF → Redis → RCE</h1>
    <h2 style="text-align: center;">From Zero to System Compromise</h2>
    
    <div style="text-align: center; margin: 60px 0;">
        <div style="font-size: 6em; margin-bottom: 40px; filter: drop-shadow(0 0 20px rgba(0,212,255,0.7));">
            🔥🔄💾⚡
        </div>
        <p style="font-size: 2em; color: #00ff9d; max-width: 900px; margin: 0 auto;">
            Complete Attack Chain Demonstration: Finding, Exploiting, and Securing Vulnerabilities
        </p>
    </div>
    
    <div class="highlight-box" style="text-align: center; max-width: 800px; margin: 40px auto;">
        <h3>🎯 Presentation Roadmap</h3>
        <div class="grid-2">
            <div>
                <div class="icon">🎯</div>
                <p><strong>System Overview</strong><br>Understanding the target</p>
            </div>
            <div>
                <div class="icon">🔍</div>
                <p><strong>Discovery</strong><br>How attackers find vulnerabilities</p>
            </div>
            <div>
                <div class="icon">⚔️</div>
                <p><strong>Exploitation</strong><br>Turning bugs into breaches</p>
            </div>
            <div>
                <div class="icon">🛡️</div>
                <p><strong>Defense</strong><br>Building secure systems</p>
            </div>
        </div>
    </div>
</div>

<!-- Slide 2: System Overview -->
<div class="slide" id="slide2">
    <h1>🔍 System Overview</h1>
    
    <div class="highlight-box">
        <h3>📊 The Target Application</h3>
        <div class="grid-2">
            <div>
                <h4>🏗️ Architecture:</h4>
                <ul>
                    <li>PHP Web Application</li>
                    <li>Redis Session Store</li>
                    <li>SQLite Database</li>
                    <li>File Upload System</li>
                    <li>User Authentication</li>
                </ul>
            </div>
            <div>
                <h4>🎯 Key Features:</h4>
                <ul>
                    <li>User Registration/Login</li>
                    <li>Note Management</li>
                    <li>URL Import (SSRF Vector)</li>
                    <li>Admin Panel</li>
                    <li>File Export</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="code-block">
        <h3>💻 Technology Stack</h3>
        <pre style="color: #00ff9d;">
Frontend:      HTML, CSS, JavaScript
Backend:       PHP 8.x
Session Store: Redis (No authentication)
Database:      SQLite
Server:        Apache/Nginx
Protocols:     HTTP, Redis Protocol</pre>
    </div>
    
    <div class="danger-box">
        <h3>⚠️ Critical Design Flaws</h3>
        <ul>
            <li>Redis exposed internally without authentication</li>
            <li>SSRF endpoint with no URL validation</li>
            <li>Admin panel evaluates Redis content</li>
            <li>File writes without content validation</li>
            <li>No input sanitization on Redis data</li>
        </ul>
    </div>
</div>

<!-- Slide 3: How Attackers Discover Vulnerabilities -->
<div class="slide" id="slide3">
    <h1>🕵️‍♂️ Attacker's Discovery Process</h1>
    
    <div class="highlight-box">
        <h3>🎯 Reconnaissance Phase</h3>
        <div class="grid-2">
            <div>
                <h4>Passive Discovery:</h4>
                <ul>
                    <li>Analyze application functionality</li>
                    <li>Map all endpoints</li>
                    <li>Identify user inputs</li>
                    <li>Check for file uploads</li>
                    <li>Look for URL parameters</li>
                </ul>
            </div>
            <div>
                <h4>Active Testing:</h4>
                <ul>
                    <li>Fuzz URL parameters</li>
                    <li>Test for SSRF (import_note.php)</li>
                    <li>Check for Redis exposure</li>
                    <li>Test admin panel functionality</li>
                    <li>Analyze error messages</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="code-block">
        <h3>🔍 Finding the SSRF Vector</h3>
        <pre style="color: #ff6b9d;">
// Vulnerable endpoint found:
POST /import_note.php

Parameters:
- url: (unvalidated user input)

Attack pattern discovered:
1. Can request internal services
2. No protocol restrictions
3. Returns error messages revealing internal IPs
4. Timeout indicates service availability</pre>
    </div>
    
    <div class="success-box">
        <h3>🎯 Discovery Techniques Used</h3>
        <ul>
            <li><strong>Endpoint Mapping:</strong> Found /import_note.php with URL parameter</li>
            <li><strong>Protocol Testing:</strong> Tried dict://, gopher://, file://</li>
            <li><strong>Port Scanning:</strong> Discovered Redis on port 6379</li>
            <li><strong>Error Analysis:</strong> Redis protocol errors revealed service</li>
            <li><strong>Admin Panel Analysis:</strong> Found session display without validation</li>
        </ul>
    </div>
</div>

<!-- Slide 4: The Vulnerabilities -->
<div class="slide" id="slide4">
    <h1>💀 The Vulnerabilities</h1>
    
    <div class="attack-chain">
        <div class="step">
            <div class="step-number">1</div>
            <h3>CWE-918</h3>
            <p>Server-Side Request Forgery</p>
            <p style="color: #00d4ff; margin-top: 10px;">import_note.php</p>
        </div>
        
        <div class="step">
            <div class="step-number">2</div>
            <h3>CWE-306</h3>
            <p>Missing Authentication</p>
            <p style="color: #00d4ff; margin-top: 10px;">Redis Service</p>
        </div>
        
        <div class="step">
            <div class="step-number">3</div>
            <h3>CWE-94</h3>
            <p>Code Injection</p>
            <p style="color: #00d4ff; margin-top: 10px;">admin.php eval()</p>
        </div>
        
        <div class="step">
            <div class="step-number">4</div>
            <h3>CWE-434</h3>
            <p>Unrestricted Upload</p>
            <p style="color: #00d4ff; margin-top: 10px;">export.php</p>
        </div>
    </div>
    
    <div class="grid-2">
        <div class="danger-box">
            <h3>📝 import_note.php (SSRF)</h3>
            <div class="code-block" style="margin: 15px 0; padding: 15px;">
                <pre style="color: #ff6b9d; font-size: 1em;">
$url = trim($_POST['url'] ?? '');
$content = @file_get_contents($url, false, $ctx);
// NO VALIDATION!
// Accepts: dict://, gopher://, file://</pre>
            </div>
            <p><strong>Impact:</strong> Access to internal services, Redis protocol injection</p>
        </div>
        
        <div class="danger-box">
            <h3>⚡ admin.php (Code Injection)</h3>
            <div class="code-block" style="margin: 15px 0; padding: 15px;">
                <pre style="color: #ff6b9d; font-size: 1em;">
$webshell_content = $redis->get("webshell");
eval('?>' . $webshell_content);
// EXECUTES UNTRUSTED CODE!
// Direct RCE enabled</pre>
            </div>
            <p><strong>Impact:</strong> Arbitrary command execution, system takeover</p>
        </div>
    </div>
    
    <div class="highlight-box">
        <h3>🎯 Vulnerability Chain Analysis</h3>
        <ol>
            <li><strong>SSRF</strong> allows accessing internal Redis service</li>
            <li><strong>No Redis auth</strong> enables arbitrary data writes</li>
            <li><strong>Admin panel eval()</strong> executes Redis content</li>
            <li><strong>File write primitive</strong> creates persistent backdoors</li>
            <li><strong>Chain reaction</strong> leads to full system compromise</li>
        </ol>
    </div>
</div>

<!-- Slide 5: Exploitation Demonstration -->
<div class="slide" id="slide5">
    <h1>⚔️ Live Exploitation</h1>
    
    <div class="live-badge" style="position: relative; top: 0; left: 0; margin: 20px 0;">🔴 LIVE DEMO</div>
    
    <div class="highlight-box">
        <h3>🚀 Step-by-Step Attack Chain</h3>
        <div class="grid-2">
            <div>
                <h4>Attack Preparation:</h4>
                <ol>
                    <li>Discover SSRF endpoint</li>
                    <li>Find Redis service (port 6379)</li>
                    <li>Craft Redis protocol payload</li>
                    <li>Encode for gopher://</li>
                    <li>Inject via SSRF</li>
                </ol>
            </div>
            <div>
                <h4>Exploitation:</h4>
                <ol>
                    <li>Inject admin session</li>
                    <li>Store PHP webshell in Redis</li>
                    <li>Access admin panel</li>
                    <li>Trigger eval() execution</li>
                    <li>Gain RCE</li>
                </ol>
            </div>
        </div>
    </div>
    
    <div class="code-block">
        <h3>🔥 Redis Protocol Injection Payload</h3>
        <pre style="color: #00ff9d;">
# Build RESP command
*3\r\n$3\r\nSET\r\n$19\r\nsession_webshell_demo\r\n$55\r\n&lt;?php echo shell_exec($_GET['cmd'] ?? ''); ?&gt;\r\n

# URL encode for SSRF
gopher://127.0.0.1:6379/_*3%0d%0a%243%0d%0aSET...

# Send via vulnerable endpoint
POST /import_note.php
url=gopher://127.0.0.1:6379/_...</pre>
    </div>
    
    <div class="danger-box">
        <h3>🎯 LIVE DEMO LINKS</h3>
        <div style="text-align: center; margin: 30px 0;">
            <a href="admin.php" target="_blank" class="demo-link">🔓 Admin Panel</a>
            <a href="admin.php?execute=eval&cmd=whoami" target="_blank" class="demo-link">👤 whoami</a>
            <a href="admin.php?execute=eval&cmd=id" target="_blank" class="demo-link">🆔 User Info</a>
            <a href="admin.php?execute=persist" target="_blank" class="demo-link">📁 Create Backdoor</a>
            <a href="advanced_attacks.php" target="_blank" class="demo-link">🔬 Advanced Attacks</a>
        </div>
        <p style="text-align: center; color: #ff6b9d; margin-top: 20px;">
            ⚠️ These links demonstrate real RCE in isolated environment
        </p>
    </div>
</div>

<!-- Slide 6: Attacker's Achievements -->
<div class="slide" id="slide6">
    <h1>🏆 Attacker's Achievements</h1>
    
    <div class="grid-2">
        <div class="danger-box">
            <h3>🎯 Immediate Gains</h3>
            <ul>
                <li><strong>Admin Access:</strong> Full control of admin panel</li>
                <li><strong>RCE:</strong> Arbitrary command execution as www-data</li>
                <li><strong>Data Access:</strong> Read/write to database</li>
                <li><strong>File System:</strong> Navigate and read sensitive files</li>
                <li><strong>Network:</strong> Discover internal services</li>
            </ul>
        </div>
        
        <div class="danger-box">
            <h3>🚀 Advanced Capabilities</h3>
            <ul>
                <li><strong>Persistence:</strong> Create cron jobs, backdoors</li>
                <li><strong>Lateral Movement:</strong> Access other systems</li>
                <li><strong>Data Exfiltration:</strong> Steal databases, configs</li>
                <li><strong>Privilege Escalation:</strong> Root access potential</li>
                <li><strong>Coverage:</strong> Remove logs, hide presence</li>
            </ul>
        </div>
    </div>
    
    <div class="highlight-box">
        <h3>💥 Business Impact</h3>
        <div class="grid-2">
            <div>
                <h4>Technical Impact:</h4>
                <ul>
                    <li>Complete system compromise</li>
                    <li>Data breach (PII, credentials)</li>
                    <li>Service disruption</li>
                    <li>Malware installation</li>
                    <li>Reputation damage</li>
                </ul>
            </div>
            <div>
                <h4>Business Impact:</h4>
                <ul>
                    <li>Financial losses</li>
                    <li>Regulatory fines (GDPR, HIPAA)</li>
                    <li>Customer trust erosion</li>
                    <li>Competitive disadvantage</li>
                    <li>Legal liability</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="code-block">
        <h3>📈 Attack Metrics</h3>
        <pre style="color: #00ff9d;">
Time to Discover:      ~2 hours
Time to Exploit:       ~15 minutes
Privilege Level:       Root (via escalation)
Data Exposure:         Complete system
Persistence:           Permanent backdoors
Detection Difficulty:  Low (but often missed)</pre>
    </div>
</div>

<!-- Slide 7: Root Cause Analysis -->
<div class="slide" id="slide7">
    <h1>🔍 Root Cause Analysis</h1>
    
    <div class="highlight-box">
        <h3>🎯 Why This Happened</h3>
        <div class="grid-2">
            <div>
                <h4>Technical Failures:</h4>
                <ul>
                    <li>No input validation on SSRF endpoint</li>
                    <li>Redis deployed without authentication</li>
                    <li>eval() used on untrusted data</li>
                    <li>Missing network segmentation</li>
                    <li>No security headers or WAF</li>
                </ul>
            </div>
            <div>
                <h4>Process Failures:</h4>
                <ul>
                    <li>No security requirements</li>
                    <li>Missing code review</li>
                    <li>No penetration testing</li>
                    <li>Lack of security training</li>
                    <li>No threat modeling</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="danger-box">
        <h3>💀 The Critical Mistakes</h3>
        <ol>
            <li><strong>Trusting User Input:</strong> SSRF endpoint accepted any URL</li>
            <li><strong>Internal Service Exposure:</strong> Redis accessible without auth</li>
            <li><strong>Dangerous Functions:</strong> eval() on Redis content</li>
            <li><strong>Defense in Depth Missing:</strong> Single point of failure</li>
            <li><strong>Monitoring Absence:</strong> No detection of attacks</li>
        </ol>
    </div>
    
    <div class="success-box">
        <h3>🎓 Lessons Learned</h3>
        <ul>
            <li>Never trust user input - validate, sanitize, escape</li>
            <li>Internal services need security too</li>
            <li>eval() is dangerous - avoid at all costs</li>
            <li>Security is a chain - strengthen every link</li>
            <li>Assume breach - monitor and detect</li>
        </ul>
    </div>
</div>

<!-- Slide 8: Defense Strategies -->
<div class="slide" id="slide8">
    <h1>🛡️ Defense Strategies</h1>
    
    <div class="highlight-box">
        <h3>🎯 Immediate Fixes</h3>
        <div class="grid-2">
            <div>
                <h4>Code Level:</h4>
                <div class="code-block" style="margin: 15px 0; padding: 15px;">
                    <pre style="color: #00ff9d;">
// FIX: SSRF Validation
function validateUrl($url) {
    $parsed = parse_url($url);
    $blocked = ['dict', 'gopher', 'file'];
    if (in_array($parsed['scheme'], $blocked)) {
        return false;
    }
    return filter_var($url, FILTER_VALIDATE_URL);
}</pre>
                </div>
            </div>
            <div>
                <h4>Configuration:</h4>
                <div class="code-block" style="margin: 15px 0; padding: 15px;">
                    <pre style="color: #00ff9d;">
# Redis Configuration
requirepass StrongPassword123!
bind 127.0.0.1
rename-command FLUSHALL ""
rename-command CONFIG ""
rename-command EVAL ""</pre>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid-2">
        <div class="success-box">
            <h3>🔐 PHP Hardening</h3>
            <div class="code-block" style="margin: 15px 0; padding: 15px;">
                <pre style="color: #00ff9d;">
; php.ini Security
disable_functions = eval,exec,passthru,shell_exec,system
open_basedir = /var/www/html
allow_url_fopen = Off
allow_url_include = Off
expose_php = Off</pre>
            </div>
        </div>
        
        <div class="success-box">
            <h3>🛡️ Architectural Security</h3>
            <ul>
                <li>Network segmentation (VLANs)</li>
                <li>Web Application Firewall (WAF)</li>
                <li>Intrusion Detection System (IDS)</li>
                <li>Regular security audits</li>
                <li>Automated vulnerability scanning</li>
            </ul>
        </div>
    </div>
    
    <div class="danger-box">
        <h3>🎯 Defense-in-Depth Strategy</h3>
        <ol>
            <li><strong>Network Layer:</strong> Firewall rules, VLAN segmentation</li>
            <li><strong>Application Layer:</strong> Input validation, output encoding</li>
            <li><strong>Service Layer:</strong> Authentication, least privilege</li>
            <li><strong>Data Layer:</strong> Encryption, access controls</li>
            <li><strong>Monitoring Layer:</strong> Logging, SIEM, alerting</li>
        </ol>
    </div>
</div>

<!-- Slide 9: Best Practices -->
<div class="slide" id="slide9">
    <h1>🚀 Security Best Practices</h1>
    
    <div class="grid-2">
        <div class="highlight-box">
            <h3>💻 Development Practices</h3>
            <ul>
                <li><strong>Secure Coding Standards:</strong> OWASP ASVS, SANS Top 25</li>
                <li><strong>Code Review:</strong> Security-focused peer reviews</li>
                <li><strong>Static Analysis:</strong> SAST tools in CI/CD</li>
                <li><strong>Dependency Scanning:</strong> Regular updates, SCA</li>
                <li><strong>Threat Modeling:</strong> STRIDE, DREAD methodologies</li>
            </ul>
        </div>
        
        <div class="highlight-box">
            <h3>🔧 Operations Practices</h3>
            <ul>
                <li><strong>Least Privilege:</strong> Minimal permissions for services</li>
                <li><strong>Regular Patching:</strong> OS, middleware, applications</li>
                <li><strong>Backup Strategy:</strong> Regular, tested backups</li>
                <li><strong>Incident Response:</strong> Documented playbooks</li>
                <li><strong>Security Training:</strong> Regular team education</li>
            </ul>
        </div>
    </div>
    
    <div class="success-box">
        <h3>🎯 Prevention Checklist</h3>
        <div class="grid-2">
            <div>
                <h4>For SSRF:</h4>
                <ul>
                    <li>✅ Validate all URLs</li>
                    <li>✅ Use allowlists, not blocklists</li>
                    <li>✅ Implement timeouts</li>
                    <li>✅ Use authentication for internal services</li>
                    <li>✅ Monitor unusual requests</li>
                </ul>
            </div>
            <div>
                <h4>For Redis:</h4>
                <ul>
                    <li>✅ Enable authentication</li>
                    <li>✅ Bind to localhost</li>
                    <li>✅ Rename dangerous commands</li>
                    <li>✅ Use network segmentation</li>
                    <li>✅ Monitor connection attempts</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="code-block">
        <h3>📊 Security Metrics to Track</h3>
        <pre style="color: #00ff9d;">
Vulnerability Discovery Rate:   &lt; 24 hours
Patch Deployment Time:          &lt; 7 days
Security Training Completion:   100% annually
Penetration Test Frequency:     Quarterly
Incident Response Time:         &lt; 1 hour detection</pre>
    </div>
</div>

<!-- Slide 10: Conclusion & Takeaways -->
<div class="slide" id="slide10">
    <h1>🎓 Key Takeaways</h1>
    
    <div style="text-align: center; margin: 40px 0;">
        <div style="font-size: 5em; margin-bottom: 40px; filter: drop-shadow(0 0 25px rgba(0,255,157,0.7));">
            🛡️🔥💡
        </div>
        <p style="font-size: 2.2em; color: #00ff9d; max-width: 1000px; margin: 0 auto;">
            Understand Attacks → Build Better Defenses
        </p>
    </div>
    
    <div class="grid-2">
        <div class="highlight-box">
            <h3>🎯 Technical Insights</h3>
            <ul>
                <li>SSRF is a gateway to internal services</li>
                <li>Redis without auth is an open door</li>
                <li>eval() should never touch user data</li>
                <li>Chained vulnerabilities multiply impact</li>
                <li>Defense requires multiple layers</li>
            </ul>
        </div>
        
        <div class="highlight-box">
            <h3>🚀 Strategic Insights</h3>
            <ul>
                <li>Security is everyone's responsibility</li>
                <li>Assume breach - focus on detection</li>
                <li>Regular testing finds issues early</li>
                <li>Education prevents many attacks</li>
                <li>Continuous improvement is key</li>
            </ul>
        </div>
    </div>
    
    <div class="success-box" style="text-align: center;">
        <h3>🙏 Thank You! Questions?</h3>
        
        <div style="margin: 40px 0;">
            <div style="display: inline-block; margin: 20px;">
                <div style="font-size: 3em;">👨‍💻</div>
                <p><strong>Isan Jemal</strong><br>Security Researcher</p>
            </div>
            
            <div style="display: inline-block; margin: 20px;">
                <div style="font-size: 3em;">🔗</div>
                <p><strong>Links & Resources</strong><br>
                <a href="https://owasp.org/www-project-top-ten/" target="_blank" style="color: #00ff9d;">OWASP Top 10</a> | 
                <a href="https://cheatsheetseries.owasp.org/" target="_blank" style="color: #00ff9d;">Cheat Sheets</a></p>
            </div>
            
            <div style="display: inline-block; margin: 20px;">
                <div style="font-size: 3em;">💾</div>
                <p><strong>Demo Resources</strong><br>
                <a href="admin.php" target="_blank" style="color: #00ff9d;">Live Demo</a> | 
                <a href="#" onclick="window.print()" style="color: #00ff9d;">Save Slides</a></p>
            </div>
        </div>
        
        <div style="margin-top: 50px; padding-top: 30px; border-top: 2px solid rgba(255,255,255,0.2);">
            <p style="font-size: 1.5em; color: #00d4ff;">
                🔥 RCE Achieved | 🛡️ Lessons Learned | 🚀 Better Security
            </p>
        </div>
    </div>
</div>

<!-- Navigation -->
<div class="nav-buttons">
    <button class="nav-btn" onclick="prevSlide()">←</button>
    <button class="nav-btn" onclick="nextSlide()">→</button>
</div>

<div class="slide-counter">
    Slide: <span id="currentSlide">1</span>/10
</div>

<script>
    let currentSlide = 1;
    const totalSlides = 10;
    
    function showSlide(n) {
        document.querySelectorAll('.slide').forEach(slide => {
            slide.classList.remove('active');
        });
        
        document.getElementById(`slide${n}`).classList.add('active');
        currentSlide = n;
        document.getElementById('currentSlide').textContent = n;
        
        // Scroll to top for each slide
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
        if (e.key === '0') showSlide(10);
    });
    
    // Touch/swipe support
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    document.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left
                nextSlide();
            } else {
                // Swipe right
                prevSlide();
            }
        }
    }
    
    // Initialize
    showSlide(1);
</script>

</body>
</html>