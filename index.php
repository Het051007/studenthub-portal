<?php
/**
 * StudentHub Portal - Landing Page & Portal Gateway
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
$page_title = "Welcome to StudentHub";
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/header.php';

// Fetch public announcements
$public_notices = [];
try {
    $stmt = $pdo->query("SELECT n.*, u.full_name as author_name FROM notices n JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC LIMIT 3");
    $public_notices = $stmt->fetchAll();
} catch (Exception $e) {
    // If DB is not yet populated
}
?>

<div style="text-align: center; margin-bottom: 40px; padding: 30px 20px; background: linear-gradient(180deg, rgba(37, 99, 235, 0.1) 0%, rgba(15, 23, 42, 0) 100%); border-radius: var(--radius-lg); border: 1px solid var(--border);">
    <span class="badge badge-info" style="margin-bottom: 15px; font-size: 0.85rem; padding: 6px 14px;">CHARUSAT CSPIT &bull; Web Development Framework Final Practical</span>
    <h1 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px;">StudentHub Academic Portal</h1>
    <p style="font-size: 1.15rem; color: var(--text-secondary); max-width: 720px; margin: 0 auto 24px;">
        An integrated academic course registration, role-based access control, and notification platform engineered with modern security defenses (OWASP, Bcrypt, Session Fixation Immunity).
    </p>

    <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
        <?php if ($is_logged_in): ?>
            <?php if ($user_role === 'Admin'): ?>
                <a href="admin_dashboard.php" class="btn btn-primary">Open Admin Console &rarr;</a>
            <?php else: ?>
                <a href="student_dashboard.php" class="btn btn-primary">Open Student Dashboard &rarr;</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary" style="padding: 12px 28px; font-size: 1rem;">Student & Admin Login</a>
            <a href="register.php" class="btn btn-outline" style="padding: 12px 28px; font-size: 1rem;">New Student Registration</a>
        <?php endif; ?>
        <a href="standalone_studenthub_demo.html" target="_blank" class="btn btn-outline" style="border-color: #38bdf8; color: #38bdf8;">
            ⚡ Launch Zero-Server Interactive Simulator
        </a>
    </div>
</div>

<!-- Core Architectural Pillars -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Security Protocol</div>
        <div class="stat-val" style="font-size: 1.4rem; color: #38bdf8;">Bcrypt & Salt</div>
        <div class="stat-sub">password_hash with adaptive cost prevents rainbow-table lookups.</div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-label">Authorization</div>
        <div class="stat-val" style="font-size: 1.4rem; color: #34d399;">RBAC Enforced</div>
        <div class="stat-sub">Admin & Student role separation with HTTP 403 Forbidden traps.</div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-label">Session Guard</div>
        <div class="stat-val" style="font-size: 1.4rem; color: #fbbf24;">Anti-Fixation</div>
        <div class="stat-sub">session_regenerate_id(true) & 300s idle timeout defense.</div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-label">Database Guard</div>
        <div class="stat-val" style="font-size: 1.4rem; color: #a78bfa;">Prepared PDO</div>
        <div class="stat-sub">100% Parameterized queries guaranteeing SQLi immunity.</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 10px;">
    <!-- Public Bulletins -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📢 Latest Campus Notices</h2>
            <span class="badge badge-info">Live Updates</span>
        </div>
        
        <?php if (!empty($public_notices)): ?>
            <?php foreach ($public_notices as $n): ?>
                <div class="notice-item <?= strtolower($n['priority']) ?>">
                    <div class="notice-header">
                        <span class="notice-title"><?= htmlspecialchars($n['title']) ?></span>
                        <span class="badge <?= $n['priority'] === 'Urgent' ? 'badge-danger' : ($n['priority'] === 'Important' ? 'badge-warning' : 'badge-info') ?>">
                            <?= htmlspecialchars($n['priority']) ?>
                        </span>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 8px;">
                        <?= nl2br(htmlspecialchars($n['content'])) ?>
                    </p>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                        Published by <?= htmlspecialchars($n['author_name']) ?> &bull; <?= date('M d, Y h:i A', strtotime($n['created_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-muted);">No broadcast notices currently posted or database pending import.</p>
        <?php endif; ?>
    </div>

    <!-- Quick Submission Reference Card -->
    <div class="card" style="border-top: 4px solid var(--primary);">
        <div class="card-header">
            <h3 class="card-title" style="font-size: 1.1rem;">📋 Submission Checklist</h3>
        </div>
        <ul style="list-style: none; font-size: 0.9rem; line-height: 1.8; color: var(--text-secondary);">
            <li>✅ <strong>Integrated Codebase:</strong> PHP + PDO + MySQL</li>
            <li>✅ <strong>Database Dump:</strong> <code>studenthub.sql</code></li>
            <li>✅ <strong>Setup Guide:</strong> <code>README.md</code></li>
            <li>✅ <strong>Audit & Accessibility:</strong> <code>TEST_SECURITY_REPORT.md</code></li>
            <li>✅ <strong>Interactive Demo:</strong> <code>standalone_studenthub_demo.html</code></li>
        </ul>
        <div style="margin-top: 20px; padding: 12px; background: #0f172a; border-radius: var(--radius-sm); font-size: 0.8rem; color: var(--text-muted);">
            <strong>Student:</strong> Patel Het Nitinkumar<br>
            <strong>ID:</strong> 25CS062 | <strong>Sem:</strong> 3 (CSE)<br>
            <strong>Course:</strong> Web Dev Framework
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
