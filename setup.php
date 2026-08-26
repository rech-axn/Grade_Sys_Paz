<?php
// setup.php - Database Initializer and Seeder
require_once __DIR__ . '/config/database.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    try {
        // Connect to MySQL server without database
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Create Database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `" . DB_NAME . "`");

        // Execute Table Definitions
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `role` ENUM('teacher', 'student') NOT NULL,
                `full_name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `students` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `student_id_number` VARCHAR(50) NOT NULL UNIQUE,
                `course_section` VARCHAR(50) NOT NULL DEFAULT 'BSIT 1-A',
                `year_level` VARCHAR(20) NOT NULL DEFAULT '1st Year',
                `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
                `contact_number` VARCHAR(30) DEFAULT NULL,
                `address` TEXT DEFAULT NULL,
                FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `subjects` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `subject_code` VARCHAR(20) NOT NULL UNIQUE,
                `subject_title` VARCHAR(100) NOT NULL,
                `units` INT NOT NULL DEFAULT 3,
                `semester` VARCHAR(30) NOT NULL DEFAULT '1st Semester',
                `academic_year` VARCHAR(30) NOT NULL DEFAULT '2025-2026',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `grades` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `student_id` INT NOT NULL,
                `subject_id` INT NOT NULL,
                `prelim` DECIMAL(5,2) DEFAULT NULL,
                `midterm` DECIMAL(5,2) DEFAULT NULL,
                `finals` DECIMAL(5,2) DEFAULT NULL,
                `final_grade` DECIMAL(5,2) DEFAULT NULL,
                `remarks` ENUM('Passed', 'Failed', 'Incomplete', 'Pending') DEFAULT 'Pending',
                `notes` VARCHAR(255) DEFAULT NULL,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `unique_student_subject` (`student_id`, `subject_id`),
                FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
                FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Clear existing data if resetting
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE `grades`;");
        $pdo->exec("TRUNCATE TABLE `students`;");
        $pdo->exec("TRUNCATE TABLE `subjects`;");
        $pdo->exec("TRUNCATE TABLE `users`;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Seed Users
        $defaultPassword = password_hash('password123', PASSWORD_BCRYPT);

        // 1. Teacher
        $stmtTeacher = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `role`, `full_name`, `email`) VALUES (?, ?, 'teacher', ?, ?)");
        $stmtTeacher->execute(['teacher', $defaultPassword, 'Prof. Maria Paz Santos', 'teacher@gradesys.edu']);

        // 2. Students users
        $stmtStudentUser = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `role`, `full_name`, `email`) VALUES (?, ?, 'student', ?, ?)");
        
        $stmtStudentUser->execute(['student1', $defaultPassword, 'Juan C. Dela Cruz', 'juan.delacruz@gradesys.edu']);
        $student1_uid = $pdo->lastInsertId();

        $stmtStudentUser->execute(['student2', $defaultPassword, 'Alyssa Jane Reyes', 'alyssa.reyes@gradesys.edu']);
        $student2_uid = $pdo->lastInsertId();

        $stmtStudentUser->execute(['student3', $defaultPassword, 'Mark Anthony Santos', 'mark.santos@gradesys.edu']);
        $student3_uid = $pdo->lastInsertId();

        $stmtStudentUser->execute(['student4', $defaultPassword, 'Sophia Marie Mendoza', 'sophia.mendoza@gradesys.edu']);
        $student4_uid = $pdo->lastInsertId();

        // 3. Students Info
        $stmtStudent = $pdo->prepare("INSERT INTO `students` (`user_id`, `student_id_number`, `course_section`, `year_level`, `gender`, `contact_number`, `address`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmtStudent->execute([$student1_uid, '2026-00101', 'BS Information Technology 1-A', '1st Year', 'Male', '+63 912 345 6789', 'Manila, Philippines']);
        $s1_id = $pdo->lastInsertId();

        $stmtStudent->execute([$student2_uid, '2026-00102', 'BS Information Technology 1-A', '1st Year', 'Female', '+63 918 765 4321', 'Quezon City, Philippines']);
        $s2_id = $pdo->lastInsertId();

        $stmtStudent->execute([$student3_uid, '2026-00103', 'BS Information Technology 1-B', '1st Year', 'Male', '+63 920 112 3344', 'Makati City, Philippines']);
        $s3_id = $pdo->lastInsertId();

        $stmtStudent->execute([$student4_uid, '2026-00104', 'BS Information Technology 1-B', '1st Year', 'Female', '+63 922 998 8776', 'Pasig City, Philippines']);
        $s4_id = $pdo->lastInsertId();

        // 4. Seed Subjects
        $stmtSubject = $pdo->prepare("INSERT INTO `subjects` (`subject_code`, `subject_title`, `units`, `semester`, `academic_year`) VALUES (?, ?, ?, ?, ?)");
        
        $stmtSubject->execute(['IT101', 'Introduction to Computing', 3, '1st Semester', '2025-2026']);
        $sub1_id = $pdo->lastInsertId();

        $stmtSubject->execute(['IT102', 'Computer Programming 1 (Python)', 3, '1st Semester', '2025-2026']);
        $sub2_id = $pdo->lastInsertId();

        $stmtSubject->execute(['IT103', 'Discrete Mathematics', 3, '1st Semester', '2025-2026']);
        $sub3_id = $pdo->lastInsertId();

        $stmtSubject->execute(['GE101', 'Understanding the Self', 3, '1st Semester', '2025-2026']);
        $sub4_id = $pdo->lastInsertId();

        $stmtSubject->execute(['PE101', 'Physical Fitness and Wellness', 2, '1st Semester', '2025-2026']);
        $sub5_id = $pdo->lastInsertId();

        // 5. Seed Grades
        // Helper to compute final grade
        function calcGrade($p, $m, $f) {
            if ($p === null || $m === null || $f === null) return [null, 'Pending'];
            $final = round(($p * 0.3) + ($m * 0.3) + ($f * 0.4), 2);
            $remarks = ($final >= 75.00) ? 'Passed' : 'Failed';
            return [$final, $remarks];
        }

        $gradesData = [
            // Student 1 (Juan)
            [$s1_id, $sub1_id, 88.00, 91.50, 93.00, 'Excellent performance and participation.'],
            [$s1_id, $sub2_id, 92.00, 90.00, 95.00, 'Consistently submits high quality code.'],
            [$s1_id, $sub3_id, 84.00, 86.00, 88.00, 'Good logical reasoning skills.'],
            [$s1_id, $sub4_id, 89.00, 91.00, 90.00, 'Active in group reflections.'],
            [$s1_id, $sub5_id, 95.00, 96.00, 98.00, 'Outstanding physical endurance.'],

            // Student 2 (Alyssa)
            [$s2_id, $sub1_id, 95.00, 96.00, 97.00, 'Dean’s list candidate.'],
            [$s2_id, $sub2_id, 94.00, 95.50, 96.00, 'High aptitude in algorithmic design.'],
            [$s2_id, $sub3_id, 91.00, 93.00, 94.00, 'Strong mathematical background.'],
            [$s2_id, $sub4_id, 92.00, 94.00, 95.00, 'Great communication and teamwork.'],
            [$s2_id, $sub5_id, 90.00, 92.00, 93.00, 'Active participant.'],

            // Student 3 (Mark)
            [$s3_id, $sub1_id, 78.00, 80.00, 82.00, 'Shows steady improvement.'],
            [$s3_id, $sub2_id, 74.00, 76.00, 78.00, 'Needs extra practice with nested loops.'],
            [$s3_id, $sub3_id, 70.00, 73.00, 72.00, 'Needs tutorial on graph theory.'],
            [$s3_id, $sub4_id, 85.00, 87.00, 86.00, 'Very cooperative in class activities.'],
            [$s3_id, $sub5_id, 88.00, 90.00, 89.00, 'Good sportsmanship.'],

            // Student 4 (Sophia)
            [$s4_id, $sub1_id, 90.00, 92.00, 91.00, 'Very attentive in lecture.'],
            [$s4_id, $sub2_id, 86.00, 88.00, 89.00, 'Creative problem solver.'],
            [$s4_id, $sub3_id, 82.00, 85.00, 87.00, 'Solid understanding of proofs.'],
            [$s4_id, $sub4_id, 94.00, 95.00, 96.00, 'Expressive writing skills.'],
            [$s4_id, $sub5_id, 91.00, 93.00, 92.00, 'Punctual and energetic.'],
        ];

        $stmtGrade = $pdo->prepare("INSERT INTO `grades` (`student_id`, `subject_id`, `prelim`, `midterm`, `finals`, `final_grade`, `remarks`, `notes`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($gradesData as $g) {
            list($final, $remarks) = calcGrade($g[2], $g[3], $g[4]);
            $stmtGrade->execute([$g[0], $g[1], $g[2], $g[3], $g[4], $final, $remarks, $g[5]]);
        }

        $status = 'success';
        $message = 'Database and demo data initialized successfully!';
    } catch (Exception $e) {
        $status = 'error';
        $message = 'Installation Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GradeSys Setup & Installation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .setup-card {
            max-width: 650px;
            margin: 40px auto;
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 2.5rem;
            border: 1px solid var(--border-color);
        }
        .account-badge {
            background: var(--surface-light);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-color);
        }
    </style>
