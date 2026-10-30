<?php
/**
 * StudentHub Portal - Student Registration Portal
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
$page_title = "Student Registration";
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

$errors = [];
$form_data = [
    'student_id' => '',
    'full_name'  => '',
    'email'      => '',
    'phone'      => '',
    'department' => 'Computer Science & Engineering',
    'semester'   => 3
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token mismatch. Please resubmit the form.";
    }

    $form_data['student_id'] = strtoupper(trim($_POST['student_id'] ?? ''));
    $form_data['full_name']  = trim($_POST['full_name'] ?? '');
    $form_data['email']      = strtolower(trim($_POST['email'] ?? ''));
    $form_data['phone']      = trim($_POST['phone'] ?? '');
    $form_data['department'] = trim($_POST['department'] ?? 'Computer Science & Engineering');
    $form_data['semester']   = (int)($_POST['semester'] ?? 3);

    $password        = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation Rules
    if (empty($form_data['student_id']) || empty($form_data['full_name']) || empty($form_data['email']) || empty($password)) {
        $errors[] = "All mandatory fields must be completed.";
    }

    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "The email address provided is not in a valid format.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "The confirmation password does not match the entered password.";
    }

    // Duplicate Check & Persistence
    if (empty($errors)) {
        try {
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR student_id = ? LIMIT 1");
            $check_stmt->execute([$form_data['email'], $form_data['student_id']]);
            if ($check_stmt->fetch()) {
                $errors[] = "An account with this Student ID or Email address already exists.";
            } else {
                // Bcrypt Salted Password Hash
                $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

                $insert_stmt = $pdo->prepare("INSERT INTO users (student_id, full_name, email, phone, password_hash, role, department, semester, status) VALUES (?, ?, ?, ?, ?, 'Student', ?, ?, 'Active')");
                $insert_stmt->execute([
                    $form_data['student_id'],
                    $form_data['full_name'],
                    $form_data['email'],
                    $form_data['phone'],
                    $password_hash,
                    $form_data['department'],
                    $form_data['semester']
                ]);

                $new_id = $pdo->lastInsertId();

                // Automatically enroll in core semester 3 course CE251
                $enroll_stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, semester, grade, status) VALUES (?, 1, 3, 'AA', 'Enrolled')");
                $enroll_stmt->execute([$new_id]);

                // Audit Log
                $audit = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address, details) VALUES (?, 'STUDENT_REGISTER', ?, ?)");
                $audit->execute([$new_id, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', "New student registration for " . $form_data['student_id']]);

                header("Location: login.php?msg=registered");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database insertion failed: " . htmlspecialchars($e->getMessage());
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="max-width: 640px; margin: 20px auto;">
    <div class="card" style="padding: 32px; border-top: 5px solid var(--primary);">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-size: 2.2rem; margin-bottom: 8px;">📝</div>
            <h1 style="font-size: 1.6rem; font-weight: 700;">Student Portal Registration</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Create your authenticated student credentials</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul style="margin-left: 20px;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="registrationForm" action="register.php" method="POST" autocomplete="on">
            <?= csrf_input(); ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="student_id" class="form-label">Student ID (e.g. 25CS062) *</label>
                    <input type="text" id="student_id" name="student_id" class="form-control" required 
                           value="<?= htmlspecialchars($form_data['student_id']) ?>" placeholder="25CS062">
                </div>

                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required 
                           value="<?= htmlspecialchars($form_data['full_name']) ?>" placeholder="Patel Het Nitinkumar">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="email" class="form-label">University Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?= htmlspecialchars($form_data['email']) ?>" placeholder="25cs062@charusat.edu.in">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Contact Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($form_data['phone']) ?>" placeholder="9825123456">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="department" class="form-label">Department</label>
                    <select id="department" name="department" class="form-control">
                        <option value="Computer Science & Engineering" selected>Computer Science & Engineering</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Artificial Intelligence">Artificial Intelligence & ML</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester" class="form-label">Semester</label>
                    <select id="semester" name="semester" class="form-control">
                        <option value="1">Sem 1</option>
                        <option value="2">Sem 2</option>
                        <option value="3" selected>Sem 3</option>
                        <option value="4">Sem 4</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Min 8 characters">
                    <div id="strengthIndicator" class="form-text" style="font-weight: 600;"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Re-enter password">
                    <div id="matchError" class="form-text" style="color: #ef4444; display: none;">Passwords do not match!</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 12px; font-size: 1rem; margin-top: 10px;">
                Register Student Account &rarr;
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-secondary);">
            Already registered? <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Sign in here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
