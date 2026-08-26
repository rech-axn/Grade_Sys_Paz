<?php
// teacher/manage_grades.php
$page_title = 'Grade Sheet Matrix';
$active_nav = 'grades';

require_once __DIR__ . '/../includes/header.php';
require_teacher();

$pdo = getDBConnection();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save_grade') {
        $studentId = intval($_POST['student_id'] ?? 0);
        $subjectId = intval($_POST['subject_id'] ?? 0);
        $prelim = $_POST['prelim'] !== '' ? floatval($_POST['prelim']) : null;
        $midterm = $_POST['midterm'] !== '' ? floatval($_POST['midterm']) : null;
        $finals = $_POST['finals'] !== '' ? floatval($_POST['finals']) : null;
        $notes = trim($_POST['notes'] ?? '');

        // Calculation: 30% Prelim, 30% Midterm, 40% Finals
        $finalGrade = null;
        $remarks = 'Pending';

        if ($prelim !== null && $midterm !== null && $finals !== null) {
            $finalGrade = round(($prelim * 0.3) + ($midterm * 0.3) + ($finals * 0.4), 2);
            $remarks = ($finalGrade >= 75.0) ? 'Passed' : 'Failed';
        }

        if ($studentId <= 0 || $subjectId <= 0) {
            set_flash('danger', 'Please select both student and subject.');
        } else {
            try {
                // Upsert (Insert or Update on Duplicate)
                $sql = "
                    INSERT INTO `grades` (`student_id`, `subject_id`, `prelim`, `midterm`, `finals`, `final_grade`, `remarks`, `notes`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        `prelim` = VALUES(`prelim`),
                        `midterm` = VALUES(`midterm`),
                        `finals` = VALUES(`finals`),
                        `final_grade` = VALUES(`final_grade`),
                        `remarks` = VALUES(`remarks`),
                        `notes` = VALUES(`notes`)
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$studentId, $subjectId, $prelim, $midterm, $finals, $finalGrade, $remarks, $notes]);

                set_flash('success', 'Grade record saved and computed successfully!');
            } catch (Exception $e) {
                set_flash('danger', 'Error saving grade: ' . $e->getMessage());
            }
        }
        $filterSub = $subjectId ? "?subject_id={$subjectId}" : "";
        header("Location: manage_grades.php{$filterSub}");
        exit();
    }

    if ($action === 'delete_grade') {
        $gradeId = intval($_POST['grade_id'] ?? 0);
        if ($gradeId > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `grades` WHERE `id` = ?");
                $stmt->execute([$gradeId]);
                set_flash('success', 'Grade record removed.');
            } catch (Exception $e) {
                set_flash('danger', 'Error deleting grade: ' . $e->getMessage());
            }
        }
        header("Location: manage_grades.php");
        exit();
    }
}

// Filters
$selectedSubject = isset($_GET['subject_id']) && $_GET['subject_id'] !== '' ? intval($_GET['subject_id']) : null;
$selectedStudent = isset($_GET['student_id']) && $_GET['student_id'] !== '' ? intval($_GET['student_id']) : null;

