# STUDENTHUB PORTAL - COMPREHENSIVE TEST, SECURITY & ACCESSIBILITY AUDIT REPORT

**Subject:** Web Development Framework (Practical Submission)  
**Course Code:** B.Tech Computer Science & Engineering (Semester 3)  
**Institution:** Charotar University of Science and Technology (CHARUSAT)  
**Student Name:** Patel Het Nitinkumar  
**Student ID:** 25CS062  
**Evaluation Date:** October 2026  
**Document Classification:** Final Practical Submission Technical Dossier  

---

## 1. EXECUTIVE SUMMARY & QUALITY ASSURANCE OBJECTIVES

This quality assurance dossier presents an exhaustive verification audit for the **StudentHub Academic Portal**. The verification framework integrates three core evaluation pillars:
1. **Functional & Boundary Testing Matrix:** Rigorous empirical validation of registration, authentication, RBAC authorization, course enrollment, notice broadcasting, and session lifecycles across 20 distinct test cases.
2. **Security Vulnerability Assessment:** Threat modeling aligned with the **OWASP Top 10 (2021)** and **CWE (Common Weakness Enumeration)**, verifying cryptographic password storage (Bcrypt), SQL injection immunity (PDO Prepared Statements), CSRF token enforcement, Cross-Site Scripting (XSS) output encoding, Session Fixation neutralization (`session_regenerate_id`), and automated inactivity session termination.
3. **Accessibility (A11y) Evaluation:** Verification against the international **W3C Web Content Accessibility Guidelines (WCAG 2.1 Level AA)** standard covering semantic landmarks, high-contrast ratios ($\ge 4.5:1$), keyboard-only navigability, visible focus rings, and ARIA attributes.

---

## 2. FUNCTIONAL & BOUNDARY TEST MATRIX (20 TEST CASES)

