<?php
// student/dashboard.php
$page_title = 'Student Dashboard';
$active_nav = 'dashboard';

require_once __DIR__ . '/../includes/header.php';
require_student();

$user = current_user();
$pdo = getDBConnection();

// Fetch student profile details
$stmtStu = $pdo->prepare("
    SELECT s.*, u.full_name, u.email, u.username
    FROM `students` s
    JOIN `users` u ON s.user_id = u.id
    WHERE s.user_id = ?
    LIMIT 1
");
$stmtStu->execute([$user['id']]);
$student = $stmtStu->fetch();

if (!$student) {
    echo "<div class='alert alert-danger'>Student profile record not found. Please contact the faculty administrator.</div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

// Fetch all grades for this student
$stmtGrades = $pdo->prepare("
    SELECT g.*, sub.subject_code, sub.subject_title, sub.units, sub.semester, sub.academic_year
    FROM `grades` g
    JOIN `subjects` sub ON g.subject_id = sub.id
    WHERE g.student_id = ?
    ORDER BY sub.subject_code ASC
");
$stmtGrades->execute([$student['id']]);
$grades = $stmtGrades->fetchAll();

// Calculations
$totalUnits = 0;
$passedUnits = 0;
$weightedSum = 0;
$gradesRecorded = 0;

foreach ($grades as $g) {
    $totalUnits += $g['units'];
    if ($g['final_grade'] !== null) {
        $weightedSum += ($g['final_grade'] * $g['units']);
        $gradesRecorded++;
        if ($g['final_grade'] >= 75.0) {
            $passedUnits += $g['units'];
        }
    }
}

$gwa = ($totalUnits > 0 && $weightedSum > 0) ? round($weightedSum / $totalUnits, 2) : 0;
$academicStatus = ($gwa >= 88.0) ? "Dean's Lister" : (($gwa >= 75.0) ? "In Good Standing" : ($gwa > 0 ? "Academic Warning" : "Ongoing Term"));
?>

<!-- Welcome Banner -->
<div class="card mb-6" style="background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%); color: #fff;">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="badge" style="background: rgba(255,255,255,0.2); color: #fff; margin-bottom: 0.5rem;">
                <?= htmlspecialchars($student['student_id_number']) ?>
            </div>
            <h2 class="text-2xl font-bold">Hello, <?= htmlspecialchars($student['full_name']) ?>!</h2>
            <p class="text-sm mt-1" style="color: #e0f2fe;">
                <?= htmlspecialchars($student['course_section']) ?> &bull; <?= htmlspecialchars($student['year_level']) ?>
            </p>
        </div>
        <div>
            <a href="my_grades.php" class="btn" style="background: #fff; color: #0284c7; font-weight: 700;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                View Full Grade Slip
            </a>
        </div>
    </div>
</div>

<!-- Student Stat Grid -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">General Weighted Average</div>
            <div class="stat-value text-indigo-600" style="color: var(--primary);"><?= $gwa > 0 ? $gwa . '%' : 'N/A' ?></div>
            <div class="text-xs text-muted mt-1">Weighted by subject units</div>
        </div>
        <div class="stat-icon indigo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 12l-4-4-4 4"/><path d="M12 16V8"/></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Academic Standing</div>
            <div class="stat-value text-base font-bold text-slate-800" style="margin-top: 0.5rem;"><?= $academicStatus ?></div>
            <div class="text-xs text-muted mt-1">A.Y. 2025-2026 Term</div>
        </div>
        <div class="stat-icon emerald">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Units Enrolled / Passed</div>
            <div class="stat-value"><?= $passedUnits ?> <span class="text-sm text-muted font-normal">/ <?= $totalUnits ?> Units</span></div>
            <div class="text-xs text-muted mt-1"><?= count($grades) ?> registered subjects</div>
        </div>
        <div class="stat-icon cyan">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
    </div>
</div>

<!-- Enrolled Subjects & Current Grades Summary -->
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">My Registered Courses & Grades</h3>
            <p class="text-xs text-muted">First Semester, Academic Year 2025-2026</p>
        </div>
        <a href="my_grades.php" class="btn btn-sm btn-outline">Detailed View &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Course Title</th>
                    <th class="text-center">Units</th>
                    <th class="text-center">Prelim</th>
                    <th class="text-center">Midterm</th>
                    <th class="text-center">Finals</th>
                    <th class="text-center">Final Grade</th>
                    <th class="text-center">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grades)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-6">You are not enrolled in any subjects yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grades as $g): ?>
                        <tr>
                            <td>
                                <span class="badge badge-primary font-mono font-bold"><?= htmlspecialchars($g['subject_code']) ?></span>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($g['subject_title']) ?></div>
                            </td>
                            <td class="text-center font-medium"><?= $g['units'] ?></td>
                            <td class="text-center"><?= $g['prelim'] !== null ? number_format($g['prelim'], 2) : '—' ?></td>
                            <td class="text-center"><?= $g['midterm'] !== null ? number_format($g['midterm'], 2) : '—' ?></td>
                            <td class="text-center"><?= $g['finals'] !== null ? number_format($g['finals'], 2) : '—' ?></td>
                            <td class="text-center">
                                <strong class="font-bold text-slate-900">
                                    <?= $g['final_grade'] !== null ? number_format($g['final_grade'], 2) : '—' ?>
                                </strong>
                            </td>
                            <td class="text-center">
                                <?php if ($g['remarks'] === 'Passed'): ?>
                                    <span class="badge badge-success">Passed</span>
                                <?php elseif ($g['remarks'] === 'Failed'): ?>
                                    <span class="badge badge-danger">Failed</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= htmlspecialchars($g['remarks']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
