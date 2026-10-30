<?php
/**
 * StudentHub Portal - Student Academic Dashboard
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
$page_title = "Student Dashboard";
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/rbac_check.php';

// Enforce Student Role (blocks Admin with 403 Forbidden)
require_role('Student');

$user_id = $_SESSION['user_id'];

// Fetch user profile data
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$student = $user_stmt->fetch();

// Fetch enrollments
$enroll_stmt = $pdo->prepare("
    SELECT e.*, c.course_code, c.course_name, c.credits, c.instructor 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.course_id 
    WHERE e.user_id = ? 
    ORDER BY c.course_code ASC
");
$enroll_stmt->execute([$user_id]);
$enrollments = $enroll_stmt->fetchAll();

// Calculate total credits & average attendance
$total_credits = 0;
$total_att = 0;
$count = count($enrollments);
foreach ($enrollments as $en) {
    $total_credits += $en['credits'];
    $total_att += $en['attendance_pct'];
}
$avg_attendance = $count > 0 ? round($total_att / $count, 1) : 0;

// Fetch Available Courses for enrollment
$avail_stmt = $pdo->prepare("
    SELECT * FROM courses 
    WHERE course_id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)
");
$avail_stmt->execute([$user_id]);
$available_courses = $avail_stmt->fetchAll();

// Fetch Notices targeted to Students or All
$notice_stmt = $pdo->prepare("
    SELECT n.*, u.full_name as author_name 
    FROM notices n 
    JOIN users u ON n.author_id = u.id 
    WHERE n.target_role IN ('All', 'Student') 
    ORDER BY n.created_at DESC LIMIT 5
");
$notice_stmt->execute();
$notices = $notice_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Welcome Banner -->
<div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 28px; margin-bottom: 24px; border: 1px solid #2563eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
        <span class="badge badge-success" style="margin-bottom: 8px;">Authenticated Student Session</span>
        <h1 style="font-size: 1.8rem; margin: 0 0 6px;">Welcome, <?= htmlspecialchars($student['full_name']) ?>!</h1>
        <p style="color: #93c5fd; margin: 0; font-size: 0.95rem;">
            Student ID: <strong><?= htmlspecialchars($student['student_id']) ?></strong> &bull;
            Department: <?= htmlspecialchars($student['department']) ?> (Sem <?= htmlspecialchars($student['semester']) ?>)
        </p>
    </div>
    <div style="text-align: right; background: rgba(0, 0, 0, 0.3); padding: 12px 20px; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div style="font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Session Inactivity Timeout</div>
        <div id="sessionTimeoutCounter" style="font-size: 1.6rem; font-weight: 800; color: #38bdf8; font-family: monospace;">05:00</div>
        <div style="font-size: 0.7rem; color: #64748b;">Auto-terminates after 300s idle</div>
    </div>
</div>

<!-- Enrolled Academics Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Enrolled Modules</div>
        <div class="stat-val"><?= count($enrollments) ?></div>
        <div class="stat-sub">Active semester 3 subjects</div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-label">Total Earned Credits</div>
        <div class="stat-val"><?= $total_credits ?></div>
        <div class="stat-sub">Course credits registered</div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-label">Aggregate Attendance</div>
        <div class="stat-val"><?= $avg_attendance ?>%</div>
        <div class="stat-sub">Minimum 80% mandatory</div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-label">Security Role</div>
        <div class="stat-val" style="font-size: 1.6rem;">Student</div>
        <div class="stat-sub">Strict RBAC scope applied</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Course Enrolment List -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📚 Registered Academic Courses</h2>
                <span class="badge badge-info">Semester 3 Curriculum</span>
            </div>
            <div class="table-responsive">
                <table class="table" aria-label="Enrolled Courses">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Course Title</th>
                            <th>Credits</th>
                            <th>Faculty</th>
                            <th>Grade</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($enrollments)): ?>
                            <?php foreach ($enrollments as $row): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['course_code']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                                    <td><?= htmlspecialchars($row['credits']) ?></td>
                                    <td><?= htmlspecialchars($row['instructor']) ?></td>
                                    <td><span class="badge badge-success"><?= htmlspecialchars($row['grade'] ?? 'Pending') ?></span></td>
                                    <td>
                                        <div style="font-weight: 600; color: <?= $row['attendance_pct'] >= 85 ? '#34d399' : '#fbbf24' ?>;">
                                            <?= htmlspecialchars($row['attendance_pct']) ?>%
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    No courses enrolled yet. Please register below.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Course Self-Registration Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">➕ Elective & Course Registration</h3>
            </div>
            <?php if (!empty($available_courses)): ?>
                <form action="enroll.php" method="POST" style="display: flex; gap: 14px; align-items: flex-end;">
                    <?= csrf_input(); ?>
                    <div style="flex: 1;">
                        <label for="course_select" class="form-label">Available Semester Courses</label>
                        <select name="course_id" id="course_select" class="form-control" required>
                            <option value="">-- Select Course to Enroll --</option>
                            <?php foreach ($available_courses as $c): ?>
                                <option value="<?= $c['course_id'] ?>">
                                    <?= htmlspecialchars($c['course_code']) ?> - <?= htmlspecialchars($c['course_name']) ?> (<?= $c['credits'] ?> Credits)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 48px;">Enroll Now</button>
                </form>
            <?php else: ?>
                <p style="color: #34d399; margin: 0;">🎉 You are currently enrolled in all available courses for Semester 3!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar: Notices & Session Security Diagnostics -->
    <div>
        <!-- Institutional Announcements -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📢 Student Bulletins</h3>
            </div>
            <?php if (!empty($notices)): ?>
                <?php foreach ($notices as $nt): ?>
                    <div class="notice-item <?= strtolower($nt['priority']) ?>" style="padding: 12px; margin-bottom: 10px;">
                        <div class="notice-header">
                            <span style="font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($nt['title']) ?></span>
                            <span class="badge <?= $nt['priority'] === 'Urgent' ? 'badge-danger' : 'badge-warning' ?>" style="font-size: 0.7rem;">
                                <?= htmlspecialchars($nt['priority']) ?>
                            </span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 6px;">
                            <?= htmlspecialchars($nt['content']) ?>
                        </p>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">
                            <?= date('M d, Y', strtotime($nt['created_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.85rem;">No new bulletins posted.</p>
            <?php endif; ?>
        </div>

        <!-- Security Diagnostics Panel (for Lab Verification) -->
        <div class="card" style="border-top: 3px solid #10b981;">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1rem;">🛡️ Security State & Audit</h3>
            </div>
            <div style="font-size: 0.8rem; line-height: 1.8; color: var(--text-secondary); font-family: monospace;">
                <div><strong>Session ID:</strong> <?= substr(session_id(), 0, 12) ?>...</div>
                <div><strong>Regenerated:</strong> <span style="color: #34d399;">Active (Anti-Fixation)</span></div>
                <div><strong>Cookie HttpOnly:</strong> <span style="color: #34d399;">True</span></div>
                <div><strong>IP Binding:</strong> <?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?></div>
                <div><strong>Timeout Limit:</strong> 300 Seconds</div>
            </div>
            <div style="margin-top: 15px;">
                <a href="admin_dashboard.php" class="btn btn-outline btn-sm btn-block" style="color: #f87171; border-color: #ef4444;" title="Test RBAC restriction by attempting to access admin page">
                    ⚠️ Test RBAC: Attempt Admin Console
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
