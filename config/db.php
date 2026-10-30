<?php
/**
 * StudentHub Portal - Database Configuration & Connection
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

// Database credentials
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Enforce genuine prepared statements (anti-SQLi)
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Return sanitized diagnostic error without leaking internal credentials
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Database Connection Error - StudentHub</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }
            .box { max-width: 600px; margin: 40px auto; background: #1e293b; padding: 30px; border-radius: 12px; border-left: 6px solid #ef4444; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
            h2 { margin-top: 0; color: #ef4444; }
            code { background: #0f172a; padding: 4px 8px; border-radius: 4px; color: #38bdf8; font-size: 14px; }
            .step { margin: 15px 0; line-height: 1.6; }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>⚠️ Database Connection Notice</h2>
            <p>Could not connect to MySQL database <code><?= htmlspecialchars(DB_NAME) ?></code>.</p>
            <div class="step"><strong>To resolve on local XAMPP:</strong></div>
            <ol>
                <li>Open XAMPP Control Panel and start <strong>Apache</strong> and <strong>MySQL</strong>.</li>
                <li>Open <code>http://localhost/phpmyadmin/</code> and create database <code>studenthub_db</code>.</li>
                <li>Import the <code>studenthub.sql</code> file provided in this project root.</li>
                <li>Ensure credentials in <code>config/db.php</code> match (Default: user <code>root</code>, password <code>empty</code>).</li>
            </ol>
            <p><small>Debug details: <?= htmlspecialchars($e->getMessage()) ?></small></p>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
