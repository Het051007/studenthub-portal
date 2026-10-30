<?php
/**
 * StudentHub Portal - Publish Notice Action
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/rbac_check.php';

require_role('Admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_dashboard.php");
    exit();
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("Security verification failed. CSRF token mismatch.");
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$priority = in_array($_POST['priority'] ?? '', ['Normal', 'Important', 'Urgent']) ? $_POST['priority'] : 'Normal';
$target_role = in_array($_POST['target_role'] ?? '', ['All', 'Student', 'Admin']) ? $_POST['target_role'] : 'All';
$admin_id = $_SESSION['user_id'];

if (!empty($title) && !empty($content)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notices (author_id, title, content, priority, target_role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_id, $title, $content, $priority, $target_role]);

        // Audit Log
        $audit = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address, details) VALUES (?, 'NOTICE_BROADCAST', ?, ?)");
        $audit->execute([$admin_id, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', "Broadcasted notice: " . htmlspecialchars($title)]);
    } catch (PDOException $e) {
        // Handle error
    }
}

header("Location: admin_dashboard.php");
exit();
?>