</head>
<body class="bg-gradient flex-center min-h-screen p-4">
    <div class="setup-card animate-fade-in">
        <div class="text-center mb-6">
            <div class="logo-badge inline-flex items-center justify-center mb-3">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">GradeSys Paz Initialization</h1>
            <p class="text-muted text-sm mt-1">Automatic Database Schema & Demo Data Setup Wizard</p>
        </div>

        <?php if ($status === 'success'): ?>
            <div class="alert alert-success mb-6">
                <div class="flex items-center gap-2 font-semibold">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Installation Complete!
                </div>
                <p class="text-sm mt-1"><?= htmlspecialchars($message) ?></p>
            </div>

            <h3 class="font-bold text-sm text-slate-700 uppercase tracking-wider mb-3">Ready Demo Accounts:</h3>
            
            <div class="account-badge">
                <div>
                    <span class="badge badge-primary">Teacher</span>
                    <strong class="block text-slate-800 mt-1">Prof. Maria Paz Santos</strong>
                    <span class="text-xs text-muted">User: <code>teacher</code> | Pass: <code>password123</code></span>
                </div>
                <a href="index.php?user=teacher" class="btn btn-sm btn-primary">Login as Teacher</a>
            </div>

            <div class="account-badge">
                <div>
                    <span class="badge badge-secondary">Student 1</span>
                    <strong class="block text-slate-800 mt-1">Juan C. Dela Cruz (2026-00101)</strong>
                    <span class="text-xs text-muted">User: <code>student1</code> | Pass: <code>password123</code></span>
                </div>
                <a href="index.php?user=student1" class="btn btn-sm btn-outline">Login as Student</a>
            </div>

            <div class="account-badge">
                <div>
                    <span class="badge badge-secondary">Student 2</span>
                    <strong class="block text-slate-800 mt-1">Alyssa Jane Reyes (2026-00102)</strong>
                    <span class="text-xs text-muted">User: <code>student2</code> | Pass: <code>password123</code></span>
                </div>
                <a href="index.php?user=student2" class="btn btn-sm btn-outline">Login as Student</a>
            </div>

            <div class="mt-6 flex gap-3">
                <a href="index.php" class="btn btn-primary w-full text-center">Go to Login Page</a>
            </div>

        <?php else: ?>
            <?php if ($status === 'error'): ?>
                <div class="alert alert-danger mb-6">
                    <strong>Error:</strong> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-sm text-slate-600">
                <p class="font-semibold text-slate-800 mb-2">This installer will perform the following actions:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Create the database <code>grade_sys_paz</code> in your local MySQL.</li>
                    <li>Create tables for <code>users</code>, <code>students</code>, <code>subjects</code>, and <code>grades</code>.</li>
                    <li>Seed 1 Teacher account, 4 Student accounts with enrollment info.</li>
                    <li>Seed 5 academic subjects with complete prelim/midterm/final grade entries.</li>
                </ul>
            </div>

            <form method="POST" action="setup.php">
                <input type="hidden" name="action" value="install">
                <button type="submit" class="btn btn-primary w-full py-3 text-base font-semibold shadow-lg shadow-indigo-500/20">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Run Database Setup & Seed Data
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
