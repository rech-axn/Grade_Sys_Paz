<?php
// teacher/manage_students.php
$page_title = 'Students Directory';
$active_nav = 'students';

require_once __DIR__ . '/../includes/header.php';
require_teacher();

$pdo = getDBConnection();

// Handle POST actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $studentIdNum = trim($_POST['student_id_number'] ?? '');
        $courseSection = trim($_POST['course_section'] ?? 'BSIT 1-A');
        $yearLevel = trim($_POST['year_level'] ?? '1st Year');
        $gender = trim($_POST['gender'] ?? 'Male');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($fullName) || empty($username) || empty($password) || empty($studentIdNum)) {
            set_flash('danger', 'Please fill in all required fields.');
        } else {
            try {
                // Check username or student ID duplicate
                $chk = $pdo->prepare("SELECT id FROM `users` WHERE `username` = ? UNION SELECT id FROM `students` WHERE `student_id_number` = ?");
                $chk->execute([$username, $studentIdNum]);
                if ($chk->rowCount() > 0) {
                    set_flash('danger', 'Username or Student ID Number is already registered.');
                } else {
                    $pdo->beginTransaction();
                    $pwdHash = password_hash($password, PASSWORD_BCRYPT);
                    $stmtUser = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `role`, `full_name`, `email`) VALUES (?, ?, 'student', ?, ?)");
                    $stmtUser->execute([$username, $pwdHash, $fullName, $email]);
                    $userId = $pdo->lastInsertId();

                    $stmtStu = $pdo->prepare("INSERT INTO `students` (`user_id`, `student_id_number`, `course_section`, `year_level`, `gender`, `contact_number`, `address`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmtStu->execute([$userId, $studentIdNum, $courseSection, $yearLevel, $gender, $contactNumber, $address]);
                    
                    $pdo->commit();
                    set_flash('success', "Student {$fullName} added successfully!");
                }
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                set_flash('danger', 'Error adding student: ' . $e->getMessage());
            }
        }
        header("Location: manage_students.php");
        exit();
    }

    if ($action === 'update') {
        $studentId = intval($_POST['student_id'] ?? 0);
        $userId = intval($_POST['user_id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $studentIdNum = trim($_POST['student_id_number'] ?? '');
        $courseSection = trim($_POST['course_section'] ?? '');
        $yearLevel = trim($_POST['year_level'] ?? '');
        $gender = trim($_POST['gender'] ?? 'Male');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $newPassword = trim($_POST['password'] ?? '');

        if (empty($fullName) || empty($studentIdNum) || $studentId <= 0) {
            set_flash('danger', 'Please provide valid student details.');
        } else {
            try {
                $pdo->beginTransaction();
                
                if (!empty($newPassword)) {
                    $pwdHash = password_hash($newPassword, PASSWORD_BCRYPT);
                    $stmtUser = $pdo->prepare("UPDATE `users` SET `full_name` = ?, `email` = ?, `password` = ? WHERE `id` = ?");
                    $stmtUser->execute([$fullName, $email, $pwdHash, $userId]);
                } else {
                    $stmtUser = $pdo->prepare("UPDATE `users` SET `full_name` = ?, `email` = ? WHERE `id` = ?");
                    $stmtUser->execute([$fullName, $email, $userId]);
                }

                $stmtStu = $pdo->prepare("UPDATE `students` SET `student_id_number` = ?, `course_section` = ?, `year_level` = ?, `gender` = ?, `contact_number` = ?, `address` = ? WHERE `id` = ?");
                $stmtStu->execute([$studentIdNum, $courseSection, $yearLevel, $gender, $contactNumber, $address, $studentId]);

                $pdo->commit();
                set_flash('success', "Student record updated successfully!");
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                set_flash('danger', 'Update failed: ' . $e->getMessage());
            }
        }
        header("Location: manage_students.php");
        exit();
    }

    if ($action === 'delete') {
        $userId = intval($_POST['user_id'] ?? 0);
        if ($userId > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM `users` WHERE `id` = ? AND `role` = 'student'");
                $stmt->execute([$userId]);
                set_flash('success', 'Student record removed successfully.');
            } catch (Exception $e) {
                set_flash('danger', 'Error removing student: ' . $e->getMessage());
            }
        }
        header("Location: manage_students.php");
        exit();
    }
}

