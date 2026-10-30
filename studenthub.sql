-- =======================================================================
-- STUDENTHUB PORTAL - DATABASE DUMP & INITIALIZATION SCRIPT
-- =======================================================================
-- Subject:    Web Development Framework (B.Tech CSE - Semester 3)
-- Institution: Charotar University of Science and Technology (CHARUSAT)
-- Student:     Patel Het Nitinkumar | Student ID: 25CS062
-- Database:    studenthub_db
-- Engine:      MySQL / MariaDB (InnoDB, utf8mb4)
-- =======================================================================

CREATE DATABASE IF NOT EXISTS studenthub_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE studenthub_db;

-- -----------------------------------------------------------------------
-- Disable foreign key checks for clean recreation
-- -----------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notices;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- =======================================================================
-- 1. TABLE: users
-- Core user accounts with RBAC roles, contact details, and Bcrypt hashes
-- =======================================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student',
    department VARCHAR(50) NOT NULL DEFAULT 'Computer Science & Engineering',
    semester INT NOT NULL DEFAULT 3,
    status ENUM('Active', 'Suspended', 'Pending') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_users PRIMARY KEY (id),
    CONSTRAINT uq_student_id UNIQUE (student_id),
    CONSTRAINT uq_user_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================================================================
-- 2. TABLE: courses
-- Academic courses offered in the engineering curriculum
-- =======================================================================
CREATE TABLE courses (
    course_id INT AUTO_INCREMENT NOT NULL,
    course_code VARCHAR(15) NOT NULL UNIQUE,
    course_name VARCHAR(100) NOT NULL,
    credits INT NOT NULL DEFAULT 4,
    instructor VARCHAR(100) NOT NULL,
    department VARCHAR(50) NOT NULL DEFAULT 'Computer Science & Engineering',
    semester INT NOT NULL DEFAULT 3,
    capacity INT NOT NULL DEFAULT 60,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_courses PRIMARY KEY (course_id),
    CONSTRAINT uq_course_code UNIQUE (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================================================================
-- 3. TABLE: enrollments
-- Associative entity mapping students to enrolled courses with grades
-- =======================================================================
CREATE TABLE enrollments (
    enrollment_id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    semester INT NOT NULL DEFAULT 3,
    grade VARCHAR(5) DEFAULT 'A',
    attendance_pct DECIMAL(5,2) DEFAULT 88.50,
    status ENUM('Enrolled', 'Completed', 'Dropped') NOT NULL DEFAULT 'Enrolled',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_enrollments PRIMARY KEY (enrollment_id),
    CONSTRAINT uq_student_course UNIQUE (user_id, course_id),
    CONSTRAINT fk_enrollments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_enrollments_course FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================================================================
-- 4. TABLE: notices
-- Institutional and departmental circulars broadcasted by Admin
-- =======================================================================
CREATE TABLE notices (
    notice_id INT AUTO_INCREMENT NOT NULL,
    author_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    priority ENUM('Normal', 'Important', 'Urgent') NOT NULL DEFAULT 'Normal',
    target_role ENUM('All', 'Student', 'Admin') NOT NULL DEFAULT 'All',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_notices PRIMARY KEY (notice_id),
    CONSTRAINT fk_notices_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================================================================
-- 5. TABLE: audit_logs
-- Security logging for authentication, RBAC violations, and CRUD actions
-- =======================================================================
CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT NOT NULL,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    details TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_audit_logs PRIMARY KEY (log_id),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =======================================================================
-- SEED DATA POPULATION
-- Passwords:
-- Admin:   Admin@123   (Bcrypt Hash: $2y$10$2j9GqL3mV7oN5w8sB.c0w.tH9/bNx9W7H0v8Cw3eI2q7u0A0j6oWe)
-- Student: Student@123 (Bcrypt Hash: $2y$10$0dJpC1rUqKxU7D8uA.a6v.rF6/yKz6T4E7s5Zt0bF9n4s7X7g3lYq)
-- =======================================================================

-- 1. Users
INSERT INTO users (id, student_id, full_name, email, phone, password_hash, role, department, semester, status) VALUES
(1, 'ADM001', 'CHARUSAT Admin Console', 'admin@charusat.edu.in', '9876543210', '$2y$10$2j9GqL3mV7oN5w8sB.c0w.tH9/bNx9W7H0v8Cw3eI2q7u0A0j6oWe', 'Admin', 'Computer Science & Engineering', 0, 'Active'),
(2, '25CS062', 'Patel Het Nitinkumar', '25cs062@charusat.edu.in', '9825123456', '$2y$10$0dJpC1rUqKxU7D8uA.a6v.rF6/yKz6T4E7s5Zt0bF9n4s7X7g3lYq', 'Student', 'Computer Science & Engineering', 3, 'Active'),
(3, '25CS015', 'Shah Aryan Jignesh', '25cs015@charusat.edu.in', '9898112233', '$2y$10$0dJpC1rUqKxU7D8uA.a6v.rF6/yKz6T4E7s5Zt0bF9n4s7X7g3lYq', 'Student', 'Computer Science & Engineering', 3, 'Active'),
(4, '25CS044', 'Desai Ria Krunal', '25cs044@charusat.edu.in', '9712334455', '$2y$10$0dJpC1rUqKxU7D8uA.a6v.rF6/yKz6T4E7s5Zt0bF9n4s7X7g3lYq', 'Student', 'Computer Science & Engineering', 3, 'Active');

-- 2. Courses
INSERT INTO courses (course_id, course_code, course_name, credits, instructor, department, semester, capacity) VALUES
(1, 'CE251', 'Web Development Framework', 4, 'Prof. K. R. Sharma', 'Computer Science & Engineering', 3, 60),
(2, 'CE252', 'Data Structures & Algorithms', 5, 'Dr. P. N. Joshi', 'Computer Science & Engineering', 3, 60),
(3, 'CE253', 'Operating Systems & System Programming', 4, 'Prof. S. M. Patel', 'Computer Science & Engineering', 3, 60),
(4, 'CE254', 'Computer Networks & Protocols', 4, 'Dr. A. V. Trivedi', 'Computer Science & Engineering', 3, 60),
(5, 'MA251', 'Discrete Mathematics & Graph Theory', 4, 'Dr. R. B. Mehta', 'Computer Science & Engineering', 3, 60);

-- 3. Enrollments (Het Patel 25CS062 enrolled in 4 courses)
INSERT INTO enrollments (enrollment_id, user_id, course_id, semester, grade, attendance_pct, status) VALUES
(1, 2, 1, 3, 'AA', 94.50, 'Enrolled'),
(2, 2, 2, 3, 'AB', 91.20, 'Enrolled'),
(3, 2, 3, 3, 'AA', 96.00, 'Enrolled'),
(4, 2, 4, 3, 'BB', 87.50, 'Enrolled'),
(5, 3, 1, 3, 'AB', 89.00, 'Enrolled'),
(6, 3, 2, 3, 'BC', 82.00, 'Enrolled');

-- 4. Institutional Notices
INSERT INTO notices (notice_id, author_id, title, content, priority, target_role) VALUES
(1, 1, 'Final Practical Exam Schedule - Semester 3', 'The practical exam for Web Development Framework (CE251) will be conducted in Lab 304. Ensure your repository is committed and SQL dump is verified.', 'Urgent', 'All'),
(2, 1, 'Microsoft Teams Submission Window Active', 'Upload your integrated practical submission file including GitHub Repo link, SQL dump, README, and security checklist before the deadline.', 'Important', 'Student'),
(3, 1, 'Campus Cyber Security Best Practices', 'All students and staff must ensure 2FA is activated and avoid sharing session cookies or leaving workstations unattended.', 'Normal', 'All');

-- 5. Audit Logs
INSERT INTO audit_logs (user_id, action, ip_address, user_agent, details) VALUES
(1, 'SYSTEM_INIT', '127.0.0.1', 'Mozilla/5.0 System Installer', 'Database tables created and initialized with seed data successfully.'),
(2, 'USER_LOGIN', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Student Patel Het Nitinkumar authenticated successfully. Session ID regenerated.'),
(1, 'USER_LOGIN', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Admin CHARUSAT Admin Console authenticated successfully.');
