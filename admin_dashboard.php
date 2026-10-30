<?php
/**
 * StudentHub Portal - Administrative Management & Security Console
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
$page_title = "Admin Console";
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/rbac_check.php';

// Enforce Admin Role (blocks Student with 403 Forbidden)
require_role('Admin');

// Fetch Metrics
$student_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Student'")->fetchColumn();
$course_count  = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$enroll_count  = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$notice_count  = $pdo->query("SELECT COUNT(*) FROM notices")->fetchColumn();

// Fetch Registered Students
$students_stmt = $pdo->query("SELECT id, student_id, full_name, email, department, semester, status, created_at FROM users WHERE role = 'Student' ORDER BY id DESC LIMIT 10");
$students = $students_stmt->fetchAll();

// Fetch Courses
$courses_stmt = $pdo->query("SELECT * FROM courses ORDER BY course_code ASC");
$courses = $courses_stmt->fetchAll();

// Fetch Security Audit Logs
$logs_stmt = $pdo->query("SELECT l.*, u.full_name, u.role FROM audit_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.log_id DESC LIMIT 8");
$audit_logs = $logs_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Admin Console Header -->
<div style="background: linear-gradient(135deg, #7f1d1d 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 28px; margin-bottom: 24px; border: 1px solid #ef4444; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <span class="badge badge-danger" style="margin-bottom: 8px;">Privileged Administrative Session</span>
        <h1 style="font-size: 1.8rem; margin: 0 0 6px;">CHARUSAT Admin Console</h1>
        <p style="color: #fca5a5; margin: 0; font-size: 0.95rem;">
            Institutional security management, course dispatch, and student record supervision.
        </p>
    </div>
    <div style="text-align: right; background: rgba(0, 0, 0, 0.4); padding: 12px 20px; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div style="font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Privileged RBAC Scope</div>
        <div style="font-size: 1.4rem; font-weight: 800; color: #f87171;">System Admin</div>
        <div style="font-size: 0.75rem; color: #94a3b8;">User: <?= htmlspecialchars($_SESSION['full_name']) ?></div>
    </div>
</div>

<!-- Admin KPI Summary -->
<div class="stats-grid">
    <div class="stat-card stat-info">
        <div class="stat-label">Total Registered Students</div>
        <div class="stat-val"><?= $student_count ?></div>
        <div class="stat-sub">Active engineering scholars</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Academic Courses</div>
        <div class="stat-val"><?= $course_count ?></div>
        <div class="stat-sub">Accredited university modules</div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-label">Student Enrollments</div>
        <div class="stat-val"><?= $enroll_count ?></div>
        <div class="stat-sub">Across semester cohorts</div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-label">Published Bulletins</div>
        <div class="stat-val"><?= $notice_count ?></div>
        <div class="stat-sub">Campus circulars online</div>
    </div>
</div>

<!-- Student Directory Table -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">👥 Registered Student Directory</h2>
        <span class="badge badge-info">Latest Registrations</span>
    </div>
    <div class="table-responsive">
        <table class="table" aria-label="Student Directory">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>University Email</th>
                    <th>Department</th>
                    <th>Sem</th>
                    <th>Account Status</th>
                    <th>Registered At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $st): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($st['student_id']) ?></strong></td>
                        <td><?= htmlspecialchars($st['full_name']) ?></td>
                        <td><?= htmlspecialchars($st['email']) ?></td>
                        <td><?= htmlspecialchars($st['department']) ?></td>
                        <td>Sem <?= htmlspecialchars($st['semester']) ?></td>
                        <td><span class="badge badge-success"><?= htmlspecialchars($st['status']) ?></span></td>
                        <td><small style="color: var(--text-muted);"><?= date('d M Y', strtotime($st['created_at'])) ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Two-Column Section: Notice Broadcasting & Course Catalog -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Publish Notice Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📢 Broadcast Campus Notice</h3>
        </div>
        <form action="post_notice.php" method="POST">
            <?= csrf_input(); ?>
            <div class="form-group">
                <label for="notice_title" class="form-label">Notice Headline *</label>
                <input type="text" id="notice_title" name="title" class="form-control" required placeholder="e.g. Practical Submission Deadline Extension">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="form-group">
                    <label for="notice_priority" class="form-label">Priority</label>
                    <select id="notice_priority" name="priority" class="form-control">
                        <option value="Normal">Normal</option>
                        <option value="Important" selected>Important</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notice_target" class="form-label">Target Audience</label>
                    <select id="notice_target" name="target_role" class="form-control">
                        <option value="All" selected>All Users</option>
                        <option value="Student">Students Only</option>
                        <option value="Admin">Admins Only</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="notice_content" class="form-label">Bulletin Details *</label>
                <textarea id="notice_content" name="content" class="form-control" rows="3" required placeholder="Enter the official notification announcement..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Broadcast Notice &rarr;</button>
        </form>
    </div>

    <!-- Active Courses Catalog -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📖 Active Courses Directory</h3>
        </div>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.85rem;" aria-label="Course Catalog">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Title</th>
                        <th>Credits</th>
                        <th>Faculty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $cs): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($cs['course_code']) ?></strong></td>
                            <td><?= htmlspecialchars($cs['course_name']) ?></td>
                            <td><?= htmlspecialchars($cs['credits']) ?></td>
                            <td><?= htmlspecialchars($cs['instructor']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Real-Time Security Audit Logs -->
<div class="card" style="border-top: 4px solid #ef4444; margin-top: 24px;">
    <div class="card-header">
        <h2 class="card-title">🛡️ Security Incident & Activity Audit Trail</h2>
        <span class="badge badge-warning">Live System Monitoring</span>
    </div>
    <div class="table-responsive">
        <table class="table" style="font-size: 0.85rem; font-family: monospace;" aria-label="Security Audit Trail">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th>Security Context & Event Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($audit_logs as $log): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($log['log_id']) ?></td>
                        <td>
                            <span class="badge <?= strpos($log['action'], 'BLOCKED') !== false || strpos($log['action'], 'FAILED') !== false ? 'badge-danger' : 'badge-success' ?>">
                                <?= htmlspecialchars($log['action']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($log['full_name'] ?? 'Guest / System') ?></td>
                        <td><?= htmlspecialchars($log['ip_address']) ?></td>
                        <td><?= htmlspecialchars($log['details']) ?></td>
                        <td><?= htmlspecialchars($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 15px; display: flex; justify-content: flex-end;">
        <a href="student_dashboard.php" class="btn btn-outline btn-sm" style="color: #f87171; border-color: #ef4444;" title="Test RBAC restriction by attempting to access student page">
            ⚠️ Test RBAC: Attempt Student Dashboard Access
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