| Test ID | Module | Test Scenario & Objective | Input Parameters | Expected Result | Actual Result | Status |
|---|---|---|---|---|---|:---:|
| **TC-01** | Auth | Valid Student Login | Email: `25cs062@charusat.edu.in`<br>Password: `Student@123` | Session ID regenerated, redirects to `student_dashboard.php` | Successfully loaded student portal with enrolled courses | **PASS** |
| **TC-02** | Auth | Valid Admin Login | Email: `admin@charusat.edu.in`<br>Password: `Admin@123` | Session ID regenerated, redirects to `admin_dashboard.php` | Loaded admin console with user directory & audit logs | **PASS** |
| **TC-03** | Auth | Invalid Password Rejection | Email: `25cs062@charusat.edu.in`<br>Password: `WrongPass#99` | Block access, render alert: "Invalid email or password", log failed attempt | Returned red error banner, credential rejected | **PASS** |
| **TC-04** | Auth | Unregistered Email Rejection | Email: `ghost_user@charusat.edu.in`<br>Password: `AnyPassword123` | Block access with generic failure message | Returned red error banner without user enumeration | **PASS** |
| **TC-05** | Auth | Empty Fields Submission | Email: ` ` (empty)<br>Password: ` ` (empty) | Trigger client validation and server fallback error banner | Form blocked, displayed empty fields warning | **PASS** |
| **TC-06** | Auth | CSRF Token Tampering | Altered hidden `csrf_token` value in POST payload | Reject request with error `csrf_failed`, abort authentication | Request terminated, redirected with CSRF alert | **PASS** |
| **TC-07** | RBAC | Student Accessing Admin Console | Authenticated Student requests `GET /admin_dashboard.php` | Return HTTP 403 Forbidden, log `RBAC_VIOLATION_BLOCKED` to audit | Rendered 403 Forbidden page; violation recorded in DB | **PASS** |
| **TC-08** | RBAC | Admin Accessing Student Dashboard | Authenticated Admin requests `GET /student_dashboard.php` | Return HTTP 403 Forbidden, log violation | Rendered 403 Forbidden page; admin redirected | **PASS** |
| **TC-09** | RBAC | Unauthenticated Direct Access | Guest requests `GET /student_dashboard.php` without session | Redirect to `login.php?error=unauthorized` | Redirected cleanly to login gateway | **PASS** |
| **TC-10** | Session | Session Fixation Mitigation | Pre-auth session ID compared against post-auth session ID | `session_regenerate_id(true)` destroys old token, generates new ID | Verified pre-login token purged; new session key allocated | **PASS** |
| **TC-11** | Session | Inactivity Expiration (Timeout) | Idle state exceeding 300 seconds (5 minutes) | Session variables wiped, redirected to `login.php?msg=timeout` | Automatic session destruction confirmed | **PASS** |
| **TC-12** | Session | Secure Logout Flow | User clicks "Sign Out" button | Session destroyed, cookie expired in past, redirect to login | Session ended; subsequent back-button navigation blocked | **PASS** |
| **TC-13** | Register | Valid New Student Registration | ID: `25CS099`, Name: `Kavya Patel`, Email: `25cs099@charusat.edu.in`, Pass: `StrongPass@2026` | Bcrypt hash generated, user record created, redirect to login | Record added to DB with 60-char Bcrypt string | **PASS** |
| **TC-14** | Register | Duplicate Student ID Prevention | Attempt to register with existing ID: `25CS062` | Server catches duplicate unique constraint, returns error banner | Registration aborted with duplicate warning | **PASS** |
| **TC-15** | Register | Duplicate Email Prevention | Attempt to register with existing Email: `25cs062@charusat.edu.in` | Reject registration, alert duplicate email | Rejected; database integrity maintained | **PASS** |
| **TC-16** | Register | Password Mismatch Catch | Password: `Password@123`<br>Confirm: `Different@123` | Client error message displayed; server blocks insert | Highlighted red border; insert aborted | **PASS** |
| **TC-17** | Register | Password Length Boundary (<8 chars) | Password: `Pass1` (5 characters) | Reject submission: "Password must be at least 8 characters long" | Client strength meter warns; server rejects | **PASS** |
| **TC-18** | Academics | Student Course Self-Enrollment | Student selects CE255 from dropdown and clicks "Enroll Now" | Record added to `enrollments` table, credits recalculated | Course appears in registered courses table | **PASS** |
| **TC-19** | Admin | Notice Broadcast by Admin | Title: "Exam Notice", Content: "Lab schedule", Priority: "Urgent" | Insert into `notices` table, immediately visible to students | Notice displayed on student dashboard banner | **PASS** |
| **TC-20** | Security | SQL Injection Payload Test | Email: `' OR '1'='1' --`<br>Password: `' OR '1'='1'` | PDO prepared statement treats input as literal string; query returns 0 rows | Login rejected with invalid credentials; no bypass possible | **PASS** |

---

## 3. SECURITY VULNERABILITY ASSESSMENT (OWASP TOP 10 MITIGATION)

```mermaid
flowchart TD
    Client["Client Browser Request"] --> WAF["Input Sanitization & HTTP Headers"]
    WAF --> CSRFCheck{"Valid CSRF Token?"}
    CSRFCheck -- No --> BlockCSRF["HTTP 403 / CSRF Error"]
    CSRFCheck -- Yes --> AuthEngine["Authentication Engine"]
    AuthEngine --> PdoQuery["PDO Parameterized Query (Anti-SQLi)"]
    PdoQuery --> PwdVerify{"password_verify() Match?"}
    PwdVerify -- No --> LogFail["Log Failed Attempt in audit_logs"]
    PwdVerify -- Yes --> Regen["session_regenerate_id(true) (Anti-Fixation)"]
    Regen --> RbacGuard{"RBAC Role Authorization Check"}
    RbacGuard -- Role Mismatch --> Block403["HTTP 403 Forbidden Intercept"]
    RbacGuard -- Authorized --> GrantAccess["Render Role-Specific Dashboard"]
```

### 3.1 Detailed OWASP Vulnerability Matrix

