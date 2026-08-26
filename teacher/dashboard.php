<?php
// teacher/dashboard.php
$page_title = 'Faculty Dashboard';
$active_nav = 'dashboard';

require_once __DIR__ . '/../includes/header.php';
require_teacher();

$pdo = getDBConnection();

// Fetch summary metrics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM `students`")->fetchColumn();
$totalSubjects = $pdo->query("SELECT COUNT(*) FROM `subjects`")->fetchColumn();

$gradeStats = $pdo->query("
    SELECT 
        AVG(final_grade) AS avg_grade,
        COUNT(*) AS total_grades,
        SUM(CASE WHEN remarks = 'Passed' THEN 1 ELSE 0 END) AS passed_count
    FROM `grades` 
    WHERE final_grade IS NOT NULL
")->fetch();

$avgGrade = $gradeStats['avg_grade'] ? round($gradeStats['avg_grade'], 2) : 0;
$totalGraded = $gradeStats['total_grades'] ?? 0;
$passedCount = $gradeStats['passed_count'] ?? 0;
$passingRate = ($totalGraded > 0) ? round(($passedCount / $totalGraded) * 100, 1) : 0;

// Fetch Recent Grade Entries
$stmtRecent = $pdo->query("
    SELECT g.*, s.student_id_number, u.full_name AS student_name, sub.subject_code, sub.subject_title
    FROM `grades` g
    JOIN `students` s ON g.student_id = s.id
    JOIN `users` u ON s.user_id = u.id
    JOIN `subjects` sub ON g.subject_id = sub.id
    ORDER BY g.updated_at DESC
    LIMIT 6
");
$recentGrades = $stmtRecent->fetchAll();

// Top Performing Students
$stmtTop = $pdo->query("
    SELECT s.id, s.student_id_number, u.full_name, s.course_section, ROUND(AVG(g.final_grade), 2) AS gwa
    FROM `students` s
    JOIN `users` u ON s.user_id = u.id
    JOIN `grades` g ON g.student_id = s.id
    WHERE g.final_grade IS NOT NULL
    GROUP BY s.id, s.student_id_number, u.full_name, s.course_section
    ORDER BY gwa DESC
    LIMIT 5
");
$topStudents = $stmtTop->fetchAll();
?>

<!-- Statistics Overview -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Students</div>
            <div class="stat-value"><?= number_format($totalStudents) ?></div>
            <div class="text-xs text-muted mt-1">Enrolled in department</div>
        </div>
        <div class="stat-icon indigo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Active Subjects</div>
            <div class="stat-value"><?= number_format($totalSubjects) ?></div>
            <div class="text-xs text-muted mt-1">Curriculum courses</div>
        </div>
        <div class="stat-icon cyan">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Class Grade Average</div>
            <div class="stat-value"><?= $avgGrade ?>%</div>
            <div class="text-xs text-muted mt-1">Overall final scores</div>
        </div>
        <div class="stat-icon amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 12l-4-4-4 4"/><path d="M12 16V8"/></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Passing Rate</div>
            <div class="stat-value text-success" style="color: var(--success);"><?= $passingRate ?>%</div>
            <div class="text-xs text-muted mt-1"><?= $passedCount ?> of <?= $totalGraded ?> passing</div>
        </div>
        <div class="stat-icon emerald">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
</div>

<!-- Quick Actions Banner -->
<div class="card mb-6" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #fff;">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-xl font-bold">Welcome back, <?= htmlspecialchars($user['full_name']) ?>!</h2>
            <p class="text-sm mt-1" style="color: #e0e7ff;">Manage your student records, course subjects, and encode term grades effortlessly.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="manage_grades.php" class="btn" style="background: #fff; color: #4f46e5; font-weight: 700;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Encode Grades
            </a>
            <a href="manage_students.php" class="btn" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.25);">
                + Add Student
            </a>
        </div>
    </div>
</div>

<div class="form-row">
    <!-- Recent Grade Updates Table -->
    <div class="form-col" style="flex: 2;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Recent Grade Records</h3>
                    <p class="text-xs text-muted">Latest updated student grades across all subjects</p>
                </div>
                <a href="manage_grades.php" class="btn btn-sm btn-outline">View All</a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Prelim</th>
                            <th>Midterm</th>
                            <th>Finals</th>
                            <th>Final Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentGrades)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No grade records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentGrades as $rg): ?>
                                <tr>
                                    <td>
                                        <div class="font-semibold text-slate-800"><?= htmlspecialchars($rg['student_name']) ?></div>
                                        <div class="text-xs text-muted"><?= htmlspecialchars($rg['student_id_number']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary"><?= htmlspecialchars($rg['subject_code']) ?></span>
                                    </td>
                                    <td><?= $rg['prelim'] !== null ? number_format($rg['prelim'], 2) : '-' ?></td>
                                    <td><?= $rg['midterm'] !== null ? number_format($rg['midterm'], 2) : '-' ?></td>
                                    <td><?= $rg['finals'] !== null ? number_format($rg['finals'], 2) : '-' ?></td>
                                    <td>
                                        <strong class="font-bold text-slate-900"><?= $rg['final_grade'] !== null ? number_format($rg['final_grade'], 2) : '-' ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($rg['remarks'] === 'Passed'): ?>
                                            <span class="badge badge-success">Passed</span>
                                        <?php elseif ($rg['remarks'] === 'Failed'): ?>
                                            <span class="badge badge-danger">Failed</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= htmlspecialchars($rg['remarks']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Students -->
    <div class="form-col" style="flex: 1;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Top Academic Standings</h3>
                    <p class="text-xs text-muted">Students with highest GWA</p>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <?php if (empty($topStudents)): ?>
                    <p class="text-muted text-sm text-center py-4">No student grade averages recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($topStudents as $rank => $ts): ?>
                        <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100" style="background: var(--surface-light);">
                            <div class="flex items-center gap-3">
                                <div class="font-bold text-sm <?= $rank === 0 ? 'text-indigo-600' : 'text-slate-500' ?>" style="width: 20px;">
                                    #<?= $rank + 1 ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-sm text-slate-800"><?= htmlspecialchars($ts['full_name']) ?></div>
                                    <div class="text-xs text-muted"><?= htmlspecialchars($ts['course_section']) ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-primary font-bold"><?= $ts['gwa'] ?>%</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="manage_students.php" class="text-xs font-semibold text-primary">View Student Directory &rarr;</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
