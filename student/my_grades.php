<?php
// student/my_grades.php
$page_title = 'My Official Grade Slip';
$active_nav = 'my_grades';

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

foreach ($grades as $g) {
    $totalUnits += $g['units'];
    if ($g['final_grade'] !== null) {
        $weightedSum += ($g['final_grade'] * $g['units']);
        if ($g['final_grade'] >= 75.0) {
            $passedUnits += $g['units'];
        }
    }
}

$gwa = ($totalUnits > 0 && $weightedSum > 0) ? round($weightedSum / $totalUnits, 2) : 0;
?>

<!-- Action Bar (Hidden when printed) -->
<div class="card mb-6 no-print">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-xl font-bold text-slate-800">My Grade Slip & Academic Performance</h2>
            <p class="text-xs text-muted">A.Y. 2025-2026 &bull; First Semester</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Grade Slip
        </button>
    </div>
</div>

<!-- Main Printable Grade Slip Container -->
<div class="card" style="padding: 2.5rem; max-width: 950px; margin: 0 auto;">
    <!-- School Header -->
    <div class="text-center pb-6 mb-6 border-b border-slate-200">
        <div class="sidebar-logo mx-auto mb-2" style="width: 44px; height: 44px;">GS</div>
        <h2 class="text-2xl font-bold text-slate-900">GRADE SYSTEM ACADEMY</h2>
        <p class="text-xs text-muted uppercase tracking-widest mt-1">Student Portal &bull; Official Term Grade Slip</p>
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
            <div class="text-xs text-muted uppercase font-bold">Program & Year</div>
            <div class="font-bold text-slate-800"><?= htmlspecialchars($student['course_section']) ?> (<?= htmlspecialchars($student['year_level']) ?>)</div>

            <div class="text-xs text-muted uppercase font-bold mt-2">Term & Academic Year</div>
            <div class="font-medium text-slate-700">1st Semester, 2025-2026</div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="table-responsive mb-6">
        <table class="table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Descriptive Title</th>
                    <th class="text-center">Units</th>
                    <th class="text-center">Prelim (30%)</th>
                    <th class="text-center">Midterm (30%)</th>
                    <th class="text-center">Finals (40%)</th>
                    <th class="text-center">Final Grade</th>
                    <th class="text-center">Remarks</th>
                    <th>Teacher Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grades)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-6">No grades recorded yet for this term.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grades as $g): ?>
                        <tr>
                            <td class="font-mono font-bold text-slate-800"><?= htmlspecialchars($g['subject_code']) ?></td>
                            <td>
                                <div class="font-semibold text-slate-900"><?= htmlspecialchars($g['subject_title']) ?></div>
                            </td>
                            <td class="text-center font-medium"><?= $g['units'] ?></td>
                            <td class="text-center"><?= $g['prelim'] !== null ? number_format($g['prelim'], 2) : '—' ?></td>
                            <td class="text-center"><?= $g['midterm'] !== null ? number_format($g['midterm'], 2) : '—' ?></td>
                            <td class="text-center"><?= $g['finals'] !== null ? number_format($g['finals'], 2) : '—' ?></td>
                            <td class="text-center font-bold text-slate-900 text-base">
                                <?= $g['final_grade'] !== null ? number_format($g['final_grade'], 2) : '—' ?>
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
                            <td>
                                <span class="text-xs text-muted"><?= htmlspecialchars($g['notes'] ?: '—') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Box -->
    <div class="p-4 rounded-xl border border-slate-200 flex items-center justify-between mb-8" style="background: #f8fafc; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="text-xs text-muted uppercase font-bold">Total Earned Units</div>
            <div class="font-bold text-base text-slate-800"><?= $passedUnits ?> of <?= $totalUnits ?> Credits Passed</div>
        </div>
        <div>
            <div class="text-xs text-muted uppercase font-bold">General Weighted Average (GWA)</div>
            <div class="text-2xl font-extrabold text-indigo-600"><?= $gwa > 0 ? $gwa . '%' : 'N/A' ?></div>
        </div>
        <div>
            <div class="text-xs text-muted uppercase font-bold">Standing</div>
            <div class="font-bold text-base <?= ($gwa >= 75.0) ? 'text-success' : 'text-danger' ?>">
                <?= ($gwa >= 88.0) ? "Dean's Honor List" : (($gwa >= 75.0) ? "In Good Standing" : "Subject for Review") ?>
            </div>
        </div>
    </div>

    <!-- Grading System Scale Guide -->
    <div class="p-4 rounded-xl border border-slate-100 mb-8" style="background: var(--surface-light);">
        <div class="text-xs font-bold text-slate-700 uppercase mb-2">Grading System Legend & Equivalence:</div>
        <div class="flex gap-4 text-xs text-muted" style="flex-wrap: wrap;">
            <span><strong class="text-slate-800">95.00 - 100.00:</strong> 1.00 (Excellent)</span>
            <span><strong class="text-slate-800">90.00 - 94.99:</strong> 1.25 - 1.50 (Superior)</span>
            <span><strong class="text-slate-800">85.00 - 89.99:</strong> 1.75 - 2.00 (Good)</span>
            <span><strong class="text-slate-800">75.00 - 84.99:</strong> 2.25 - 3.00 (Passed)</span>
            <span><strong class="text-danger">Below 75.00:</strong> 5.00 (Failed)</span>
        </div>
    </div>

    <div class="text-center text-xs text-muted pt-4 border-t border-slate-200">
        Generated electronically via GradeSys Paz Portal on <?= date('F j, Y, g:i a') ?>. No physical signature required for unofficial review.
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