| Vulnerability | CWE ID | Threat Impact | Mitigation Mechanism in StudentHub | Verification Result |
|---|---|---|---|---|
| **A01: Broken Access Control** | CWE-285 | Unauthorized student viewing admin users or admin tampering student grades | `includes/rbac_check.php` enforces server-side role validation on every endpoint. Cross-role attempts trigger HTTP 403 Forbidden. | **Vulnerability Neutralized** |
| **A02: Cryptographic Failures** | CWE-256 | Exposure of user passwords from database dump leaks | Native `password_hash($pass, PASSWORD_BCRYPT, ['cost'=>10])`. Dynamic 22-char salt per user; 60-char Eksblowfish hash. | **Vulnerability Neutralized** |
| **A03: Injection (SQLi)** | CWE-89 | Attacker extracting database tables via malformed `' OR '1'='1` payloads | 100% of SQL statements authored with PDO Parameterized Prepared Statements. Emulated prepares disabled (`ATTR_EMULATE_PREPARES => false`). | **Vulnerability Neutralized** |
| **A03: Cross-Site Scripting (XSS)** | CWE-79 | Malicious scripts injected via student names or notice announcements | All dynamic user output wrapped in `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`. Content-Security-Policy headers applied. | **Vulnerability Neutralized** |
| **A05: Security Misconfiguration** | CWE-16 | Information leakage via verbose stack traces to attackers | PDO exceptions trapped in structured try-catch blocks. Generic user-facing error dialogs rendered without leaking filepaths. | **Vulnerability Neutralized** |
| **A07: Identification & Auth Failures** | CWE-384<br>CWE-613 | Session fixation hijacks; stale sessions on public laboratory workstations | `session_regenerate_id(true)` called upon login. 300-second (5 minute) automated inactivity session expiration enforced. | **Vulnerability Neutralized** |
| **A08: Software & Data Integrity (CSRF)** | CWE-352 | Forging unauthorized course enrollments or admin notices from external origins | High-entropy 32-byte cryptographic random tokens generated via `random_bytes(32)` validated with `hash_equals()`. | **Vulnerability Neutralized** |
| **A09: Security Logging & Monitoring** | CWE-778 | Unaudited privilege escalations or undetected brute-force attacks | Dedicated `audit_logs` table recording all login successes, failures, RBAC violations, user IDs, and client IP addresses. | **Vulnerability Neutralized** |

---

## 4. ACCESSIBILITY (A11Y) COMPLIANCE AUDIT (WCAG 2.1 LEVEL AA)

The interface was constructed and audited following the **W3C Web Content Accessibility Guidelines (WCAG 2.1 Level AA)** across all 4 accessibility principles:

### 4.1 Principle 1: Perceivable
- **Color Contrast Ratios (WCAG 1.4.3):**
  - Primary text (`#f8fafc`) on dark card background (`#151d30`): **Contrast Ratio 15.2:1** (Exceeds 4.5:1 requirement).
  - Primary button (`#2563eb`) with white text: **Contrast Ratio 4.6:1** (Passes AA).
  - Status alert banners (Success `#10b981`, Warning `#f59e0b`, Danger `#ef4444`) utilize both distinct colors AND supplementary text icons (`✅`, `⚠️`, `🛑`, `🔒`) to satisfy **WCAG 1.4.1 (Use of Color)**.
- **Semantic Structure (WCAG 1.3.1):**
  - Logical header hierarchies (`<h1>` for page titles, `<h2>` for major sections, `<h3>` for cards).
  - Structural landmark tags: `<header role="banner">`, `<nav role="navigation">`, `<main role="main">`, `<footer role="contentinfo">`.

### 4.2 Principle 2: Operable
- **Keyboard Navigation (WCAG 2.1.1):**
  - All interactive elements (links, form inputs, buttons, dropdown selects) are 100% reachable and operable via the <kbd>Tab</kbd>, <kbd>Enter</kbd>, and <kbd>Space</kbd> keys.
- **Focus Visible (WCAG 2.4.7):**
  - Custom `:focus-visible` CSS rule applies a high-contrast 3px outline (`outline: 3px solid #38bdf8; outline-offset: 2px`) ensuring keyboard users clearly identify active control focus.
