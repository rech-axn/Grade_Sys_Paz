# GradeSys - Faculty & Student Grading Portal

A modern, full-featured Grade Management System built with **Laravel 12 (PHP 8.2+)**, **Blade Templates**, **Alpine.js**, and **Tailwind CSS v3**. Designed for educational institutions to provide dedicated, role-separated portals for Faculty Instructors and Enrolled Students.

---

## 🚀 Tech Stack

- **Backend**: [Laravel 12](https://laravel.com/) & PHP 8.2+
- **Frontend**: Blade Components, [Alpine.js 3.x](https://alpinejs.dev/), and [Tailwind CSS v3](https://tailwindcss.com/)
- **Build Tool**: Vite & PostCSS
- **Database**: MySQL / PostgreSQL (Production & Local) | SQLite in-memory (Automated Tests)
- **Testing**: PHPUnit / Laravel Test Runner (100% automated test coverage)

---

## ✨ Features

### 🧑‍🏫 Faculty / Teacher Portal
- **Interactive Analytics Dashboard**: Total student enrollment, active course subjects, department average grade, and passing rate.
- **Students Directory & Management**:
  - Full CRUD operations with instantaneous Alpine.js search filtering.
  - Automatic account provisioning with secure bcrypt password hashing.
  - Printable official **Certificate of Student Grades** with university header, GPA/GWA calculation, and signature lines.
- **Course Subjects Catalog**:
  - Create and manage course descriptions, credit units, semesters, and academic years.
- **Master Grade Matrix**:
  - Real-time live grade calculation powered by Alpine.js.
  - Automated formula: `(Prelim × 30%) + (Midterm × 30%) + (Finals × 40%) = Final Grade`.
  - Automatic status computation (`>= 75.00` = `Passed`, `< 75.00` = `Failed`).
  - Subject and Student matrix filtering.
  - Instructor feedback & notes per student course entry.
  - Print-ready Grade Sheet layouts.

### 🎓 Student Portal
- **Secure Role-Isolated Access**: Students can only view their own respective grades and academic records.
- **Academic Dashboard**:
  - Term General Weighted Average (GWA) computation weighted by subject credit units.
  - Overall Academic Standing evaluation (Dean's List / Good Standing / Academic Probation).
  - Enrolled courses list and earned credits summary.
- **Official Term Grade Slip**:
  - Breakdown of Prelim, Midterm, Finals, Final Grades, and Instructor Remarks.
  - Grading scale legend (1.00 to 5.00 standard scale).
  - Clean `@media print` layout for saving as PDF or printing official slips.
- **Profile & Security Settings**:
  - View registered student details, ID number, and program section.
  - Self-service password change form with validation.

---

## 🔑 Demo Login Accounts

| Role | Email | Password | Notes |
| :--- | :--- | :--- | :--- |
| **Faculty / Teacher** | `teacher@gradesys.edu` | `password123` | Prof. Maria Santos (Full administrative & grading access) |
| **Student** | `juan.delacruz@gradesys.edu` | `password123` | Juan Dela Cruz (BSIT 1-A) |
| **Student** | `alyssa.reyes@gradesys.edu` | `password123` | Alyssa Reyes (BSIT 1-A, Dean's List Honor) |
| **Student** | `mark.santos@gradesys.edu` | `password123` | Mark Anthony Bautista (BSIT 1-A) |
| **Student** | `sophia.mendoza@gradesys.edu` | `password123` | Christine Joy Garcia (BSIT 1-A) |

> 💡 *The login page includes 1-click demo account autofill buttons for immediate testing.*

---

## 📐 Grading Formula

The system automatically computes the student's term final grade and passing status using the standard collegiate evaluation standard:

$$\text{Final Grade} = (\text{Prelim} \times 0.30) + (\text{Midterm} \times 0.30) + (\text{Finals} \times 0.40)$$

$$\text{GWA} = \frac{\sum (\text{Final Grade} \times \text{Units})}{\sum \text{Units}}$$

- **Passed**: $\text{Final Grade} \ge 75.00\%$
- **Failed**: $\text{Final Grade} < 75.00\%$

---

## 🛠️ Installation & Setup Guide

### 1. Requirements
- PHP 8.2 or higher (with `pdo_mysql`, `pdo_sqlite`, `zip`, `mbstring`, `openssl` extensions enabled)
- Composer 2.x
- Node.js 18+ and npm
- MySQL Server

### 2. Clone Repository & Setup
```bash
git clone https://github.com/rech-axn/Grade_Sys_Paz.git
cd Grade_Sys_Paz

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment config
cp .env.example .env

# Generate Application Key
php artisan key:generate
```

### 3. Database Configuration
Configure your database credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=grade_sys
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seed Demo Data
```bash
php artisan migrate:fresh --seed
```

### 5. Build Assets & Start Server
```bash
# Build Tailwind CSS & Alpine assets
npm run build

# Start Laravel development server
php artisan serve
```

Access the application in your browser at `http://localhost:8000`.

---

## 🧪 Automated Testing

Automated feature and unit tests execute against an in-memory SQLite database (`:memory:`) configured in `phpunit.xml`:

```bash
php artisan test
```

Test coverage includes:
- Authentication, role validation & unauthorized portal redirects.
- Student & Subject CRUD operations.
- Grade encoding, updating, and cascade calculations.
- Automatic formula precision checks ($30\% / 30\% / 40\%$).
- Student password modification.

---

## 📄 License
This project is open-sourced under the [MIT License](LICENSE).