// Fetch all subjects and students for dropdowns
$allSubjects = $pdo->query("SELECT id, subject_code, subject_title FROM `subjects` ORDER BY subject_code ASC")->fetchAll();
$allStudents = $pdo->query("
    SELECT s.id, s.student_id_number, u.full_name 
    FROM `students` s 
    JOIN `users` u ON s.user_id = u.id 
    ORDER BY u.full_name ASC
")->fetchAll();

// Build query for grade records
$whereConditions = [];
$params = [];

if ($selectedSubject) {
    $whereConditions[] = "g.subject_id = ?";
    $params[] = $selectedSubject;
}
if ($selectedStudent) {
    $whereConditions[] = "g.student_id = ?";
    $params[] = $selectedStudent;
}

$whereSql = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

$query = "
    SELECT g.*, 
           s.id AS student_tbl_id, s.student_id_number, s.course_section,
           u.full_name AS student_name,
           sub.id AS subject_tbl_id, sub.subject_code, sub.subject_title, sub.units
    FROM `grades` g
    JOIN `students` s ON g.student_id = s.id
    JOIN `users` u ON s.user_id = u.id
    JOIN `subjects` sub ON g.subject_id = sub.id
    {$whereSql}
    ORDER BY sub.subject_code ASC, u.full_name ASC
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$gradesList = $stmt->fetchAll();

// Compute stats for current view
$currentGradesCount = count($gradesList);
$currentFinals = array_filter(array_column($gradesList, 'final_grade'), fn($val) => $val !== null);
$currentAvg = !empty($currentFinals) ? round(array_sum($currentFinals) / count($currentFinals), 2) : 0;
$highestGrade = !empty($currentFinals) ? max($currentFinals) : 0;
$lowestGrade = !empty($currentFinals) ? min($currentFinals) : 0;
?>

<!-- Action & Filter Bar -->
<div class="card mb-6 no-print">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Master Grade Matrix</h2>
            <p class="text-xs text-muted">Formula: (Prelim × 30%) + (Midterm × 30%) + (Finals × 40%) = Final Grade</p>
        </div>

        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
            <button type="button" class="btn btn-primary" onclick="openModal('encodeGradeModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Encode / Edit Grade
            </button>
            <button type="button" class="btn btn-secondary" onclick="window.print()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Grade Sheet
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="manage_grades.php" class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-3" style="flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <select name="subject_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- All Subjects --</option>
                <?php foreach ($allSubjects as $sub): ?>
                    <option value="<?= $sub['id'] ?>" <?= $selectedSubject == $sub['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sub['subject_code']) ?> - <?= htmlspecialchars($sub['subject_title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 200px;">
            <select name="student_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- All Students --</option>
                <?php foreach ($allStudents as $stu): ?>
                    <option value="<?= $stu['id'] ?>" <?= $selectedStudent == $stu['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($stu['full_name']) ?> (<?= htmlspecialchars($stu['student_id_number']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <input type="text" class="form-control" style="width: 220px;" placeholder="Search records..." data-table-search="gradeMatrixTable">
        </div>

        <?php if ($selectedSubject || $selectedStudent): ?>
            <a href="manage_grades.php" class="btn btn-secondary btn-sm">Reset Filters</a>
        <?php endif; ?>
    </form>
</div>

<!-- Print Header (Visible during Print only) -->
<div class="print-header">
    <h2 class="text-2xl font-bold">GradeSys Paz - Faculty Master Grade Sheet</h2>
    <p class="text-sm text-muted">Academic Year 2025-2026 | First Semester</p>
    <p class="text-xs text-muted">Generated by: <?= htmlspecialchars($user['full_name']) ?> on <?= date('F j, Y, g:i a') ?></p>
</div>

<!-- Summary Mini Cards -->
<div class="stat-grid no-print" style="margin-bottom: 1.5rem;">
    <div class="stat-card" style="padding: 1rem 1.25rem;">
        <div class="stat-info">
            <div class="stat-label">Displayed Records</div>
            <div class="stat-value text-xl"><?= $currentGradesCount ?></div>
        </div>
    </div>
    <div class="stat-card" style="padding: 1rem 1.25rem;">
        <div class="stat-info">
            <div class="stat-label">Section Average</div>
            <div class="stat-value text-xl"><?= $currentAvg ?>%</div>
        </div>
    </div>
    <div class="stat-card" style="padding: 1rem 1.25rem;">
        <div class="stat-info">
            <div class="stat-label">Highest Score</div>
            <div class="stat-value text-xl text-success" style="color: var(--success);"><?= $highestGrade ?>%</div>
        </div>
    </div>
    <div class="stat-card" style="padding: 1rem 1.25rem;">
        <div class="stat-info">
            <div class="stat-label">Lowest Score</div>
            <div class="stat-value text-xl text-danger" style="color: var(--danger);"><?= $lowestGrade ?>%</div>
        </div>
    </div>
</div>

<!-- Grade Matrix Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table" id="gradeMatrixTable">
            <thead>
                <tr>
                    <th>Student Name & ID</th>
                    <th>Course & Section</th>
                    <th>Subject</th>
                    <th class="text-center">Prelim (30%)</th>
                    <th class="text-center">Midterm (30%)</th>
                    <th class="text-center">Finals (40%)</th>
                    <th class="text-center">Final Grade</th>
                    <th class="text-center">Remarks</th>
                    <th>Notes / Feedback</th>
                    <th class="text-right no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gradesList)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-8">
                            No grade records found matching the filter criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($gradesList as $row): ?>
                        <tr>
                            <td>
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($row['student_name']) ?></div>
                                <div class="text-xs text-muted font-mono"><?= htmlspecialchars($row['student_id_number']) ?></div>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600"><?= htmlspecialchars($row['course_section']) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-primary font-bold"><?= htmlspecialchars($row['subject_code']) ?></span>
                                <div class="text-xs text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($row['subject_title']) ?>
                                </div>
                            </td>
                            <td class="text-center font-medium"><?= $row['prelim'] !== null ? number_format($row['prelim'], 2) : '-' ?></td>
                            <td class="text-center font-medium"><?= $row['midterm'] !== null ? number_format($row['midterm'], 2) : '-' ?></td>
                            <td class="text-center font-medium"><?= $row['finals'] !== null ? number_format($row['finals'], 2) : '-' ?></td>
                            <td class="text-center">
                                <span class="font-bold text-base <?= ($row['final_grade'] !== null && $row['final_grade'] >= 75) ? 'text-slate-900' : 'text-danger' ?>">
                                    <?= $row['final_grade'] !== null ? number_format($row['final_grade'], 2) : '-' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($row['remarks'] === 'Passed'): ?>
                                    <span class="badge badge-success">Passed</span>
                                <?php elseif ($row['remarks'] === 'Failed'): ?>
                                    <span class="badge badge-danger">Failed</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= htmlspecialchars($row['remarks']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-xs text-muted"><?= htmlspecialchars($row['notes'] ?: '—') ?></span>
                            </td>
                            <td class="text-right no-print">
                                <div class="flex items-center gap-2" style="justify-content: flex-end;">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick='editGradeRecord(<?= json_encode($row) ?>)' title="Quick Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <form method="POST" action="manage_grades.php" onsubmit="return confirm('Remove grade entry?');" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete_grade">
                                        <input type="hidden" name="grade_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Entry">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Encode / Edit Grade Modal -->
<div class="modal-overlay" id="encodeGradeModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="font-bold text-lg text-slate-800" id="modal_grade_title">Encode Student Grade</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('encodeGradeModal')">&times;</button>
        </div>
        <form method="POST" action="manage_grades.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="save_grade">

            <div class="modal-body grade-calc-group">
                <div class="form-group">
                    <label class="form-label">Select Student *</label>
                    <select name="student_id" id="modal_student_id" class="form-control" required>
                        <option value="">-- Choose Student --</option>
                        <?php foreach ($allStudents as $stu): ?>
                            <option value="<?= $stu['id'] ?>"><?= htmlspecialchars($stu['full_name']) ?> (<?= htmlspecialchars($stu['student_id_number']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Select Subject *</label>
                    <select name="subject_id" id="modal_subject_id" class="form-control" required>
                        <option value="">-- Choose Subject --</option>
                        <?php foreach ($allSubjects as $sub): ?>
                            <option value="<?= $sub['id'] ?>" <?= $selectedSubject == $sub['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sub['subject_code']) ?> - <?= htmlspecialchars($sub['subject_title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Prelim (30%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="prelim" id="modal_prelim" class="form-control calc-prelim" placeholder="0.00">
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Midterm (30%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="midterm" id="modal_midterm" class="form-control calc-midterm" placeholder="0.00">
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Finals (40%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="finals" id="modal_finals" class="form-control calc-finals" placeholder="0.00">
                    </div>
                </div>

                <!-- Real-time Live Calculation Box -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between mb-4">
                    <div>
                        <span class="text-xs text-muted uppercase font-bold block">Computed Final Grade</span>
                        <input type="text" readonly class="calc-final-grade font-bold text-lg text-indigo-600 bg-transparent border-0" value="—" style="outline:none; width: 100px;">
                    </div>
                    <div>
                        <span class="text-xs text-muted uppercase font-bold block text-right">Status</span>
                        <span class="calc-remarks badge badge-secondary">Pending</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Teacher's Remarks / Feedback</label>
                    <input type="text" name="notes" id="modal_notes" class="form-control" placeholder="e.g. Excellent active participation in lab sessions">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('encodeGradeModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save & Calculate</button>
            </div>
        </form>
    </div>
</div>

<script>
function editGradeRecord(row) {
    document.getElementById('modal_grade_title').textContent = 'Edit Grade for ' + row.student_name;
    document.getElementById('modal_student_id').value = row.student_tbl_id;
    document.getElementById('modal_subject_id').value = row.subject_tbl_id;
    document.getElementById('modal_prelim').value = row.prelim !== null ? row.prelim : '';
    document.getElementById('modal_midterm').value = row.midterm !== null ? row.midterm : '';
    document.getElementById('modal_finals').value = row.finals !== null ? row.finals : '';
    document.getElementById('modal_notes').value = row.notes || '';

    // Trigger calculation
    const container = document.querySelector('.grade-calc-group');
    if (container) {
        const p = parseFloat(row.prelim);
        const m = parseFloat(row.midterm);
        const f = parseFloat(row.finals);
        const finalDisplay = container.querySelector('.calc-final-grade');
        const remarkDisplay = container.querySelector('.calc-remarks');

        if (!isNaN(p) && !isNaN(m) && !isNaN(f)) {
            const computed = ((p * 0.3) + (m * 0.3) + (f * 0.4)).toFixed(2);
            finalDisplay.value = computed;
            const passed = parseFloat(computed) >= 75.0;
            remarkDisplay.textContent = passed ? 'Passed' : 'Failed';
            remarkDisplay.className = 'badge badge-' + (passed ? 'success' : 'danger');
        } else {
            finalDisplay.value = '—';
            remarkDisplay.textContent = 'Pending';
            remarkDisplay.className = 'badge badge-secondary';
        }
    }

    openModal('encodeGradeModal');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