- **No Keyboard Traps (WCAG 2.1.2):**
  - Tab order follows the natural document visual flow from top-left to bottom-right without loops.

### 4.3 Principle 3: Understandable
- **Form Labels & Error Association (WCAG 3.3.2):**
  - Every `<input>` and `<select>` explicitly associates with its `<label for="...">` attribute.
  - Required fields are clearly identified with asterisks (`*`) and HTML5 `required` attributes.
  - Dynamic client-side password strength meter provides real-time guidance before submission.
- **Predictable Navigation (WCAG 3.2.3):**
  - Navigation bar maintains consistent position, links, and styling across all sub-pages.

### 4.4 Principle 4: Robust
- **Compatibility & ARIA Support (WCAG 4.1.2):**
  - Tables include `aria-label` attributes (`aria-label="Enrolled Courses"`, `aria-label="Student Directory"`).
  - Alert banners include `role="alert"` for instant screen-reader announcements upon trigger.

---

## 5. EMPIRICAL SCREENSHOT EVIDENCE & SUBMISSION PROOFS

For practical documentation and verification, the following 9 visual scenarios can be observed directly on the server or via [`standalone_studenthub_demo.html`](standalone_studenthub_demo.html):

| Proof # | Scenario Name | Visual Evidence Description | Key Observed Indicators |
|:---:|---|---|---|
| **Screenshot 1** | **Landing Page Portal Gateway** | System homepage (`index.php`) showing hero banner, architectural pillars, campus notices, and navigation. | CHARUSAT badge, StudentHub logo, Bcrypt & RBAC feature highlights. |
| **Screenshot 2** | **Student Authentication** | Login page (`login.php`) populated with Student Het Patel's credentials (`25cs062@charusat.edu.in`). | Fast-test autofill buttons, accessible input fields, CSRF protection. |
| **Screenshot 3** | **Student Dashboard (Academics)** | Student dashboard (`student_dashboard.php`) after successful login. | Welcome banner with Het Patel, 4 enrolled courses (CE251, CE252, etc.), attendance (94.5%), session timer (05:00). |
| **Screenshot 4** | **Admin Security Console** | Admin dashboard (`admin_dashboard.php`) showing executive management view. | Admin banner (`ADM001`), KPI cards (Students, Courses, Enrollments), Registered Student Directory table. |
| **Screenshot 5** | **RBAC 403 Forbidden Intercept** | Security intercept screen when authenticated Student attempts to access `/admin_dashboard.php`. | HTTP 403 Forbidden badge, "RBAC Access Restriction Violation" headline, security audit diagnostics. |
| **Screenshot 6** | **Session Fixation Defense Proof** | Visual diagnostics comparison showing pre-login session ID destroyed and new session ID allocated. | Pre-auth session ID purged, post-auth session ID active via `session_regenerate_id(true)`. |
| **Screenshot 7** | **Inactivity Session Expiration** | System auto-redirecting to `login.php?msg=timeout` after 300 seconds of inactivity. | Yellow alert banner: "⏳ Session Expired: Inactive for over 300 seconds. Re-login required". |
| **Screenshot 8** | **New Student Registration** | Registration portal (`register.php`) showing live password strength meter and validation. | Form fields (Student ID, Name, Email, Phone, Dept, Passwords) and green strength indicator. |
| **Screenshot 9** | **Live Security Audit Trail** | Admin Console Audit Log table showing real-time event tracking. | Log table displaying `AUTH_SUCCESS`, `RBAC_VIOLATION_BLOCKED`, client IP (`127.0.0.1`), and timestamps. |

---

## 6. CONCLUSION & ACADEMIC CERTIFICATION

The StudentHub Portal successfully satisfies all academic and technical specifications for the Web Development Framework curriculum at CHARUSAT. The application eliminates the OWASP Top 10 primary web attack vectors through defensive architectural engineering, enforces strict separation of concerns via Role-Based Access Control, and provides an inclusive, WCAG 2.1 Level AA compliant user experience.
