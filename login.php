<?php
/**
 * StudentHub Portal - Secure Login Gateway
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
$page_title = "Sign In";
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

// Redirect if already authenticated
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'Admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 480px; margin: 20px auto;">
    <div class="card" style="padding: 32px; border-top: 5px solid var(--primary);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 2.2rem; margin-bottom: 8px;">🔐</div>
            <h1 style="font-size: 1.6rem; font-weight: 700;">Account Authentication</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Sign in to access student or admin console</p>
        </div>

        <!-- Feedback Alert Messages -->
        <?php if (isset($_GET['error'])): ?>
            <?php if ($_GET['error'] === 'invalid_credentials'): ?>
                <div class="alert alert-danger" role="alert">
                    <span>⚠️ Invalid email or password. Please verify and try again.</span>
                </div>
            <?php elseif ($_GET['error'] === 'csrf_failed'): ?>
                <div class="alert alert-danger" role="alert">
                    <span>🛑 Security Alert: CSRF token validation failed. Please retry.</span>
                </div>
            <?php elseif ($_GET['error'] === 'unauthorized'): ?>
                <div class="alert alert-warning" role="alert">
                    <span>🔒 Access Denied: You must authenticate first.</span>
                </div>
            <?php elseif ($_GET['error'] === 'empty_fields'): ?>
                <div class="alert alert-warning" role="alert">
                    <span>⚠️ Please enter both your email address and password.</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'logged_out'): ?>
                <div class="alert alert-success" role="alert">
                    <span>✅ You have been securely logged out. Session destroyed.</span>
                </div>
            <?php elseif ($_GET['msg'] === 'timeout'): ?>
                <div class="alert alert-warning" role="alert">
                    <span>⏳ Session Expired: Inactive for over 300 seconds. Re-login required.</span>
                </div>
            <?php elseif ($_GET['msg'] === 'registered'): ?>
                <div class="alert alert-success" role="alert">
                    <span>🎉 Registration completed successfully! Please sign in below.</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Quick Autofill Testing Bar -->
        <div style="background: #0f172a; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 20px; border: 1px dashed var(--border);">
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; text-align: center;">
                ⚡ Fast-Test Autofill (Lab Evaluation)
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="fillCredentials('student')" aria-label="Autofill Student Credentials">
                    🎓 Student Account
                </button>
                <button type="button" class="btn btn-outline btn-sm" onclick="fillCredentials('admin')" aria-label="Autofill Admin Credentials">
                    🛡️ Admin Account
                </button>
            </div>
        </div>

        <form action="authenticate.php" method="POST" autocomplete="on">
            <?= csrf_input(); ?>

            <div class="form-group">
                <label for="loginEmail" class="form-label">University Email Address</label>
                <input type="email" id="loginEmail" name="email" class="form-control" required placeholder="e.g. 25cs062@charusat.edu.in" autocomplete="email">
            </div>

            <div class="form-group">
                <label for="loginPassword" class="form-label">Password</label>
                <input type="password" id="loginPassword" name="password" class="form-control" required placeholder="••••••••" autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; margin-top: 10px; font-size: 1rem;">
                Secure Sign In &rarr;
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-secondary);">
            Don't have an account? <a href="register.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Register as Student</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
