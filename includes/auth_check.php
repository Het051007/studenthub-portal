<?php
/**
 * StudentHub Portal - Authentication Guard, Session Security & CSRF Defense
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

// Configure robust session cookie attributes prior to session start
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax'); // Or Strict
    
    // Check if HTTPS is active
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// Inactivity Session Timeout Duration (300 seconds = 5 minutes)
define('SESSION_TIMEOUT_DURATION', 300);

// Check Inactivity Expiration
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT_DURATION)) {
        // Log inactivity timeout in session
        $expired_user = $_SESSION['full_name'] ?? 'User';
        
        // Purge session data
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        // Redirect with explicit timeout reason
        header("Location: login.php?msg=timeout");
        exit();
    }
    
    // Update timestamp on active interaction
    $_SESSION['last_activity'] = time();
}

/**
 * Generate cryptographically secure CSRF Token
 */
function get_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden HTML CSRF input field
 */
function csrf_input(): string {
    $token = get_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate incoming CSRF Token against session
 */
function verify_csrf_token(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require active authentication
 */
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=unauthorized");
        exit();
    }
}
?>
