<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Teacher Account
        $teacher = User::create([
            'name' => 'Prof. Maria Paz Santos',
            'email' => 'teacher@gradesys.edu',
            'role' => 'teacher',
            'password' => $defaultPassword,
        ]);

        // 2. Student Accounts & Profiles
        $studentsData = [
            [
                'name' => 'Juan C. Dela Cruz',
                'email' => 'juan.delacruz@gradesys.edu',
                'student_id_number' => '2026-00101',
                'course_section' => 'BS Information Technology 1-A',
                'year_level' => '1st Year',
                'gender' => 'Male',
                'contact_number' => '+63 912 345 6789',
                'address' => 'Manila, Philippines',
            ],
            [
                'name' => 'Alyssa Jane Reyes',
                'email' => 'alyssa.reyes@gradesys.edu',
                'student_id_number' => '2026-00102',
                'course_section' => 'BS Information Technology 1-A',
                'year_level' => '1st Year',
                'gender' => 'Female',
                'contact_number' => '+63 918 765 4321',
                'address' => 'Quezon City, Philippines',
            ],
            [
                'name' => 'Mark Anthony Santos',
                'email' => 'mark.santos@gradesys.edu',
                'student_id_number' => '2026-00103',
                'course_section' => 'BS Information Technology 1-B',
                'year_level' => '1st Year',
                'gender' => 'Male',
                'contact_number' => '+63 920 112 3344',
                'address' => 'Makati City, Philippines',
            ],
            [
                'name' => 'Sophia Marie Mendoza',
                'email' => 'sophia.mendoza@gradesys.edu',
                'student_id_number' => '2026-00104',
                'course_section' => 'BS Information Technology 1-B',
                'year_level' => '1st Year',
                'gender' => 'Female',
                'contact_number' => '+63 922 998 8776',
                'address' => 'Pasig City, Philippines',
            ],
        ];

        $studentModels = [];
        foreach ($studentsData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'student',
                'password' => $defaultPassword,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'student_id_number' => $data['student_id_number'],
                'course_section' => $data['course_section'],
                'year_level' => $data['year_level'],
                'gender' => $data['gender'],
                'contact_number' => $data['contact_number'],
                'address' => $data['address'],
            ]);

            $studentModels[] = $student;
        }

        // 3. Subjects
        $subjectsData = [
            ['subject_code' => 'IT101', 'subject_title' => 'Introduction to Computing', 'units' => 3, 'semester' => '1st Semester', 'academic_year' => '2025-2026'],
            ['subject_code' => 'IT102', 'subject_title' => 'Computer Programming 1 (Python)', 'units' => 3, 'semester' => '1st Semester', 'academic_year' => '2025-2026'],
            ['subject_code' => 'IT103', 'subject_title' => 'Discrete Mathematics', 'units' => 3, 'semester' => '1st Semester', 'academic_year' => '2025-2026'],
            ['subject_code' => 'GE101', 'subject_title' => 'Understanding the Self', 'units' => 3, 'semester' => '1st Semester', 'academic_year' => '2025-2026'],
            ['subject_code' => 'PE101', 'subject_title' => 'Physical Fitness and Wellness', 'units' => 2, 'semester' => '1st Semester', 'academic_year' => '2025-2026'],
        ];

        $subjectModels = [];
        foreach ($subjectsData as $sData) {
            $subjectModels[] = Subject::create($sData);
        }

        // 4. Grades
        $gradesMatrix = [
            // Student 0 (Juan)
            [
                ['prelim' => 88.0, 'midterm' => 91.5, 'finals' => 93.0, 'notes' => 'Excellent performance and participation.'],
                ['prelim' => 92.0, 'midterm' => 90.0, 'finals' => 95.0, 'notes' => 'Consistently submits high quality code.'],
                ['prelim' => 84.0, 'midterm' => 86.0, 'finals' => 88.0, 'notes' => 'Good logical reasoning skills.'],
                ['prelim' => 89.0, 'midterm' => 91.0, 'finals' => 90.0, 'notes' => 'Active in group reflections.'],
                ['prelim' => 95.0, 'midterm' => 96.0, 'finals' => 98.0, 'notes' => 'Outstanding physical endurance.'],
            ],
            // Student 1 (Alyssa)
            [
                ['prelim' => 95.0, 'midterm' => 96.0, 'finals' => 97.0, 'notes' => 'Dean’s list candidate.'],
                ['prelim' => 94.0, 'midterm' => 95.5, 'finals' => 96.0, 'notes' => 'High aptitude in algorithmic design.'],
                ['prelim' => 91.0, 'midterm' => 93.0, 'finals' => 94.0, 'notes' => 'Strong mathematical background.'],
                ['prelim' => 92.0, 'midterm' => 94.0, 'finals' => 95.0, 'notes' => 'Great communication and teamwork.'],
                ['prelim' => 90.0, 'midterm' => 92.0, 'finals' => 93.0, 'notes' => 'Active participant.'],
            ],
            // Student 2 (Mark)
            [
                ['prelim' => 78.0, 'midterm' => 80.0, 'finals' => 82.0, 'notes' => 'Shows steady improvement.'],
                ['prelim' => 74.0, 'midterm' => 76.0, 'finals' => 78.0, 'notes' => 'Needs extra practice with nested loops.'],
                ['prelim' => 70.0, 'midterm' => 73.0, 'finals' => 72.0, 'notes' => 'Needs tutorial on graph theory.'],
                ['prelim' => 85.0, 'midterm' => 87.0, 'finals' => 86.0, 'notes' => 'Very cooperative in class activities.'],
                ['prelim' => 88.0, 'midterm' => 90.0, 'finals' => 89.0, 'notes' => 'Good sportsmanship.'],
            ],
            // Student 3 (Sophia)
            [
                ['prelim' => 90.0, 'midterm' => 92.0, 'finals' => 91.0, 'notes' => 'Very attentive in lecture.'],
                ['prelim' => 86.0, 'midterm' => 88.0, 'finals' => 89.0, 'notes' => 'Creative problem solver.'],
                ['prelim' => 82.0, 'midterm' => 85.0, 'finals' => 87.0, 'notes' => 'Solid understanding of proofs.'],
                ['prelim' => 94.0, 'midterm' => 95.0, 'finals' => 96.0, 'notes' => 'Expressive writing skills.'],
                ['prelim' => 91.0, 'midterm' => 93.0, 'finals' => 92.0, 'notes' => 'Punctual and energetic.'],
            ],
        ];

        foreach ($studentModels as $sIdx => $stu) {
            foreach ($subjectModels as $subIdx => $sub) {
                $g = $gradesMatrix[$sIdx][$subIdx];
                Grade::create([
                    'student_id' => $stu->id,
                    'subject_id' => $sub->id,
                    'prelim' => $g['prelim'],
                    'midterm' => $g['midterm'],
                    'finals' => $g['finals'],
                    'notes' => $g['notes'],
                ]);
            }
        }
    }
}
