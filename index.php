<?php
// index.php - Login Page
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    $user = current_user();
    if ($user['role'] === 'teacher') {
        header("Location: " . getBaseUrl() . "/teacher/dashboard.php");
    } else {
        header("Location: " . getBaseUrl() . "/student/dashboard.php");
    }
    exit();
}

$error = '';
$prefillUser = $_GET['user'] ?? '';
$prefillPass = ($prefillUser !== '') ? 'password123' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `username` = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];

                if ($user['role'] === 'student') {
                    $stmtStu = $pdo->prepare("SELECT id FROM `students` WHERE `user_id` = ? LIMIT 1");
                    $stmtStu->execute([$user['id']]);
                    $stu = $stmtStu->fetch();
                    $_SESSION['student_id'] = $stu ? $stu['id'] : null;
                    header("Location: " . getBaseUrl() . "/student/dashboard.php");
                } else {
                    header("Location: " . getBaseUrl() . "/teacher/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid username or password. Please try again.";
            }
        } catch (Exception $e) {
            $error = "System Error: " . $e->getMessage() . ". Have you run the setup wizard?";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GradeSys Paz Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-bg-glow"></div>

    <div class="login-card animate-fade-in">
        <div class="text-center mb-6">
            <div class="sidebar-logo mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.4rem; margin: 0 auto 1rem;">GS</div>
            <h1 class="text-2xl font-bold text-slate-900">GradeSys Paz</h1>
            <p class="text-muted text-sm mt-1">Student & Faculty Grading Portal</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-4">
                <div class="flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
            <div class="alert alert-warning mb-4">
                <span>Please log in to access this portal.</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" value="<?= htmlspecialchars($prefillUser) ?>" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" value="<?= htmlspecialchars($prefillPass) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary w-full py-3 mt-2 font-semibold">
                Sign In to Portal
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-200">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 text-center">Quick Demo Login:</div>
            <div class="flex flex-col gap-2">
                <button type="button" class="demo-account-pill flex items-center justify-between" onclick="fillCreds('teacher', 'password123')">
                    <span class="font-medium text-slate-800">🧑‍🏫 Teacher: Prof. Maria Santos</span>
                    <code class="text-xs text-indigo-600">teacher</code>
                </button>
                <button type="button" class="demo-account-pill flex items-center justify-between" onclick="fillCreds('student1', 'password123')">
                    <span class="font-medium text-slate-800">🎓 Student: Juan Dela Cruz</span>
                    <code class="text-xs text-indigo-600">student1</code>
                </button>
                <button type="button" class="demo-account-pill flex items-center justify-between" onclick="fillCreds('student2', 'password123')">
                    <span class="font-medium text-slate-800">🎓 Student: Alyssa Jane Reyes</span>
                    <code class="text-xs text-indigo-600">student2</code>
                </button>
            </div>

            <div class="mt-4 text-center">
                <a href="setup.php" class="text-xs text-muted hover:text-indigo-600">Need to install or reset database? Run Setup</a>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(u, p) {
            document.getElementById('username').value = u;
            document.getElementById('password').value = p;
        }
    </script>
</body>
</html>
