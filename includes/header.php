<?php
/**
 * StudentHub Portal - Global Semantic Header
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['role'] ?? null;
$user_name = $_SESSION['full_name'] ?? null;
$student_id = $_SESSION['student_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - StudentHub Portal' : 'StudentHub Portal | CHARUSAT' ?></title>
    <!-- Security meta headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="navbar" role="banner">
        <div class="container nav-container">
            <a href="index.php" class="brand-logo" aria-label="StudentHub Portal Home">
                <span style="font-size: 1.5rem;">🎓</span>
                <span>StudentHub</span>
                <span class="brand-badge">CHARUSAT</span>
            </a>
            <nav role="navigation" aria-label="Main Navigation">
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    
                    <?php if ($is_logged_in): ?>
                        <?php if ($user_role === 'Admin'): ?>
                            <li><a href="admin_dashboard.php" class="nav-link">Admin Console</a></li>
                        <?php else: ?>
                            <li><a href="student_dashboard.php" class="nav-link">My Academics</a></li>
                        <?php endif; ?>
                        
                        <li>
                            <div class="user-badge">
                                <span>👤 <?= htmlspecialchars($user_name) ?></span>
                                <span class="role-tag <?= strtolower($user_role) ?>"><?= htmlspecialchars($user_role) ?></span>
                            </div>
                        </li>
                        <li><a href="logout.php" class="btn btn-outline btn-sm">Sign Out</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn btn-outline btn-sm">Sign In</a></li>
                        <li><a href="register.php" class="btn btn-primary btn-sm">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container" style="padding-top: 32px; padding-bottom: 40px;" role="main">
