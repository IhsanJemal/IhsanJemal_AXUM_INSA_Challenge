SSRF → Redis Injection → Admin Session Forgery → Remote Code Execution → Database Dump

Demo Video:
https://drive.google.com/file/d/1UC3B_WpSMfuMuKdH6n_pT1jDMu99KdyZ/view?usp=sharing

SECURITY WARNING
FOR EDUCATIONAL USE ONLY — DO NOT DEPLOY IN PRODUCTION

This project is intentionally vulnerable and designed strictly for learning and demonstration purposes in an isolated lab environment (Docker/ VM).
No real user data
No production hardening
Vulnerabilities are introduced on purpose
Demonstrates real-world attack chaining techniques

Project Overview
This project demonstrates a complete multi-stage attack chain starting from a Server-Side Request Forgery (SSRF) vulnerability and escalating into full system compromise, including:

Internal Redis access
Authentication bypass
Remote Code Execution (RCE)
SQLite database disclosure
The goal is to show how individually simple vulnerabilities become critical when chained together.

Application Architecture
Technology Stack
Backend: PHP 8.2
Database: SQLite
Session Store: Redis (no authentication)
Web Server: Apache / Nginx
Environment: Docker (recommended)
Application Port: http://localhost:8080

Application Components
User authentication (login / register)
Notes management
Admin control panel
URL import functionality
Export functionality

Deliverables
This repository contains:
A deliberately vulnerable PHP web application
A custom exploit generator for SSRF → Redis attacks
A demo video showing live exploitation
Vulnerability documentation and proof-of-concept attacks

Attack Chain Summary
SSRF
 → Redis Injection
 → Admin Session Forgery
 → Remote Code Execution
 → Database Dump

High-Level Flow
Attacker abuses SSRF in import_note.php
SSRF allows gopher:// access to internal Redis
Redis session keys are injected
Attacker forges an admin session
Admin panel executes attacker-controlled eval()
SQLite database files are discovered and dumped

Vulnerable Components

1️. Server-Side Request Forgery (SSRF)
Endpoint: /import_note.php
Parameter: url
Root Cause:
User input is passed directly to file_get_contents() without validation.
$content = @file_get_contents($url, false, $ctx);
Impact:
Allows attackers to access internal services, including Redis.

2️. Redis Injection via SSRF
Redis Location: 127.0.0.1:6379
Authentication: None
Root Cause:
Redis is accessible internally and accepts unauthenticated commands.
Using SSRF with the gopher:// protocol, raw Redis RESP commands can be injected.
Impact:
Arbitrary key creation
Session manipulation
Data injection

3️. Admin Session Forgery
Session Key Format: session_<SID>
Storage: Redis
Root Cause:
Session data is trusted without integrity validation.
By injecting:
SET session_admin_from_ssrf {"user":"admin","role":"admin"} and setting the browser cookie:
SID=admin_from_ssrf
the attacker gains admin access.

4️. Remote Code Execution (RCE)
Location: admin.php
Root Cause: User-controlled input is passed directly to eval().
eval($_GET['cmd']);
Impact: Arbitrary system command execution.
Example: http://localhost:8080/admin.php?execute=eval&cmd=id

5️. Database Disclosure
Database Type: SQLite
Root Cause: RCE allows unrestricted filesystem access.
Example commands:
find / -name "*.sqlite"
cat /app/data/database.sqlite
Impact: Full database dump and data exposure.

Custom Exploit Generator

File: exploits/redis_exploit_generator.py

This script automates the entire SSRF → Redis exploitation process.

Capabilities
Builds valid Redis RESP commands
Encodes payloads into gopher:// URLs
Generates SSRF-ready exploit links

Injects:
Admin session
Webshell payload
Database marker payloads
Saves exploit artifacts for review

Example Output
Admin session injection URL
Webshell injection URL
Database payload URL
This demonstrates exploit automation, not manual exploitation.

Exploit Execution
Step 1 — Generate Payloads
python exploits/redis_exploit_generator.py
Step 2 — Inject Admin Session via SSRF
Open generated URL:
http://localhost:8080/import_note.php?url=gopher://127.0.0.1:6379/...

Step 3 — Access Admin Panel
Set browser cookie:
SID=admin_from_ssrf
Visit:
http://localhost:8080/admin.php

Step 4 — Execute RCE
http://localhost:8080/admin.php?execute=eval&cmd=whoami

Step 5 — Dump Database
http://localhost:8080/admin.php?execute=eval&cmd=find%20/%20-name%20"*.sqlite"

Impact Demonstrated

Authentication bypass
Internal service compromise
Arbitrary command execution
Complete database disclosure
Full loss of confidentiality and integrity

Alternative Exploitation Paths (Theoretical)

Depending on configuration, attackers could also attempt:
SSRF → internal HTTP services
SSRF → metadata services (cloud environments)
Redis → session fixation only
Redis → persistence abuse
RCE → privilege escalation
These paths were not required to demonstrate the core chain.

Mitigation & Fix Recommendations
SSRF
Restrict allowed URL schemes
Block internal IP ranges
Use allowlists instead of blacklists

Redis
Enable authentication (requirepass)
Bind Redis to localhost or private network
Use firewall rules and segmentation
PHP / RCE
Remove eval() entirely
Validate and sanitize all user input
Use safe command execution patterns

Architecture
Do not trust internal services by default
Apply defense-in-depth
Conduct regular security reviews

Lessons Learned

This project demonstrates how low-complexity vulnerabilities can combine into critical system compromise.
SSRF is rarely an isolated issue — when internal services are insecure, it becomes a powerful pivot point.

Intentional Vulnerability Design

All vulnerabilities in this application were intentionally introduced to demonstrate realistic exploitation chains in a controlled environment.

The goal is education, not production readiness.

