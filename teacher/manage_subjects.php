<?php
// teacher/manage_subjects.php
$page_title = 'Course Subjects';
$active_nav = 'subjects';

require_once __DIR__ . '/../includes/header.php';
require_teacher();

$pdo = getDBConnection();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $code = strtoupper(trim($_POST['subject_code'] ?? ''));
        $title = trim($_POST['subject_title'] ?? '');
        $units = intval($_POST['units'] ?? 3);
        $semester = trim($_POST['semester'] ?? '1st Semester');
        $acadYear = trim($_POST['academic_year'] ?? '2025-2026');

        if (empty($code) || empty($title) || $units <= 0) {
            set_flash('danger', 'Please provide valid subject details.');
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO `subjects` (`subject_code`, `subject_title`, `units`, `semester`, `academic_year`) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$code, $title, $units, $semester, $acadYear]);
                set_flash('success', "Subject {$code} added successfully!");
            } catch (Exception $e) {
                set_flash('danger', 'Error adding subject: ' . $e->getMessage());
            }
        }
        header("Location: manage_subjects.php");
        exit();
    }

    if ($action === 'update') {
        $subjectId = intval($_POST['subject_id'] ?? 0);
        $code = strtoupper(trim($_POST['subject_code'] ?? ''));
        $title = trim($_POST['subject_title'] ?? '');
        $units = intval($_POST['units'] ?? 3);
        $semester = trim($_POST['semester'] ?? '1st Semester');
        $acadYear = trim($_POST['academic_year'] ?? '2025-2026');

        if (empty($code) || empty($title) || $subjectId <= 0) {
            set_flash('danger', 'Invalid subject data.');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE `subjects` SET `subject_code` = ?, `subject_title` = ?, `units` = ?, `semester` = ?, `academic_year` = ? WHERE `id` = ?");
                $stmt->execute([$code, $title, $units, $semester, $acadYear, $subjectId]);
                set_flash('success', "Subject {$code} updated successfully!");
            } catch (Exception $e) {
                set_flash('danger', 'Update failed: ' . $e->getMessage());
            }
        }
        header("Location: manage_subjects.php");
        exit();
    }

    if ($action === 'delete') {
        $subjectId = intval($_POST['subject_id'] ?? 0);
        if ($subjectId > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `subjects` WHERE `id` = ?");
                $stmt->execute([$subjectId]);
                set_flash('success', 'Subject deleted successfully.');
            } catch (Exception $e) {
                set_flash('danger', 'Error deleting subject: ' . $e->getMessage());
            }
        }
        header("Location: manage_subjects.php");
        exit();
    }
}

// Fetch subjects with stats
$stmt = $pdo->query("
    SELECT sub.*, 
           COUNT(g.id) AS graded_students_count,
           ROUND(AVG(g.final_grade), 2) AS subject_avg
    FROM `subjects` sub
    LEFT JOIN `grades` g ON g.subject_id = sub.id
    GROUP BY sub.id
    ORDER BY sub.subject_code ASC
");
$subjects = $stmt->fetchAll();
?>

<div class="card mb-6">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Academic Subjects</h2>
            <p class="text-xs text-muted">Manage courses, credit units, and curriculum schedules</p>
        </div>

        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
            <input type="text" class="form-control" style="width: 250px;" placeholder="Search subjects..." data-table-search="subjectsTable">
            <button type="button" class="btn btn-primary" onclick="openModal('addSubjectModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add New Subject
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table" id="subjectsTable">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Course Title</th>
                    <th>Units</th>
                    <th>Term / Semester</th>
                    <th>Academic Year</th>
                    <th>Grades Recorded</th>
                    <th>Subject Average</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subjects)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-6">No subjects created yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subjects as $sub): ?>
                        <tr>
                            <td>
                                <span class="badge badge-primary font-mono font-bold"><?= htmlspecialchars($sub['subject_code']) ?></span>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($sub['subject_title']) ?></div>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= $sub['units'] ?> Units</span>
                            </td>
                            <td><?= htmlspecialchars($sub['semester']) ?></td>
                            <td><?= htmlspecialchars($sub['academic_year']) ?></td>
                            <td>
                                <span class="font-semibold text-slate-700"><?= $sub['graded_students_count'] ?> students</span>
                            </td>
                            <td>
                                <?php if ($sub['subject_avg']): ?>
                                    <span class="badge badge-info font-bold"><?= $sub['subject_avg'] ?>%</span>
                                <?php else: ?>
                                    <span class="text-muted text-xs">No grades</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center gap-2" style="justify-content: flex-end;">
                                    <a href="manage_grades.php?subject_id=<?= $sub['id'] ?>" class="btn btn-sm btn-outline" title="Encode Grades">
                                        Grades
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick='editSubject(<?= json_encode($sub) ?>)' title="Edit Subject">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <form method="POST" action="manage_subjects.php" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($sub['subject_code'])) ?>?');" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Subject">
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

<!-- Add Subject Modal -->
<div class="modal-overlay" id="addSubjectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="font-bold text-lg text-slate-800">Add New Subject</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('addSubjectModal')">&times;</button>
        </div>
        <form method="POST" action="manage_subjects.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="create">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Subject Code *</label>
                        <input type="text" name="subject_code" class="form-control" placeholder="e.g. CS201" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Credit Units *</label>
                        <input type="number" name="units" class="form-control" value="3" min="1" max="10" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Subject Title / Course Description *</label>
                    <input type="text" name="subject_title" class="form-control" placeholder="e.g. Object Oriented Programming" required>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-control">
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="2025-2026" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addSubjectModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Subject</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal-overlay" id="editSubjectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="font-bold text-lg text-slate-800">Edit Subject</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('editSubjectModal')">&times;</button>
        </div>
        <form method="POST" action="manage_subjects.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="subject_id" id="edit_subject_id">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Subject Code *</label>
                        <input type="text" name="subject_code" id="edit_subject_code" class="form-control" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Credit Units *</label>
                        <input type="number" name="units" id="edit_units" class="form-control" min="1" max="10" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Subject Title *</label>
                    <input type="text" name="subject_title" id="edit_subject_title" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Semester</label>
                        <select name="semester" id="edit_semester" class="form-control">
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Academic Year</label>
                        <input type="text" name="academic_year" id="edit_academic_year" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editSubjectModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Subject</button>
            </div>
        </form>
    </div>
</div>

<script>
function editSubject(data) {
    document.getElementById('edit_subject_id').value = data.id;
    document.getElementById('edit_subject_code').value = data.subject_code;
    document.getElementById('edit_subject_title').value = data.subject_title;
    document.getElementById('edit_units').value = data.units;
    document.getElementById('edit_semester').value = data.semester;
    document.getElementById('edit_academic_year').value = data.academic_year;

    openModal('editSubjectModal');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
