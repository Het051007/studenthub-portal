<?php
/**
 * StudentHub Portal - Session Inactivity Timeout Handler
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = [];

// Invalidate cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 86400,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login with timeout notice
header("Location: login.php?msg=timeout");
exit();
?>
