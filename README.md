# SSRF → Redis → RCE Attack Chain Demonstration

## 🎯 Project Overview
This educational project demonstrates a complete attack chain from Server-Side Request Forgery (SSRF) to Remote Code Execution (RCE) through Redis injection. The vulnerable web application is intentionally designed to show how multiple security flaws chain together for complete system compromise.

## ⚠️ SECURITY WARNING
**FOR EDUCATIONAL USE ONLY IN ISOLATED ENVIRONMENT**
- Never deploy on production systems
- Use in VM/Docker containers only
- No sensitive data included
- All demonstrations in controlled lab

## 🏗️ System Architecture

### Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8.x
- **Session Store**: Redis (no authentication)
- **Database**: SQLite
- **Web Server**: Apache/Nginx
- **Container**: Docker (recommended)

### Application Components
1. **User Authentication System** (login.php, register.php)
2. **Note Management System** (notes.php)
3. **Admin Control Panel** (admin.php)
4. **URL Import Feature** (import_note.php)
5. **File Export Functionality** (export.php)

## 📋 Vulnerable Components

### 1. SSRF Vulnerability - `import_note.php`
**Location**: `/var/www/html/public/import_note.php`
**Vulnerability**: Unvalidated URL parameter allows accessing internal services
**Root Cause**: `file_get_contents()` accepts user-supplied URLs without validation
**Exploits**: Redis protocol injection via `gopher://` and `dict://`

### 2. Missing Redis Authentication
**Location**: Redis server configuration
**Vulnerability**: Redis accessible without password
**Root Cause**: Default configuration with `requirepass` not set
**Impact**: Arbitrary data injection into session store

### 3. Code Injection - `admin.php`
**Location**: `/var/www/html/public/admin.php`
**Vulnerability**: `eval()` executes Redis content as PHP code
**Root Cause**: Untrusted Redis data passed directly to `eval()`
**Impact**: Remote Code Execution (RCE)

### 4. Arbitrary File Write - `export.php`
**Location**: `/var/www/html/public/export.php`
**Vulnerability**: Writes Redis content to files without validation
**Root Cause**: No content validation before file creation
**Impact**: Webshell deployment to webroot

## 🚀 Quick Setup

### Docker Setup (Recommended)
```bash
# Clone or extract project files
cd ssrf-redis-rce-demo

# Start the environment
docker-compose up -d

# Access the application
http://localhost:8080

