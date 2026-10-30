# StudentHub Portal - Web Development Framework Practical Submission

[![Course](https://img.shields.io/badge/Course-Web%20Development%20Framework-blue.svg)](https://charusat.ac.in)
[![Semester](https://img.shields.io/badge/Semester-3%20(B.Tech%20CSE)-indigo.svg)](https://charusat.ac.in)
[![Security](https://img.shields.io/badge/Security-OWASP%20Top%2010%20%7C%20RBAC%20%7C%20Bcrypt-emerald.svg)](https://owasp.org)
[![Accessibility](https://img.shields.io/badge/Accessibility-WCAG%202.1%20AA%20Compliant-brightgreen.svg)](https://www.w3.org/WAI/WCAG21/quickref/)

**Student Name:** Patel Het Nitinkumar  
**Student ID:** 25CS062  
**Department:** Computer Science & Engineering, CSPIT  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Subject:** Web Development Framework (Practical Final Submission)  

---

## 📌 Executive Summary & Submission Checklist

StudentHub Portal is a full-stack, enterprise-grade academic management system integrating student self-registration, role-based access control (RBAC), multi-role dashboards (Student and Admin), course enrollment, institutional bulletin broadcasting, and automated security audit logging.

| # | Submission Item | File / Asset Link | Status |
|---|----------------|-------------------|--------|
| 1 | **GitHub Repo Link** | Integrated source code repository | ✅ Ready to Push |
| 2 | **Database Dump** | [`studenthub.sql`](file:///C:/Users/hetpa/.gemini/antigravity/scratch/studenthub/studenthub.sql) | ✅ Complete Schema & Seed Data |
| 3 | **README.md File** | [`README.md`](file:///C:/Users/hetpa/.gemini/antigravity/scratch/studenthub/README.md) | ✅ Setup & Testing Instructions |
| 4 | **Test & Security Report** | [`TEST_SECURITY_REPORT.md`](file:///C:/Users/hetpa/.gemini/antigravity/scratch/studenthub/TEST_SECURITY_REPORT.md) / [`.html`](file:///C:/Users/hetpa/.gemini/antigravity/scratch/studenthub/TEST_SECURITY_REPORT.html) | ✅ 20+ Tests, OWASP & WCAG AA Audit |
| 5 | **Screenshots / Live Demo** | [`standalone_studenthub_demo.html`](file:///C:/Users/hetpa/.gemini/antigravity/scratch/studenthub/standalone_studenthub_demo.html) | ✅ Interactive Browser Simulator |

---

## 🔗 GitHub Repository Setup Instructions

To push this codebase to your personal GitHub profile:

```bash
# 1. Navigate to the project root directory
cd c:\Users\hetpa\.gemini\antigravity\scratch\studenthub

# 2. Check git status
git status

# 3. Create a new repository on GitHub named 'studenthub-portal'
# (e.g., https://github.com/hetpatel-25cs062/studenthub-portal.git)

# 4. Connect remote repository and push
git remote add origin https://github.com/hetpatel-25cs062/studenthub-portal.git
git branch -M main
git push -u origin main
```

**Submitted Repo URL Format:** `https://github.com/hetpatel-25cs062/studenthub-portal`

---

## ⚙️ System Requirements & Architecture

- **Operating Environment:** Windows 10/11 / macOS / Linux
- **Web Server:** Apache (via XAMPP, WAMP, or standalone Apache 2.4+) or PHP Built-in Server
- **Server-Side Language:** PHP 8.0+ with `pdo_mysql`, `session`, `openssl`, and `json` extensions
- **Database Engine:** MySQL 8.0+ or MariaDB 10.4+ (InnoDB engine, `utf8mb4_unicode_ci`)
- **Frontend Technologies:** Semantic HTML5, Vanilla JavaScript (ES6+), Accessible Responsive CSS3
- **Standards:** WCAG 2.1 Level AA Accessibility & OWASP Web Application Security Best Practices

---

## 🚀 Setup & Installation Guide (Step-by-Step)

### Step 1: Clone or Copy Source Code
Place the `studenthub` directory inside your web server root:
- **XAMPP on Windows:** `C:\xampp\htdocs\studenthub`
- **WAMP on Windows:** `C:\wamp64\www\studenthub`
- **Linux Apache:** `/var/www/html/studenthub`

### Step 2: Import the Database (`studenthub.sql`)
#### Method A: Via phpMyAdmin (Recommended)
1. Open your browser and navigate to `http://localhost/phpmyadmin/`.
2. Click **New** in the left sidebar and create a database named `studenthub_db` (Collation: `utf8mb4_unicode_ci`).
3. Click the **Import** tab.
4. Select `studenthub.sql` from the project root and click **Import / Go**.
5. Verify that all 5 tables (`users`, `courses`, `enrollments`, `notices`, `audit_logs`) are created.

#### Method B: Via Command Line Interface (CLI)
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS studenthub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p studenthub_db < studenthub.sql
```

### Step 3: Configure Database Connection
Open `config/db.php` and verify connection parameters:
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL password if configured
```

### Step 4: Launch the Portal
Open any modern browser and visit:
```
http://localhost/studenthub/
```
*(Or if using the PHP built-in server from command line: `php -S 127.0.0.1:8000` then visit `http://127.0.0.1:8000/`)*

---

## 🔑 Pre-Configured Seed Test Accounts

The database comes pre-seeded with encrypted Bcrypt credentials for immediate testing:

| Role | Email Identifier | Raw Password | Assigned Identifier | Permissions & Scope |
|------|-----------------|--------------|---------------------|---------------------|
| **Admin** | `admin@charusat.edu.in` | `Admin@123` | `ADM001` | Full administrative control, student directory, notice broadcasting, course catalog, security audit logs |
| **Student** | `25cs062@charusat.edu.in` | `Student@123` | `25CS062` (Het Patel) | Personal academic dashboard, enrolled modules, grades, attendance, course enrollment, notice viewer |
| **Student** | `25cs015@charusat.edu.in` | `Student@123` | `25CS015` (Aryan Shah) | Peer student profile for multi-user verification |

> [!TIP]
> **Fast-Test Autofill:** On the `login.php` screen, click either the **"🎓 Student Account"** or **"🛡️ Admin Account"** buttons to automatically fill in the test credentials with one click.

---

## 🛡️ Security Implementation & OWASP Mitigation Matrix

1. **SQL Injection Defense (CWE-89):**
   - 100% of database interactions utilize **PDO Prepared Statements** with bound parameters (`$stmt->prepare()` and `$stmt->execute([...])`).
   - `PDO::ATTR_EMULATE_PREPARES` is set to `false`, guaranteeing genuine prepared statements on the database server.
2. **Cryptographic Password Storage (CWE-256):**
   - Implemented PHP's native `password_hash($password, PASSWORD_BCRYPT, ['cost' => 10])`.
   - Incorporates a 22-character dynamic cryptographic salt per record, neutralizing rainbow tables.
   - Verified via `password_verify()` with constant-time string comparison preventing CPU timing attacks.
3. **Session Fixation Defense (CWE-384):**
   - Calling `session_regenerate_id(true)` immediately following credential verification.
   - Deletes the unauthenticated pre-login session file (`true` argument) and allocates a fresh, high-entropy 128-bit session identifier.
4. **Session Hijacking & Inactivity Expiration (CWE-613):**
   - Inactivity window configured to 300 seconds (5 minutes) in `includes/auth_check.php`.
   - Session cookies hardened with `HttpOnly` (inaccessible to malicious JavaScript) and `SameSite=Lax`.
5. **Cross-Site Request Forgery (CSRF - CWE-352):**
   - Cryptographically random 32-byte tokens generated via `random_bytes(32)`.
   - Embedded via hidden input `<?= csrf_input(); ?>` and validated on every state-altering POST request via `hash_equals()`.
6. **Cross-Site Scripting (XSS - CWE-79):**
   - Context-aware output escaping enforced using `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')`.
7. **Role-Based Access Control (RBAC - CWE-285):**
   - Strict server-side role validation in `includes/rbac_check.php`.
   - Unauthorized attempts by students to load `admin_dashboard.php` or admins to load `student_dashboard.php` are intercepted with HTTP 403 Forbidden and recorded in `audit_logs`.

---

## 🧪 Testing Workflows

### Test 1: Student Authentication & Academics Flow
1. Navigate to `http://localhost/studenthub/login.php`.
2. Click **"🎓 Student Account"** and click **"Secure Sign In"**.
3. Verify redirection to `student_dashboard.php`.
4. Check that courses (CE251, CE252, CE253, CE254), attendance (94.5%), and grades (AA) are displayed.
5. In the "Elective & Course Registration" panel, select an available course and click "Enroll Now".
6. Verify enrollment is added and credits updated.

### Test 2: Admin Security & Broadcasting Flow
1. Log out or open an Incognito window and log in as `admin@charusat.edu.in` / `Admin@123`.
2. Verify redirection to `admin_dashboard.php`.
3. Check the KPI metric counters (Students, Courses, Enrollments, Notices).
4. Inspect the **Registered Student Directory** and verify student `25CS062` is listed.
5. Broadcast a new campus notice via the form and verify it appears in the live bulletins.
6. Inspect the **Security Incident & Activity Audit Trail** showing logins and system events.

### Test 3: RBAC Privilege Escalation Trap
1. While logged in as Student (`25cs062`), manually alter the browser URL to `http://localhost/studenthub/admin_dashboard.php`.
2. Confirm the server returns **HTTP 403 Forbidden** with the security violation alert screen.
3. Verify that the event is logged in the `audit_logs` table.

### Test 4: Inactivity Timeout
1. Leave the dashboard idle for 300 seconds (or run `timeout.php`).
2. Verify the session is invalidated and user is redirected to `login.php?msg=timeout`.

---

## ⚡ Zero-Server Standalone Simulator

For instant offline demonstration or immediate screenshot capture without configuring local web servers:
- Double-click [`standalone_studenthub_demo.html`](file:///C:/Users/hetpa/.gemini/antigravity/scratch/studenthub/standalone_studenthub_demo.html) to open in Google Chrome, Microsoft Edge, or Firefox.
- Use the quick scenario switcher at the top to simulate any auth flow, dashboard state, or security violation.
