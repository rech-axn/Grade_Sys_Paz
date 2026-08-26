<?php
// student/profile.php
$page_title = 'My Profile';
$active_nav = 'profile';

require_once __DIR__ . '/../includes/header.php';
require_student();

$user = current_user();
$pdo = getDBConnection();

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'change_password') {
        $currentPass = trim($_POST['current_password'] ?? '');
        $newPass = trim($_POST['new_password'] ?? '');
        $confirmPass = trim($_POST['confirm_password'] ?? '');

        if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
            set_flash('danger', 'Please complete all password fields.');
        } elseif ($newPass !== $confirmPass) {
            set_flash('danger', 'New password and confirmation do not match.');
        } elseif (strlen($newPass) < 6) {
            set_flash('danger', 'Password must be at least 6 characters long.');
        } else {
            try {
                $stmt = $pdo->prepare("SELECT password FROM `users` WHERE `id` = ?");
                $stmt->execute([$user['id']]);
                $hash = $stmt->fetchColumn();

                if ($hash && password_verify($currentPass, $hash)) {
                    $newHash = password_hash($newPass, PASSWORD_BCRYPT);
                    $updateStmt = $pdo->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
                    $updateStmt->execute([$newHash, $user['id']]);
                    set_flash('success', 'Your password has been changed successfully!');
                } else {
                    set_flash('danger', 'Current password is incorrect.');
                }
            } catch (Exception $e) {
                set_flash('danger', 'Error updating password: ' . $e->getMessage());
            }
        }
        header("Location: profile.php");
        exit();
    }
}

// Fetch student full profile
$stmt = $pdo->prepare("
    SELECT s.*, u.full_name, u.username, u.email, u.created_at
    FROM `students` s
    JOIN `users` u ON s.user_id = u.id
    WHERE s.user_id = ?
    LIMIT 1
");
$stmt->execute([$user['id']]);
$student = $stmt->fetch();
?>

<div class="form-row" style="max-width: 900px; margin: 0 auto;">
    <!-- Profile Info Card -->
    <div class="form-col" style="flex: 1.2;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Student Information</h3>
                    <p class="text-xs text-muted">Official academic registration details</p>
                </div>
                <span class="badge badge-success">Active Enrolled</span>
            </div>

            <div class="flex items-center gap-4 mb-6 p-4 rounded-xl" style="background: var(--surface-light);">
                <div class="avatar" style="width: 54px; height: 54px; font-size: 1.3rem;">
                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($student['full_name']) ?></h4>
                    <span class="font-mono text-xs font-bold text-indigo-600"><?= htmlspecialchars($student['student_id_number']) ?></span>
                </div>
            </div>

            <div class="flex flex-col gap-3 text-sm">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-muted">Username:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['username']) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-muted">Email:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['email'] ?: 'Not specified') ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-muted">Program & Section:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['course_section']) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-muted">Year Level:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['year_level']) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-muted">Gender:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['gender']) ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-muted">Contact Number:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['contact_number'] ?: '—') ?></span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-muted">Address:</span>
                    <span class="font-semibold text-slate-800"><?= htmlspecialchars($student['address'] ?: '—') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Password Card -->
    <div class="form-col" style="flex: 0.9;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Security Settings</h3>
                    <p class="text-xs text-muted">Update your portal login password</p>
                </div>
            </div>

            <form method="POST" action="profile.php">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label">Current Password *</label>
                    <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label class="form-label">New Password *</label>
                    <input type="password" name="new_password" class="form-control" minlength="6" required placeholder="Minimum 6 characters">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password *</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="6" required placeholder="Repeat new password">
                </div>

                <button type="submit" class="btn btn-primary w-full mt-4">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
