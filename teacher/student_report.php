<?php
// teacher/student_report.php
$page_title = 'Student Grade Report';
$active_nav = 'students';

require_once __DIR__ . '/../includes/header.php';
require_teacher();

$studentId = intval($_GET['student_id'] ?? 0);
if ($studentId <= 0) {
    header("Location: manage_students.php");
    exit();
}

$pdo = getDBConnection();

// Fetch student profile
$stmtStu = $pdo->prepare("
    SELECT s.*, u.full_name, u.email, u.username
    FROM `students` s
    JOIN `users` u ON s.user_id = u.id
    WHERE s.id = ?
    LIMIT 1
");
$stmtStu->execute([$studentId]);
$student = $stmtStu->fetch();

if (!$student) {
    die("Student not found.");
}

// Fetch all grades for this student
$stmtGrades = $pdo->prepare("
    SELECT g.*, sub.subject_code, sub.subject_title, sub.units, sub.semester, sub.academic_year
    FROM `grades` g
    JOIN `subjects` sub ON g.subject_id = sub.id
    WHERE g.student_id = ?
    ORDER BY sub.subject_code ASC
");
$stmtGrades->execute([$studentId]);
$grades = $stmtGrades->fetchAll();

// Weighted calculation
$totalUnits = 0;
$passedUnits = 0;
$weightedSum = 0;
$hasIncomplete = false;

foreach ($grades as $g) {
    $totalUnits += $g['units'];
    if ($g['final_grade'] !== null) {
        $weightedSum += ($g['final_grade'] * $g['units']);
        if ($g['final_grade'] >= 75.0) {
            $passedUnits += $g['units'];
        }
    } else {
        $hasIncomplete = true;
    }
}

$gwa = ($totalUnits > 0 && $weightedSum > 0) ? round($weightedSum / $totalUnits, 2) : 0;
?>

<div class="card mb-6 no-print">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div class="flex items-center gap-3">
            <a href="manage_students.php" class="btn btn-secondary btn-sm">&larr; Back to Directory</a>
            <h2 class="text-xl font-bold text-slate-800">Academic Grade Report</h2>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Report Card
            </button>
        </div>
    </div>
</div>

<!-- Printable Student Report Card -->
<div class="card" style="padding: 2.5rem; max-width: 900px; margin: 0 auto;">
    <!-- Institution Header -->
    <div class="text-center pb-6 mb-6 border-b border-slate-200">
        <div class="sidebar-logo mx-auto mb-2" style="width: 44px; height: 44px;">GS</div>
        <h2 class="text-2xl font-bold text-slate-900">GRADE SYSTEM ACADEMY</h2>
        <p class="text-xs text-muted uppercase tracking-widest mt-1">Office of Academic Records & Evaluation</p>
        <p class="text-sm font-semibold text-slate-700 mt-2">Official Certificate of Student Grades</p>
    </div>

    <!-- Student Metadata -->
    <div class="stat-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 2rem; gap: 1rem;">
        <div class="p-4 rounded-xl border border-slate-100" style="background: var(--surface-light);">
            <div class="text-xs text-muted uppercase font-bold">Student Name</div>
            <div class="text-lg font-bold text-slate-900"><?= htmlspecialchars($student['full_name']) ?></div>
            
            <div class="text-xs text-muted uppercase font-bold mt-2">Student ID Number</div>
            <div class="font-mono font-bold text-indigo-600"><?= htmlspecialchars($student['student_id_number']) ?></div>
        </div>

        <div class="p-4 rounded-xl border border-slate-100" style="background: var(--surface-light);">
            <div class="text-xs text-muted uppercase font-bold">Course & Section</div>
            <div class="font-bold text-slate-800"><?= htmlspecialchars($student['course_section']) ?> (<?= htmlspecialchars($student['year_level']) ?>)</div>

            <div class="text-xs text-muted uppercase font-bold mt-2">Academic Term</div>
            <div class="font-medium text-slate-700">A.Y. 2025-2026 | 1st Semester</div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="table-responsive mb-6">
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
                        <td colspan="8" class="text-center text-muted py-6">No grades recorded for this student yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grades as $g): ?>
                        <tr>
                            <td class="font-mono font-bold text-slate-800"><?= htmlspecialchars($g['subject_code']) ?></td>
                            <td><?= htmlspecialchars($g['subject_title']) ?></td>
                            <td class="text-center font-medium"><?= $g['units'] ?></td>
                            <td class="text-center"><?= $g['prelim'] !== null ? number_format($g['prelim'], 2) : '-' ?></td>
                            <td class="text-center"><?= $g['midterm'] !== null ? number_format($g['midterm'], 2) : '-' ?></td>
                            <td class="text-center"><?= $g['finals'] !== null ? number_format($g['finals'], 2) : '-' ?></td>
                            <td class="text-center font-bold text-slate-900">
                                <?= $g['final_grade'] !== null ? number_format($g['final_grade'], 2) : '-' ?>
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

    <!-- Summary Box -->
    <div class="p-4 rounded-xl border border-slate-200 flex items-center justify-between" style="background: #f8fafc; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="text-xs text-muted uppercase font-bold">Total Enrolled Units</div>
            <div class="font-bold text-base text-slate-800"><?= $totalUnits ?> Credits (<?= $passedUnits ?> Passed)</div>
        </div>
        <div>
            <div class="text-xs text-muted uppercase font-bold">General Weighted Average (GWA)</div>
            <div class="text-2xl font-extrabold text-indigo-600"><?= $gwa > 0 ? $gwa . '%' : 'N/A' ?></div>
        </div>
        <div>
            <div class="text-xs text-muted uppercase font-bold">Academic Evaluation</div>
            <div class="font-bold text-base <?= ($gwa >= 75.0) ? 'text-success' : 'text-danger' ?>">
                <?= ($gwa >= 88.0) ? 'Dean’s Honor List' : (($gwa >= 75.0) ? 'In Good Standing' : 'Needs Academic Review') ?>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="pt-12 mt-12 border-t border-slate-200 flex justify-between" style="gap: 2rem;">
        <div class="text-center" style="flex: 1;">
            <div class="border-b border-slate-400 pb-1 mb-2 font-bold text-slate-800"><?= htmlspecialchars($user['full_name']) ?></div>
            <div class="text-xs text-muted uppercase">Faculty Adviser / Instructor</div>
        </div>
        <div class="text-center" style="flex: 1;">
            <div class="border-b border-slate-400 pb-1 mb-2 font-bold text-slate-800">Dr. Emmanuel Reyes, Ph.D.</div>
            <div class="text-xs text-muted uppercase">University Registrar</div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
