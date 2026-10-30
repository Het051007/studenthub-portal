<?php
/**
 * StudentHub Portal - Role-Based Access Control (RBAC) Guard
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

require_once __DIR__ . '/auth_check.php';

/**
 * Require specific role or array of allowed roles
 */
function require_role($allowed_roles) {
    require_login();
    
    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    $user_role = $_SESSION['role'] ?? '';
    
    if (!in_array($user_role, $allowed_roles, true)) {
        // Enforce strict HTTP 403 Forbidden
        http_response_code(403);
        
        // Log unauthorized privilege escalation attempt
        if (isset($GLOBALS['pdo'])) {
            try {
                $stmt = $GLOBALS['pdo']->prepare("INSERT INTO audit_logs (user_id, action, ip_address, details) VALUES (?, 'RBAC_VIOLATION_BLOCKED', ?, ?)");
                $details = sprintf("User '%s' (Role: %s) attempted unauthorized access to restricted endpoint: %s", 
                    $_SESSION['student_id'] ?? 'Unknown',
                    $user_role,
                    $_SERVER['REQUEST_URI'] ?? ''
                );
                $stmt->execute([
                    $_SESSION['user_id'] ?? null,
                    $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    $details
                ]);
            } catch (Exception $e) {
                // Silently bypass audit error to avoid crash
            }
        }
        
        // Render 403 Forbidden Security Intercept Page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 Forbidden - Role Authorization Denied</title>
            <link rel="stylesheet" href="css/style.css">
            <style>
                body {
                    background: #0f172a;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .error-card {
                    background: #1e293b;
                    border: 1px solid #334155;
                    border-top: 5px solid #ef4444;
                    border-radius: 12px;
                    padding: 35px;
                    max-width: 600px;
                    width: 100%;
                    color: #f1f5f9;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
                }
                .badge-danger {
                    background: rgba(239, 68, 68, 0.2);
                    color: #f87171;
                    padding: 6px 12px;
                    border-radius: 999px;
                    font-size: 13px;
                    font-weight: 600;
                    display: inline-block;
                    margin-bottom: 15px;
                }
                .code-box {
                    background: #090d16;
                    border-radius: 8px;
                    padding: 15px;
                    font-family: monospace;
                    font-size: 13px;
                    color: #94a3b8;
                    margin: 20px 0;
                    border: 1px solid #1e293b;
                }
                .btn-return {
                    background: #2563eb;
                    color: white;
                    padding: 10px 20px;
                    border-radius: 8px;
                    text-decoration: none;
                    display: inline-block;
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <div class="error-card">
                <span class="badge-danger">HTTP 403 FORBIDDEN</span>
                <h1 style="margin-top:0; color:#ef4444; font-size:24px;">RBAC Access Restriction Violation</h1>
                <p>Access to this portal resource is strictly restricted based on organizational Role-Based Access Control (RBAC) security policy.</p>
                
                <div class="code-box">
                    <strong>Security Diagnostics:</strong><br>
                    • Authenticated Account : <?= htmlspecialchars($_SESSION['full_name'] ?? 'N/A') ?> (<?= htmlspecialchars($_SESSION['student_id'] ?? 'N/A') ?>)<br>
                    • Current User Role     : <span style="color:#f87171;"><?= htmlspecialchars($user_role) ?></span><br>
                    • Required Role(s)      : <span style="color:#4ade80;"><?= implode(', ', $allowed_roles) ?></span><br>
                    • Access Attempt Status : REJECTED & AUDITED
                </div>

                <p style="color:#94a3b8; font-size:14px;">If you require higher administrative privileges, contact the CHARUSAT System Administrator.</p>
                
                <div style="margin-top: 25px; display:flex; gap:12px;">
                    <?php if ($user_role === 'Student'): ?>
                        <a href="student_dashboard.php" class="btn-return">Return to Student Dashboard</a>
                    <?php elseif ($user_role === 'Admin'): ?>
                        <a href="admin_dashboard.php" class="btn-return">Return to Admin Console</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-return">Back to Login</a>
                    <?php endif; ?>
                    <a href="logout.php" style="color:#94a3b8; padding:10px 15px; text-decoration:none; align-self:center;">Sign Out</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}
?>