// Fetch all students with grade counts
$stmt = $pdo->query("
    SELECT s.*, u.id AS user_id, u.username, u.full_name, u.email,
           COUNT(g.id) AS subjects_enrolled,
           ROUND(AVG(g.final_grade), 2) AS current_gwa
    FROM `students` s
    JOIN `users` u ON s.user_id = u.id
    LEFT JOIN `grades` g ON g.student_id = s.id
    GROUP BY s.id
    ORDER BY u.full_name ASC
");
$students = $stmt->fetchAll();
?>

<div class="card mb-6">
    <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Student Directory</h2>
            <p class="text-xs text-muted">Manage enrolled students, profiles, and account credentials</p>
        </div>

        <div class="flex items-center gap-3" style="flex-wrap: wrap;">
            <input type="text" class="form-control" style="width: 250px;" placeholder="Search students..." data-table-search="studentsTable">
            <button type="button" class="btn btn-primary" onclick="openModal('addStudentModal')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add New Student
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table" id="studentsTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Course & Section</th>
                    <th>Year Level</th>
                    <th>GWA</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-6">No students found. Click "Add New Student" to enroll one.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $stu): ?>
                        <tr>
                            <td>
                                <span class="badge badge-secondary font-mono font-bold"><?= htmlspecialchars($stu['student_id_number']) ?></span>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900"><?= htmlspecialchars($stu['full_name']) ?></div>
                                <div class="text-xs text-muted"><?= htmlspecialchars($stu['email'] ?: 'No email') ?></div>
                            </td>
                            <td>
                                <code class="text-xs bg-slate-100 px-2 py-1 rounded text-indigo-600 font-semibold"><?= htmlspecialchars($stu['username']) ?></code>
                            </td>
                            <td><?= htmlspecialchars($stu['course_section']) ?></td>
                            <td><?= htmlspecialchars($stu['year_level']) ?></td>
                            <td>
                                <?php if ($stu['current_gwa']): ?>
                                    <span class="badge badge-primary font-bold"><?= $stu['current_gwa'] ?>%</span>
                                <?php else: ?>
                                    <span class="text-muted text-xs">No grades</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-center gap-2" style="justify-content: flex-end;">
                                    <a href="student_report.php?student_id=<?= $stu['id'] ?>" class="btn btn-sm btn-outline" title="View Grade Report">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                        Report
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick='editStudent(<?= json_encode($stu) ?>)' title="Edit Profile">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit
                                    </button>
                                    <form method="POST" action="manage_students.php" onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($stu['full_name'])) ?>? All grades will be deleted.');" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $stu['user_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Student">
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

<!-- Add Student Modal -->
<div class="modal-overlay" id="addStudentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="font-bold text-lg text-slate-800">Enroll New Student</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('addStudentModal')">&times;</button>
        </div>
        <form method="POST" action="manage_students.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="create">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. Maria Clara Garcia" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Student ID Number *</label>
                        <input type="text" name="student_id_number" class="form-control" placeholder="e.g. 2026-00105" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Login Username *</label>
                        <input type="text" name="username" class="form-control" placeholder="e.g. student5" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Login Password *</label>
                        <input type="password" name="password" class="form-control" value="password123" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Course & Section</label>
                        <input type="text" name="course_section" class="form-control" value="BSIT 1-A" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Year Level</label>
                        <select name="year_level" class="form-control">
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="student@gradesys.edu">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" placeholder="+63 912 345 6789">
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" placeholder="City, Country">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addStudentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal-overlay" id="editStudentModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="font-bold text-lg text-slate-800">Edit Student Record</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('editStudentModal')">&times;</button>
        </div>
        <form method="POST" action="manage_students.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="student_id" id="edit_student_id">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Student ID Number *</label>
                        <input type="text" name="student_id_number" id="edit_student_id_number" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Course & Section</label>
                        <input type="text" name="course_section" id="edit_course_section" class="form-control" required>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Year Level</label>
                        <select name="year_level" id="edit_year_level" class="form-control">
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="edit_gender" class="form-control">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-col form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" id="edit_contact_number" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" id="edit_address" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Change Password (Leave blank to keep unchanged)</label>
                    <input type="password" name="password" class="form-control" placeholder="New Password">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editStudentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>
    </div>
</div>

<script>
function editStudent(data) {
    document.getElementById('edit_student_id').value = data.id;
    document.getElementById('edit_user_id').value = data.user_id;
    document.getElementById('edit_full_name').value = data.full_name;
    document.getElementById('edit_student_id_number').value = data.student_id_number;
    document.getElementById('edit_course_section').value = data.course_section;
    document.getElementById('edit_year_level').value = data.year_level;
    document.getElementById('edit_gender').value = data.gender;
    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_contact_number').value = data.contact_number || '';
    document.getElementById('edit_address').value = data.address || '';

    openModal('editStudentModal');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
