# StudentHub Portal - Academic Management & Course Registration Platform

[![Course](https://img.shields.io/badge/Course-Web%20Development%20Framework-blue.svg)](https://charusat.ac.in)
[![Semester](https://img.shields.io/badge/Semester-3%20(B.Tech%20CSE)-indigo.svg)](https://charusat.ac.in)
[![Security](https://img.shields.io/badge/Security-OWASP%20Top%2010%20%7C%20RBAC%20%7C%20Bcrypt-emerald.svg)](https://owasp.org)
[![Accessibility](https://img.shields.io/badge/Accessibility-WCAG%202.1%20AA%20Compliant-brightgreen.svg)](https://www.w3.org/WAI/WCAG21/quickref/)

**Student Name:** Patel Het Nitinkumar  
**Student ID:** 25CS062  
**Department:** Computer Science & Engineering, CSPIT  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Subject:** Web Development Framework  

---

## 📌 Project Overview & Deliverables

StudentHub Portal is a full-stack academic management web application engineered in PHP and MySQL. It integrates student self-registration, cryptographic authentication, role-based access control (RBAC), multi-role dashboards (Student and Admin), elective course registration, institutional notice broadcasting, and automated security audit logging.

| # | Deliverable | File / Asset | Description |
|---|-------------|--------------|-------------|
| 1 | **Source Code Repository** | [GitHub Repository](https://github.com/Het051007/studenthub-portal) | Complete full-stack modular architecture. |
| 2 | **Database Dump** | [`studenthub.sql`](studenthub.sql) | Relational database schema with initial seed data. |
| 3 | **Setup Documentation** | [`README.md`](README.md) | Setup, configuration, and testing guide. |
| 4 | **Test & Security Audit** | [`TEST_SECURITY_REPORT.md`](TEST_SECURITY_REPORT.md) / [`.html`](TEST_SECURITY_REPORT.html) | 20 Test Cases, OWASP Top 10 mitigation & WCAG 2.1 AA audit. |
| 5 | **UI Prototype Verification** | [`standalone_studenthub_demo.html`](standalone_studenthub_demo.html) | Interactive offline UI prototype for scenario verification. |

---

## ⚙️ System Requirements & Architecture

- **Operating Environment:** Windows 10/11, Linux, or macOS
- **Web Server:** Apache (via XAMPP, WAMP, or standalone Apache 2.4+) or PHP Built-in Server
- **Server-Side Language:** PHP 8.0+ with `pdo_mysql`, `session`, `openssl`, and `json` extensions
- **Database Engine:** MySQL 8.0+ or MariaDB 10.4+ (InnoDB engine, `utf8mb4_unicode_ci`)
- **Frontend Technologies:** Semantic HTML5, Vanilla JavaScript (ES6+), Accessible Responsive CSS3
- **Standards:** WCAG 2.1 Level AA Accessibility & OWASP Web Application Security Best Practices

---

## 🚀 Setup & Installation Guide

### Step 1: Clone or Copy the Repository
Clone the repository into your local web server root directory:

```bash
# For XAMPP (Windows):
cd C:\xampp\htdocs
git clone https://github.com/Het051007/studenthub-portal.git studenthub

# For Linux Apache:
cd /var/www/html
git clone https://github.com/Het051007/studenthub-portal.git studenthub
```

### Step 2: Database Setup (`studenthub.sql`)

#### Method A: Via phpMyAdmin (Recommended)
1. Open your browser and go to `http://localhost/phpmyadmin/`.
2. Click **New** in the sidebar and create a database named `studenthub_db` (Collation: `utf8mb4_unicode_ci`).
3. Click the **Import** tab.
4. Choose the `studenthub.sql` file located in the project root and click **Import / Go**.
5. Ensure all 5 tables (`users`, `courses`, `enrollments`, `notices`, `audit_logs`) are created.

#### Method B: Via MySQL Command Line
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS studenthub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p studenthub_db < studenthub.sql
```

### Step 3: Configure Database Settings
Review `config/db.php` to ensure your database connection parameters match your environment:
```php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL password if configured
```

### Step 4: Run the Application
Open your browser and navigate to:
```
http://localhost/studenthub/
```
*(Or use the PHP built-in server from the project directory: `php -S 127.0.0.1:8000` and visit `http://127.0.0.1:8000/`)*

---

## 🔑 Pre-Configured Test Accounts

The database comes pre-seeded with accounts for testing:

| Role | Email Identifier | Password | Student / Staff ID | Permissions & Scope |
|------|-----------------|----------|--------------------|---------------------|
| **Admin** | `admin@charusat.edu.in` | `Admin@123` | `ADM001` | Full administrative control, student directory, notice broadcasting, course catalog, security audit logs |
| **Student** | `25cs062@charusat.edu.in` | `Student@123` | `25CS062` (Het Patel) | Personal academic dashboard, enrolled modules, grades, attendance, course enrollment, notice viewer |
| **Student** | `25cs015@charusat.edu.in` | `Student@123` | `25CS015` (Aryan Shah) | Peer student profile for multi-user verification |

---

## 🛡️ Security Implementation & OWASP Mitigation Matrix

1. **SQL Injection Defense (CWE-89):**
   - 100% of database queries utilize **PDO Prepared Statements** with parameterized inputs (`$stmt->prepare()` and `$stmt->execute([...])`).
   - `PDO::ATTR_EMULATE_PREPARES` is explicitly set to `false` to guarantee genuine prepared statements on the database server.
2. **Cryptographic Password Storage (CWE-256):**
   - Implemented PHP's native `password_hash($password, PASSWORD_BCRYPT, ['cost' => 10])`.
   - Generates a unique 22-character dynamic cryptographic salt per user, neutralizing rainbow-table attacks.
   - Verified via `password_verify()` with constant-time comparison to prevent timing attacks.
3. **Session Fixation Defense (CWE-384):**
   - Invokes `session_regenerate_id(true)` immediately following credential validation.
   - Invalidates the pre-authentication session file (`true` argument) and allocates a fresh 128-bit session token.
4. **Session Hijacking & Inactivity Expiration (CWE-613):**
   - 300-second (5 minute) inactivity session timeout configured in `includes/auth_check.php`.
   - Session cookies configured with `HttpOnly` (mitigating JavaScript cookie theft) and `SameSite=Lax`.
5. **Cross-Site Request Forgery (CSRF - CWE-352):**
   - Cryptographically secure 32-byte tokens generated via `random_bytes(32)`.
   - Embedded via hidden form inputs (`<?= csrf_input(); ?>`) and validated using `hash_equals()`.
6. **Cross-Site Scripting (XSS - CWE-79):**
   - Context-aware output escaping enforced across all views using `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')`.
7. **Role-Based Access Control (RBAC - CWE-285):**
   - Strict server-side role validation in `includes/rbac_check.php`.
   - Cross-role access attempts (such as a student accessing `admin_dashboard.php`) trigger HTTP 403 Forbidden and are logged to `audit_logs`.

---

## 🧪 Testing Workflows

### Test 1: Student Authentication & Academics Flow
1. Navigate to `http://localhost/studenthub/login.php`.
2. Enter student credentials (`25cs062@charusat.edu.in` / `Student@123`).
3. Verify redirection to `student_dashboard.php`.
4. Inspect enrolled courses (CE251, CE252, CE253, CE254), attendance (94.5%), and grades (AA).
5. In the "Elective & Course Registration" panel, select an available course and click "Enroll Now".
6. Verify enrollment is added and total credits are updated.

### Test 2: Admin Management & Broadcasting Flow
1. Log in as Admin (`admin@charusat.edu.in` / `Admin@123`).
2. Verify redirection to `admin_dashboard.php`.
3. Check the metric summary cards (Students, Courses, Enrollments, Notices).
4. Inspect the **Registered Student Directory** and verify student `25CS062` is listed.
5. Broadcast a new campus notice via the form and verify it appears in the live bulletins.
6. Inspect the **Security Incident & Activity Audit Trail** displaying real-time event logs.

### Test 3: RBAC Privilege Escalation Trap
1. While logged in as Student (`25cs062`), manually navigate to `http://localhost/studenthub/admin_dashboard.php`.
2. Confirm the server returns **HTTP 403 Forbidden** with the security violation alert screen.
3. Verify that the event is logged in the `audit_logs` table.

### Test 4: Inactivity Timeout
1. Leave the dashboard idle for 300 seconds (or run `timeout.php`).
2. Verify the session is invalidated and user is redirected to `login.php?msg=timeout`.

---

## 💻 Offline UI Prototype

For scenario verification or offline interface review:
- Open [`standalone_studenthub_demo.html`](standalone_studenthub_demo.html) in any modern browser.
- Use the scenario selector at the top to preview the system states and user flows.
