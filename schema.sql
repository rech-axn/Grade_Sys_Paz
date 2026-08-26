-- GradeSysPaz Database Schema
CREATE DATABASE IF NOT EXISTS `grade_sys_paz` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `grade_sys_paz`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('teacher', 'student') NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Students details table
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `student_id_number` VARCHAR(50) NOT NULL UNIQUE,
    `course_section` VARCHAR(50) NOT NULL DEFAULT 'BSIT 1-A',
    `year_level` VARCHAR(20) NOT NULL DEFAULT '1st Year',
    `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
    `contact_number` VARCHAR(30) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subjects table
CREATE TABLE IF NOT EXISTS `subjects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `subject_code` VARCHAR(20) NOT NULL UNIQUE,
    `subject_title` VARCHAR(100) NOT NULL,
    `units` INT NOT NULL DEFAULT 3,
    `semester` VARCHAR(30) NOT NULL DEFAULT '1st Semester',
    `academic_year` VARCHAR(30) NOT NULL DEFAULT '2025-2026',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Grades table
CREATE TABLE IF NOT EXISTS `grades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `subject_id` INT NOT NULL,
    `prelim` DECIMAL(5,2) DEFAULT NULL,
    `midterm` DECIMAL(5,2) DEFAULT NULL,
    `finals` DECIMAL(5,2) DEFAULT NULL,
    `final_grade` DECIMAL(5,2) DEFAULT NULL,
    `remarks` ENUM('Passed', 'Failed', 'Incomplete', 'Pending') DEFAULT 'Pending',
    `notes` VARCHAR(255) DEFAULT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_student_subject` (`student_id`, `subject_id`),
    FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
