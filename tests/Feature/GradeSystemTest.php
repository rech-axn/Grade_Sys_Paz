<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GradeSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('GradeSys');
    }

    public function test_teacher_can_login_and_redirect_to_dashboard(): void
    {
        $teacher = User::factory()->create([
            'username' => 'teacher',
            'role' => 'teacher',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'username' => 'teacher',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('teacher.dashboard'));
        $this->assertAuthenticatedAs($teacher);
    }

    public function test_student_can_login_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'student1',
            'role' => 'student',
            'password' => bcrypt('password123'),
        ]);

        Student::create([
            'user_id' => $user->id,
            'student_id_number' => '2026-00101',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Male',
        ]);

        $response = $this->post('/login', [
            'username' => 'student1',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_student_cannot_access_teacher_portal(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Student::create([
            'user_id' => $user->id,
            'student_id_number' => '2026-00999',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Male',
        ]);

        $response = $this->actingAs($user)->get(route('teacher.dashboard'));
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_teacher_cannot_access_student_portal(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get(route('student.dashboard'));
        $response->assertRedirect(route('teacher.dashboard'));
    }

    public function test_teacher_can_enroll_student(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->post(route('teacher.students.store'), [
            'name' => 'Maria Clara',
            'username' => 'mariaclara',
            'password' => 'password123',
            'student_id_number' => '2026-00555',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Female',
            'email' => 'maria.clara@example.com',
        ]);

        $response->assertRedirect(route('teacher.students.index'));
        $this->assertDatabaseHas('users', ['username' => 'mariaclara', 'role' => 'student']);
        $this->assertDatabaseHas('students', ['student_id_number' => '2026-00555']);
    }

    public function test_teacher_can_create_subject(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->post(route('teacher.subjects.store'), [
            'subject_code' => 'CS301',
            'subject_title' => 'Web Systems & Technologies',
            'units' => 3,
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
        ]);

        $response->assertRedirect(route('teacher.subjects.index'));
        $this->assertDatabaseHas('subjects', ['subject_code' => 'CS301']);
    }

    public function test_teacher_can_encode_and_update_grades(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_id_number' => '2026-00333',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Male',
        ]);

        $subject = Subject::create([
            'subject_code' => 'IT201',
            'subject_title' => 'Database Management Systems',
            'units' => 3,
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
        ]);

        // Prelim: 88, Midterm: 90, Finals: 92 => (88*0.3)+(90*0.3)+(92*0.4) = 26.4 + 27 + 36.8 = 90.20
        $response = $this->actingAs($teacher)->post(route('teacher.grades.store'), [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'prelim' => 88.0,
            'midterm' => 90.0,
            'finals' => 92.0,
            'notes' => 'Great performance',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('grades', [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'final_grade' => 90.20,
            'remarks' => 'Passed',
        ]);
    }

    public function test_student_can_change_password(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('oldpassword123'),
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_id_number' => '2026-00222',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Female',
        ]);

        $response = $this->actingAs($user)->put(route('student.profile.password'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));
    }

    public function test_grade_calculation_formula_is_exact(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_number' => '2026-00888',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Male',
        ]);

        $subject = Subject::create([
            'subject_code' => 'CS101',
            'subject_title' => 'Intro to Computer Science',
            'units' => 3,
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
        ]);

        // Prelim: 90 (27), Midterm: 90 (27), Finals: 95 (38) => Total: 92.00
        $grade = Grade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'prelim' => 90.0,
            'midterm' => 90.0,
            'finals' => 95.0,
        ]);

        $this->assertEquals(92.00, $grade->final_grade);
        $this->assertEquals('Passed', $grade->remarks);
    }

    public function test_failing_grade_sets_failed_remark(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $user->id,
            'student_id_number' => '2026-00777',
            'course_section' => 'BSIT 1-A',
            'year_level' => '1st Year',
            'gender' => 'Male',
        ]);

        $subject = Subject::create([
            'subject_code' => 'MATH101',
            'subject_title' => 'Calculus I',
            'units' => 3,
            'semester' => '1st Semester',
            'academic_year' => '2025-2026',
        ]);

        // Prelim: 60, Midterm: 65, Finals: 70 => 18 + 19.5 + 28 = 65.50
        $grade = Grade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'prelim' => 60.0,
            'midterm' => 65.0,
            'finals' => 70.0,
        ]);

        $this->assertEquals(65.50, $grade->final_grade);
        $this->assertEquals('Failed', $grade->remarks);
    }
}
