# GradeSys Paz - Grading System Portal

A web-based Grading System built for **Faculty Teachers** and **Students** with role-based access control, automated grade calculations, and printable grade certificates and report cards.

---

## 🌟 Key Features

- **🧑‍🏫 Faculty Portal:**
  - Dynamic Faculty Dashboard with student statistics, passing rate metrics, and top student rankings.
  - Complete Student Directory with enrollment & profile management.
  - Course Subject curriculum management.
  - Master Grade Matrix with real-time dynamic grade calculation: `(Prelim × 30%) + (Midterm × 30%) + (Finals × 40%) = Final Grade`.
  - Individual Official Certificate of Grades with printable format.

- **🎓 Student Portal:**
  - Secure student login showing strictly their own grades and enrolled courses.
  - Term GWA (General Weighted Average) and Academic Standing computation.
  - Official Term Grade Slip with `@media print` support for saving as PDF or physical printout.
  - Self-service profile viewing and password update.

- **🎨 Modern User Interface:**
  - Custom Vanilla CSS design system with smooth glassmorphism, responsive sidebar, stat cards, and status badges.
  - 1-Click Demo Login support for quick testing.

---

## 🚀 Installation & Setup (XAMPP)

1. Clone or place this repository into your XAMPP `htdocs` directory:
   ```text
   C:/xampp/htdocs/GradeSysPaz/
   ```

2. Start **Apache** and **MySQL** in the XAMPP Control Panel.

3. Open your browser and run the interactive setup wizard:
   ```text
   http://localhost/GradeSysPaz/setup.php
   ```
   Click **"Run Database Setup & Seed Data"** to automatically initialize the database schema and sample demo records.

4. Access the system at:
   ```text
   http://localhost/GradeSysPaz/
   ```

---

## 🔑 Default Demo Accounts

| Role | Username | Password | Access Details |
| :--- | :--- | :--- | :--- |
| **Faculty (Teacher)** | `teacher` | `password123` | Full faculty access (Manage students, subjects, grades, reports) |
| **Student 1** | `student1` | `password123` | Juan C. Dela Cruz (Student Portal) |
| **Student 2** | `student2` | `password123` | Alyssa Jane Reyes (Student Portal - Dean's List) |
| **Student 3** | `student3` | `password123` | Mark Anthony Santos (Student Portal) |
| **Student 4** | `student4` | `password123` | Sophia Marie Mendoza (Student Portal) |

---

## 📁 Project Architecture

```text
GradeSysPaz/
├── config/
│   └── database.php           # PDO database connection
├── includes/
│   ├── auth.php               # Session security, CSRF & RBAC guards
│   ├── header.php             # Reusable sidebar & navigation
│   └── footer.php             # Reusable layout footer & scripts
├── assets/
│   ├── css/
│   │   └── style.css          # Design system & print stylesheet
│   └── js/
│       └── script.js          # Dynamic live calculator & UI interactions
├── teacher/
│   ├── dashboard.php          # Faculty metrics & feed
│   ├── manage_students.php    # Student management (CRUD)
│   ├── manage_subjects.php    # Subject curriculum management (CRUD)
│   ├── manage_grades.php      # Master grade encoder & matrix
│   └── student_report.php     # Printable student certificate of grades
├── student/
│   ├── dashboard.php          # Student dashboard & GWA overview
│   ├── my_grades.php          # Official grade slip view
│   └── profile.php            # Student profile & password change
├── index.php                  # Portal login page
├── logout.php                 # Session destroy handler
├── schema.sql                 # MySQL schema definitions
└── setup.php                  # Interactive web database installer
```

---

## 📄 License
Open source and available for educational purposes.
