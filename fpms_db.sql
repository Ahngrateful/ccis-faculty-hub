-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 11:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fpms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `faculty_id`, `action`, `timestamp`, `admin_id`, `details`, `created_at`, `updated_at`) VALUES
(1, 2, 'Login', '2025-04-20 08:28:08', NULL, 'User logged in from IP: 92.168.250.110', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(2, 2, 'Document Submission', '2025-04-29 08:28:08', NULL, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(3, 2, 'Compliance Status Changed', '2025-05-02 08:28:08', 12, 'Compliance status changed from submitted to approved', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(4, 3, 'Login', '2025-04-24 08:28:08', NULL, 'User logged in from IP: 242.152.225.241', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(5, 3, 'Document Submission', '2025-05-01 08:28:08', NULL, 'Submitted document: Research Publication', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(6, 3, 'Compliance Status Changed', '2025-05-05 08:28:08', 1, 'Compliance status changed from missing to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(7, 4, 'Login', '2025-04-06 08:28:08', NULL, 'User logged in from IP: 21.40.182.28', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(8, 4, 'Document Submission', '2025-04-29 08:28:08', NULL, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(9, 4, 'Compliance Status Changed', '2025-05-02 08:28:08', 11, 'Compliance status changed from missing to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(10, 5, 'Login', '2025-04-28 08:28:08', NULL, 'User logged in from IP: 106.43.147.127', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(11, 5, 'Document Submission', '2025-04-24 08:28:08', NULL, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(12, 5, 'Compliance Status Changed', '2025-04-30 08:28:08', 1, 'Compliance status changed from returned to submitted', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(13, 6, 'Login', '2025-04-29 08:28:08', NULL, 'User logged in from IP: 158.145.75.208', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(14, 6, 'Document Submission', '2025-05-05 08:28:08', NULL, 'Submitted document: Research Publication', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(15, 6, 'Compliance Status Changed', '2025-04-29 08:28:08', 11, 'Compliance status changed from missing to submitted', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(16, 7, 'Login', '2025-04-12 08:28:08', NULL, 'User logged in from IP: 7.236.236.102', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(17, 7, 'Document Submission', '2025-04-22 08:28:08', NULL, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(18, 7, 'Compliance Status Changed', '2025-05-03 08:28:08', 11, 'Compliance status changed from missing to approved', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(19, 8, 'Login', '2025-04-12 08:28:08', NULL, 'User logged in from IP: 229.165.79.205', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(20, 8, 'Document Submission', '2025-04-29 08:28:08', NULL, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(21, 8, 'Compliance Status Changed', '2025-04-30 08:28:08', 11, 'Compliance status changed from returned to approved', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(22, 9, 'Login', '2025-04-11 08:28:08', NULL, 'User logged in from IP: 60.96.180.247', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(23, 9, 'Document Submission', '2025-04-24 08:28:08', NULL, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(24, 9, 'Compliance Status Changed', '2025-05-05 08:28:08', 11, 'Compliance status changed from missing to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(25, 10, 'Login', '2025-04-15 08:28:08', NULL, 'User logged in from IP: 129.180.194.236', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(26, 10, 'Document Submission', '2025-05-05 08:28:08', NULL, 'Submitted document: Teaching Portfolio', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(27, 10, 'Compliance Status Changed', '2025-05-01 08:28:08', 12, 'Compliance status changed from approved to missing', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(28, 1, 'Login', '2025-04-22 08:28:08', NULL, 'User logged in from IP: 69.0.116.50', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(29, 1, 'Document Submission', '2025-04-23 08:28:08', NULL, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(30, 1, 'Compliance Status Changed', '2025-05-05 08:28:08', 11, 'Compliance status changed from approved to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(31, 11, 'Login', '2025-05-05 08:28:08', NULL, 'User logged in from IP: 219.12.53.6', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(32, 11, 'Document Submission', '2025-04-24 08:28:08', NULL, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(33, 11, 'Compliance Status Changed', '2025-05-01 08:28:08', 1, 'Compliance status changed from submitted to approved', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(34, 12, 'Login', '2025-04-10 08:28:08', NULL, 'User logged in from IP: 207.217.170.133', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(35, 12, 'Document Submission', '2025-04-25 08:28:08', NULL, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(36, 12, 'Compliance Status Changed', '2025-05-01 08:28:08', 1, 'Compliance status changed from approved to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(37, 12, 'Compliance Status Changed', '2025-05-04 08:28:08', 1, 'Compliance status changed from approved to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(38, 6, 'Profile Update', '2025-04-21 08:28:08', 1, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(39, 1, 'Login', '2025-04-13 08:28:08', 11, 'User logged in from IP: 107.77.234.126', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(40, 8, 'Password Reset', '2025-03-12 08:28:08', 1, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(41, 11, 'Compliance Status Changed', '2025-04-27 08:28:08', 1, 'Compliance status changed from returned to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(42, 10, 'Document Submission', '2025-02-28 08:28:08', 11, 'Submitted document: Teaching Portfolio', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(43, 11, 'Login', '2025-02-13 08:28:08', 12, 'User logged in from IP: 246.69.54.204', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(44, 1, 'Profile Update', '2025-05-05 08:28:08', 1, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(45, 4, 'Document Submission', '2025-02-20 08:28:08', 1, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(46, 12, 'Password Reset', '2025-02-08 08:28:08', 12, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(47, 7, 'Profile Update', '2025-02-19 08:28:08', 11, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(48, 1, 'Document Submission', '2025-03-31 08:28:08', 1, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(49, 4, 'Login', '2025-03-01 08:28:08', 1, 'User logged in from IP: 226.114.121.98', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(50, 2, 'Compliance Status Changed', '2025-03-29 08:28:08', 12, 'Compliance status changed from approved to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(51, 2, 'Profile Update', '2025-02-08 08:28:08', 1, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(52, 9, 'Compliance Status Changed', '2025-02-26 08:28:08', 11, 'Compliance status changed from submitted to submitted', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(53, 4, 'Login', '2025-04-25 08:28:08', 11, 'User logged in from IP: 2.169.154.191', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(54, 1, 'Profile Update', '2025-02-20 08:28:08', 11, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(55, 3, 'Password Reset', '2025-05-05 08:28:08', 1, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(56, 12, 'Document Submission', '2025-03-04 08:28:08', 11, 'Submitted document: Teaching Portfolio', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(57, 2, 'Password Reset', '2025-02-23 08:28:08', 11, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(58, 12, 'Profile Update', '2025-03-19 08:28:08', 1, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(59, 2, 'Document Submission', '2025-03-01 08:28:08', 11, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(60, 9, 'Profile Update', '2025-04-27 08:28:08', 12, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(61, 10, 'Login', '2025-03-24 08:28:08', 12, 'User logged in from IP: 245.150.105.36', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(62, 3, 'Document Submission', '2025-05-02 08:28:08', 12, 'Submitted document: Community Service Documentation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(63, 5, 'Document Submission', '2025-02-20 08:28:08', 11, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(64, 7, 'Document Submission', '2025-04-11 08:28:08', 11, 'Submitted document: Performance Evaluation', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(65, 10, 'Compliance Status Changed', '2025-03-13 08:28:08', 11, 'Compliance status changed from submitted to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(66, 12, 'Password Reset', '2025-05-02 08:28:08', 12, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(67, 7, 'Login', '2025-03-30 08:28:08', 11, 'User logged in from IP: 255.24.189.11', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(68, 2, 'Compliance Status Changed', '2025-02-07 08:28:08', 12, 'Compliance status changed from returned to approved', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(69, 5, 'Profile Update', '2025-04-10 08:28:08', 12, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(70, 2, 'Login', '2025-02-09 08:28:08', 1, 'User logged in from IP: 244.228.222.142', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(71, 5, 'Login', '2025-04-25 08:28:08', 12, 'User logged in from IP: 251.15.114.140', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(72, 11, 'Profile Update', '2025-03-29 08:28:08', 1, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(73, 12, 'Login', '2025-03-17 08:28:08', 1, 'User logged in from IP: 27.60.69.243', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(74, 2, 'Compliance Status Changed', '2025-03-05 08:28:08', 11, 'Compliance status changed from missing to returned', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(75, 7, 'Password Reset', '2025-03-26 08:28:08', 12, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(76, 1, 'Password Reset', '2025-02-23 08:28:08', 1, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(77, 11, 'Password Reset', '2025-02-20 08:28:08', 1, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(78, 3, 'Compliance Status Changed', '2025-03-26 08:28:08', 1, 'Compliance status changed from missing to missing', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(79, 9, 'Login', '2025-04-11 08:28:08', 1, 'User logged in from IP: 39.35.114.38', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(80, 12, 'Profile Update', '2025-03-28 08:28:08', 1, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(81, 2, 'Profile Update', '2025-04-27 08:28:08', 12, 'User updated profile information', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(82, 2, 'Compliance Status Changed', '2025-03-16 08:28:08', 12, 'Compliance status changed from missing to submitted', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(83, 11, 'Document Submission', '2025-03-08 08:28:08', 12, 'Submitted document: Teaching Portfolio', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(84, 5, 'Document Submission', '2025-04-16 08:28:08', 12, 'Submitted document: Teaching Portfolio', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(85, 7, 'Document Submission', '2025-03-10 08:28:08', 12, 'Submitted document: Teaching Portfolio', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(86, 1, 'Password Reset', '2025-04-18 08:28:08', 1, 'Password reset requested', '2025-05-06 08:28:08', '2025-05-06 08:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ched_compliance_requirements`
--

CREATE TABLE `ched_compliance_requirements` (
  `requirement_id` bigint(20) UNSIGNED NOT NULL,
  `requirement_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ched_compliance_requirements`
--

INSERT INTO `ched_compliance_requirements` (`requirement_id`, `requirement_name`, `description`, `is_mandatory`, `created_at`, `updated_at`) VALUES
(1, 'Teaching Portfolio', 'A comprehensive collection of teaching materials, strategies, and assessments used by faculty', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(2, 'Professional Development Plan', 'Documentation of ongoing professional growth and learning activities', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(3, 'Annual Performance Evaluation', 'Yearly assessment of teaching effectiveness and contributions', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(4, 'Research Publications', 'Published academic papers in relevant journals', 0, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(5, 'Course Syllabi', 'Detailed plans for each course taught', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(6, 'Research Publications', 'Published academic papers in relevant journals', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(7, 'Academic Credentials', 'Official copies of degrees and certifications', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(8, 'Course Syllabi', 'Detailed plans for each course taught', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(9, 'Teaching Portfolio', 'A comprehensive collection of teaching materials, strategies, and assessments used by faculty', 0, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(10, 'Conference Presentations', 'Records of academic presentations at conferences', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `credential_id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `credential_type` varchar(50) NOT NULL,
  `credential_details` text DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('valid','expiring','expired') NOT NULL DEFAULT 'valid',
  `approval_status` enum('pending','approved','returned') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `approval_status` enum('pending','approved','returned') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `account_creation_date` date NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profile_image` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `first_name`, `last_name`, `email`, `password_hash`, `account_creation_date`, `role_id`, `status`, `created_at`, `updated_at`, `profile_image`) VALUES
(1, 'Admin', 'User', 'newuser@example.com', '$2y$10$yb4gxjVSB9mWAwezERoP/eJ7PQBvUflseG0nUFrPiVbPEJJZd6q/u', '2025-05-06', 2, 'active', '2025-05-06 08:28:05', '2025-05-06 08:28:05', NULL),
(2, 'Test', 'Faculty', 'faculty@umak.edu.ph', '$2y$12$/8DnsbOMND971KRVA24Jw.zZybcoHgV8gZztThARdE3RsswEth3Ri', '2025-05-06', 1, 'active', '2025-05-06 08:28:06', '2025-05-06 08:28:06', NULL),
(3, 'Luna', 'Mayer', 'vhoeger@example.net', '$2y$12$RcMKzWsRnSyDfpQ9DBlxWe2YRazCsNLm9OUNwL5Q8fX3y9Vw53doW', '2023-07-10', 1, 'active', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(4, 'Wendell', 'Feil', 'gutmann.jerod@example.net', '$2y$12$16CqCT1bc4OcsexFlBVBMOiEFMEw7WEKV/43W/QQerdbRDwIr8bA6', '2024-05-13', 1, 'active', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(5, 'Raymond', 'Ritchie', 'madelyn.upton@example.net', '$2y$12$yPBADpDp/IoHbWSQIsUlPOu66j6Ehvw6/gudU.EEvmdb3/HP4t1aa', '2024-12-31', 1, 'inactive', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(6, 'Noble', 'Bogisich', 'reichel.joel@example.net', '$2y$12$AdddVxtOT6twcEeFTwos9.zBbr0wqBaORGpjei1wFCPwuPfVPHe1i', '2025-04-16', 1, 'inactive', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(7, 'Raven', 'Schmidt', 'sanford.rau@example.com', '$2y$12$rOQLzKx8o2Sq9emQslFt4.YB/.Ot6xP71KiPjIN/Kc4ObHyOWFrkC', '2024-05-21', 1, 'inactive', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(8, 'Florine', 'Zboncak', 'hodkiewicz.werner@example.com', '$2y$12$dtSjVJlZKtAMrWnFA53/HerS7Hn.n7pMANnan69j3xjlnuzOfR7ly', '2023-07-31', 1, 'active', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(9, 'Ottilie', 'Haley', 'lsporer@example.org', '$2y$12$rMU3leHrZZdtatz7s0dzYOh4cv8JvXQ3dknScYyq8jFgJro7jleNu', '2023-12-05', 1, 'active', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(10, 'Eino', 'Upton', 'amaya.kilback@example.org', '$2y$12$LmzPyZYZNExCuWAlO8UsjOpOj02SAzUlK7v/7Exc2L9Kgl4xNRPk2', '2023-06-15', 1, 'active', '2025-05-06 08:28:07', '2025-05-06 08:28:07', NULL),
(11, 'Sienna', 'Mertz', 'flangosh@example.net', '$2y$12$V/Essbw5InCmVx2hyyDSa.7t4NIGlf.Kkm.GSjqe//pJHkrP30YhO', '2025-03-21', 2, 'active', '2025-05-06 08:28:08', '2025-05-06 08:28:08', NULL),
(12, 'Jailyn', 'Cassin', 'sadye64@example.net', '$2y$12$t18YwwJFmHbTpIbfCkoPhOgaabkbfUnXYzgQ5o4cAFCa3d4nLw4u.', '2025-05-06', 2, 'active', '2025-05-06 08:28:08', '2025-05-06 08:28:08', NULL),
(13, 'sharleen', 'olaguir', 'solaguir.k12045270@umak.edu.ph', '', '2025-05-07', 1, 'active', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_compliance_status`
--

CREATE TABLE `faculty_compliance_status` (
  `compliance_id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `requirement_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('missing','submitted','approved','returned') NOT NULL DEFAULT 'missing',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty_compliance_status`
--

INSERT INTO `faculty_compliance_status` (`compliance_id`, `faculty_id`, `requirement_id`, `status`, `last_updated`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'approved', '2024-11-12 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(2, 2, 2, 'missing', '2025-04-22 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(3, 2, 3, 'returned', '2024-12-11 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(4, 2, 4, 'approved', '2025-04-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(5, 2, 5, 'returned', '2025-01-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(6, 2, 6, 'submitted', '2025-01-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(7, 2, 7, 'approved', '2025-03-02 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(8, 2, 8, 'returned', '2024-12-03 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(9, 2, 9, 'approved', '2025-04-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(10, 2, 10, 'approved', '2024-12-07 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(11, 3, 1, 'returned', '2025-04-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(12, 3, 2, 'missing', '2025-01-09 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(13, 3, 3, 'missing', '2024-11-21 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(14, 3, 4, 'approved', '2024-12-19 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(15, 3, 5, 'missing', '2024-12-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(16, 3, 6, 'returned', '2025-05-02 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(17, 3, 7, 'returned', '2025-04-18 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(18, 3, 8, 'returned', '2025-01-23 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(19, 3, 9, 'returned', '2025-02-22 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(20, 3, 10, 'submitted', '2025-02-08 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(21, 4, 1, 'missing', '2025-03-21 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(22, 4, 2, 'submitted', '2025-03-28 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(23, 4, 3, 'missing', '2024-11-17 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(24, 4, 4, 'approved', '2024-11-17 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(25, 4, 5, 'missing', '2025-02-25 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(26, 4, 6, 'submitted', '2025-04-05 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(27, 4, 7, 'approved', '2024-11-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(28, 4, 8, 'submitted', '2024-12-23 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(29, 4, 9, 'missing', '2025-02-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(30, 4, 10, 'returned', '2025-02-04 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(31, 5, 1, 'returned', '2025-03-31 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(32, 5, 2, 'approved', '2024-12-01 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(33, 5, 3, 'missing', '2025-02-18 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(34, 5, 4, 'missing', '2024-11-14 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(35, 5, 5, 'submitted', '2025-03-17 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(36, 5, 6, 'submitted', '2025-01-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(37, 5, 7, 'returned', '2024-11-12 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(38, 5, 8, 'submitted', '2025-04-23 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(39, 5, 9, 'returned', '2025-01-01 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(40, 5, 10, 'returned', '2024-11-22 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(41, 6, 1, 'submitted', '2025-01-17 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(42, 6, 2, 'approved', '2025-04-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(43, 6, 3, 'returned', '2025-03-19 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(44, 6, 4, 'submitted', '2024-12-05 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(45, 6, 5, 'missing', '2025-04-11 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(46, 6, 6, 'approved', '2024-12-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(47, 6, 7, 'missing', '2024-11-21 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(48, 6, 8, 'returned', '2025-01-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(49, 6, 9, 'missing', '2025-01-08 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(50, 6, 10, 'approved', '2025-03-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(51, 7, 1, 'missing', '2024-11-28 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(52, 7, 2, 'approved', '2025-04-11 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(53, 7, 3, 'missing', '2025-01-09 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(54, 7, 4, 'approved', '2025-02-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(55, 7, 5, 'submitted', '2024-12-19 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(56, 7, 6, 'approved', '2025-03-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(57, 7, 7, 'approved', '2025-02-07 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(58, 7, 8, 'approved', '2024-12-23 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(59, 7, 9, 'missing', '2025-01-28 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(60, 7, 10, 'submitted', '2025-05-04 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(61, 8, 1, 'approved', '2024-12-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(62, 8, 2, 'submitted', '2025-04-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(63, 8, 3, 'returned', '2025-03-06 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(64, 8, 4, 'missing', '2025-02-08 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(65, 8, 5, 'submitted', '2025-02-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(66, 8, 6, 'submitted', '2024-12-25 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(67, 8, 7, 'returned', '2025-01-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(68, 8, 8, 'returned', '2024-12-31 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(69, 8, 9, 'approved', '2024-12-28 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(70, 8, 10, 'approved', '2025-02-09 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(71, 9, 1, 'returned', '2025-01-01 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(72, 9, 2, 'returned', '2025-01-12 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(73, 9, 3, 'approved', '2024-11-27 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(74, 9, 4, 'approved', '2025-05-03 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(75, 9, 5, 'approved', '2025-02-12 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(76, 9, 6, 'missing', '2025-02-04 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(77, 9, 7, 'approved', '2025-04-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(78, 9, 8, 'submitted', '2025-03-13 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(79, 9, 9, 'approved', '2025-02-08 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(80, 9, 10, 'missing', '2025-03-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(81, 10, 1, 'approved', '2024-12-14 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(82, 10, 2, 'missing', '2025-04-03 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(83, 10, 3, 'missing', '2024-11-11 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(84, 10, 4, 'missing', '2025-04-25 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(85, 10, 5, 'missing', '2024-11-21 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(86, 10, 6, 'missing', '2025-01-09 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(87, 10, 7, 'returned', '2025-01-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(88, 10, 8, 'missing', '2025-02-09 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(89, 10, 9, 'approved', '2025-04-11 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(90, 10, 10, 'submitted', '2025-04-29 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(91, 1, 1, 'returned', '2025-01-13 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(92, 1, 2, 'missing', '2025-02-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(93, 1, 3, 'returned', '2024-11-27 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(94, 1, 4, 'returned', '2025-04-01 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(95, 1, 5, 'submitted', '2025-01-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(96, 1, 6, 'returned', '2025-01-14 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(97, 1, 7, 'submitted', '2024-12-28 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(98, 1, 8, 'missing', '2025-01-26 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(99, 1, 9, 'missing', '2025-01-01 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(100, 1, 10, 'missing', '2025-04-19 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(101, 11, 1, 'submitted', '2025-03-02 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(102, 11, 2, 'approved', '2025-03-29 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(103, 11, 3, 'missing', '2024-12-29 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(104, 11, 4, 'returned', '2025-05-04 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(105, 11, 5, 'submitted', '2025-03-15 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(106, 11, 6, 'approved', '2024-11-16 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(107, 11, 7, 'missing', '2025-01-01 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(108, 11, 8, 'approved', '2024-12-21 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(109, 11, 9, 'submitted', '2025-02-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(110, 11, 10, 'missing', '2025-04-03 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(111, 12, 1, 'returned', '2025-04-10 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(112, 12, 2, 'approved', '2025-03-19 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(113, 12, 3, 'submitted', '2024-11-30 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(114, 12, 4, 'submitted', '2025-03-25 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(115, 12, 5, 'approved', '2025-03-20 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(116, 12, 6, 'submitted', '2024-12-18 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(117, 12, 7, 'returned', '2024-12-14 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(118, 12, 8, 'submitted', '2025-02-24 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(119, 12, 9, 'submitted', '2025-03-08 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(120, 12, 10, 'returned', '2025-05-02 08:28:08', '2025-05-06 08:28:08', '2025-05-06 08:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_profiles`
--

CREATE TABLE `faculty_profiles` (
  `profile_id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `personal_info` text DEFAULT NULL,
  `educational_background` text DEFAULT NULL,
  `work_experience` text DEFAULT NULL,
  `teaching_assignments` text DEFAULT NULL,
  `research_output` text DEFAULT NULL,
  `seminars_trainings` text DEFAULT NULL,
  `awards` text DEFAULT NULL,
  `professional_licenses` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approval_status` enum('pending','approved','returned') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(37, '0001_01_01_000000_create_users_table', 1),
(38, '0001_01_01_000001_create_cache_table', 1),
(39, '0001_01_01_000002_create_jobs_table', 1),
(40, '2025_05_06_102555_create_roles_table', 1),
(41, '2025_05_06_102934_create_faculty_table', 1),
(42, '2025_05_06_105903_create_faculty_profiles_table', 1),
(43, '2025_05_06_111244_create_credentials_table', 1),
(44, '2025_05_06_111417_create_documents_table', 1),
(45, '2025_05_06_111541_create_audit_logs_table', 1),
(46, '2025_05_06_111844_create_reminders_table', 1),
(47, '2025_05_06_112051_create_reports_table', 1),
(48, '2025_05_06_112252_create_ched_compliance_requirements_table', 1),
(49, '2025_05_06_113334_create_faculty_compliance_status_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `reminder_id` bigint(20) UNSIGNED NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  `reminder_type` varchar(50) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('sent','pending','read') NOT NULL DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`reminder_id`, `faculty_id`, `reminder_type`, `due_date`, `status`, `message`, `created_at`, `updated_at`) VALUES
(1, 2, 'Document Submission', '2025-05-09', 'pending', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(2, 2, 'Deadline Approaching', '2025-05-25', 'pending', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(3, 2, 'Portfolio Review', '2025-06-05', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(4, 3, 'Document Submission', '2025-05-14', 'sent', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(5, 3, 'Portfolio Review', '2025-07-03', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(6, 3, 'CHED Submission', '2025-07-01', 'pending', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(7, 4, 'Document Submission', '2025-05-30', 'read', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(8, 4, 'Deadline Approaching', '2025-06-28', 'pending', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(9, 4, 'CHED Submission', '2025-06-29', 'sent', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(10, 5, 'Document Submission', '2025-07-28', 'sent', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(11, 5, 'Compliance Update', '2025-07-03', 'sent', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(12, 5, 'Deadline Approaching', '2025-05-31', 'read', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(13, 5, 'CHED Submission', '2025-08-01', 'pending', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(14, 6, 'Compliance Update', '2025-05-13', 'read', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(15, 6, 'Deadline Approaching', '2025-06-23', 'read', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(16, 6, 'Portfolio Review', '2025-06-19', 'sent', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(17, 6, 'CHED Submission', '2025-06-09', 'sent', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(18, 7, 'Compliance Update', '2025-07-04', 'pending', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(19, 7, 'Deadline Approaching', '2025-05-18', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(20, 7, 'Portfolio Review', '2025-06-08', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(21, 8, 'Document Submission', '2025-06-11', 'pending', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(22, 8, 'Deadline Approaching', '2025-08-04', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(23, 8, 'Portfolio Review', '2025-05-24', 'sent', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(24, 8, 'CHED Submission', '2025-08-02', 'sent', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(25, 9, 'Compliance Update', '2025-07-13', 'pending', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(26, 9, 'Deadline Approaching', '2025-07-31', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(27, 9, 'Portfolio Review', '2025-07-07', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(28, 10, 'Document Submission', '2025-06-18', 'pending', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(29, 10, 'Deadline Approaching', '2025-06-26', 'pending', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(30, 10, 'Portfolio Review', '2025-07-03', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(31, 10, 'CHED Submission', '2025-06-09', 'pending', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(32, 1, 'Document Submission', '2025-05-20', 'sent', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(33, 1, 'Deadline Approaching', '2025-05-08', 'read', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(34, 1, 'Portfolio Review', '2025-07-14', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(35, 1, 'CHED Submission', '2025-06-24', 'sent', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(36, 11, 'Document Submission', '2025-05-29', 'pending', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(37, 11, 'Deadline Approaching', '2025-07-22', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(38, 11, 'Portfolio Review', '2025-06-13', 'sent', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(39, 11, 'CHED Submission', '2025-07-22', 'pending', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(40, 12, 'Document Submission', '2025-06-21', 'pending', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(41, 12, 'Compliance Update', '2025-07-08', 'sent', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(42, 12, 'Deadline Approaching', '2025-05-24', 'read', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(43, 12, 'Portfolio Review', '2025-06-23', 'pending', 'Time to review and update your teaching portfolio.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(44, 9, 'CHED Submission', '2025-06-22', 'read', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(45, 6, 'Document Submission', '2025-07-24', 'sent', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(46, 4, 'Deadline Approaching', '2025-06-15', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(47, 5, 'Document Submission', '2025-07-20', 'read', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(48, 7, 'Compliance Update', '2025-08-03', 'read', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(49, 10, 'Deadline Approaching', '2025-07-30', 'pending', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(50, 7, 'Document Submission', '2025-05-07', 'read', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(51, 11, 'Document Submission', '2025-07-15', 'read', 'Please submit your required documentation by the due date.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(52, 9, 'Deadline Approaching', '2025-07-18', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(53, 11, 'Deadline Approaching', '2025-05-17', 'read', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(54, 6, 'CHED Submission', '2025-05-30', 'sent', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(55, 5, 'CHED Submission', '2025-07-23', 'read', 'CHED documentation submission is due soon. Please prepare your materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(56, 3, 'Compliance Update', '2025-07-08', 'read', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(57, 4, 'Deadline Approaching', '2025-06-07', 'sent', 'Reminder: An important deadline is approaching for your required documents.', '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(58, 2, 'Compliance Update', '2025-05-11', 'sent', 'Your compliance status needs to be updated. Please submit the necessary materials.', '2025-05-06 08:28:08', '2025-05-06 08:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `generated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` text NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `report_type`, `generated_date`, `data`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 'Compliance Summary', '2025-02-05 08:28:08', '{\"title\":\"Compliance Summary - February 2025\",\"summary\":\"This report summarizes the current status of compliance summary for the CCIS faculty.\",\"details\":{\"total_faculty\":37,\"compliant\":16,\"pending\":7,\"issues\":1},\"recommendations\":\"Based on the data, we recommend focusing on improving compliance rates for faculty members with pending documents.\"}', 11, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(2, 'Faculty Status', '2025-04-23 08:28:08', '{\"title\":\"Faculty Status - April 2025\",\"summary\":\"This report summarizes the current status of faculty status for the CCIS faculty.\",\"details\":{\"total_faculty\":32,\"compliant\":41,\"pending\":10,\"issues\":3},\"recommendations\":\"Based on the data, we recommend focusing on improving compliance rates for faculty members with pending documents.\"}', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(3, 'Department Performance', '2025-02-05 08:28:08', '{\"title\":\"Department Performance - February 2025\",\"summary\":\"This report summarizes the current status of department performance for the CCIS faculty.\",\"details\":{\"total_faculty\":20,\"compliant\":17,\"pending\":14,\"issues\":5},\"recommendations\":\"Based on the data, we recommend focusing on improving compliance rates for faculty members with pending documents.\"}', 11, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(4, 'Quarterly Review', '2025-04-07 08:28:08', '{\"title\":\"Quarterly Review - April 2025\",\"summary\":\"This report summarizes the current status of quarterly review for the CCIS faculty.\",\"details\":{\"total_faculty\":42,\"compliant\":39,\"pending\":13,\"issues\":1},\"recommendations\":\"Based on the data, we recommend focusing on improving compliance rates for faculty members with pending documents.\"}', 12, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(5, 'Annual Evaluation', '2025-03-19 08:28:08', '{\"title\":\"Annual Evaluation - March 2025\",\"summary\":\"This report summarizes the current status of annual evaluation for the CCIS faculty.\",\"details\":{\"total_faculty\":27,\"compliant\":21,\"pending\":12,\"issues\":5},\"recommendations\":\"Based on the data, we recommend focusing on improving compliance rates for faculty members with pending documents.\"}', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(6, 'CHED Submission', '2025-02-22 08:28:08', '{\"title\":\"CHED Submission - February 2025\",\"summary\":\"This report summarizes the current status of ched submission for the CCIS faculty.\",\"details\":{\"total_faculty\":27,\"compliant\":35,\"pending\":12,\"issues\":0},\"recommendations\":\"Based on the data, we recommend focusing on improving compliance rates for faculty members with pending documents.\"}', 11, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(7, 'Compliance Summary', '2024-12-24 08:28:08', '{\"title\":\"Compliance Summary - December 2024\",\"summary\":\"Fugit ut rerum omnis enim distinctio nihil magni. Dolores ipsam dicta iure vel tempora. Omnis aut voluptatem adipisci velit voluptas. Nulla nostrum sed et quis ex voluptas consequatur.\",\"details\":{\"total_faculty\":45,\"compliant\":17,\"pending\":13,\"issues\":4},\"recommendations\":\"Praesentium vero iste id nobis est rerum. Et quia cumque cum nam ea. Porro labore cum beatae iste incidunt.\"}', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(8, 'Faculty Status', '2025-01-10 08:28:08', '{\"title\":\"Faculty Status - January 2025\",\"summary\":\"Beatae temporibus nesciunt ut ducimus aperiam necessitatibus sapiente vitae. Excepturi nesciunt quos unde officiis neque placeat.\",\"details\":{\"total_faculty\":49,\"compliant\":34,\"pending\":13,\"issues\":1},\"recommendations\":\"Et provident impedit dignissimos non similique ut accusamus. Ex aut quia libero officiis vero expedita dolor. Est repellendus laborum dignissimos officia.\"}', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(9, 'Quarterly Review', '2024-12-15 08:28:08', '{\"title\":\"Quarterly Review - December 2024\",\"summary\":\"Officiis vitae rem quia aut. Qui qui et perferendis. Distinctio labore quod nam repudiandae deleniti. Perferendis praesentium qui nostrum quisquam est ut tempore.\",\"details\":{\"total_faculty\":27,\"compliant\":23,\"pending\":10,\"issues\":10},\"recommendations\":\"Et et pariatur ut ad omnis sit id. Rerum ducimus doloribus nisi tempore provident quis placeat. Provident odit commodi tenetur molestiae ea temporibus porro.\"}', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(10, 'Faculty Status', '2024-11-30 08:28:08', '{\"title\":\"Faculty Status - November 2024\",\"summary\":\"Dolorem velit ut quas consequuntur libero laborum. Ipsam quam eum facilis sed eveniet unde eaque. Beatae quae eligendi ad architecto in eligendi cum ut.\",\"details\":{\"total_faculty\":48,\"compliant\":21,\"pending\":13,\"issues\":4},\"recommendations\":\"Autem incidunt quaerat et aperiam occaecati ipsum sunt. Est est ea voluptatem voluptatem. Qui labore doloremque at voluptas.\"}', 12, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(11, 'Compliance Summary', '2024-12-25 08:28:08', '{\"title\":\"Compliance Summary - December 2024\",\"summary\":\"In beatae assumenda ea eius et enim porro asperiores. Rerum ea magni mollitia dolorum suscipit quam consectetur. Rerum ad illum nulla omnis adipisci itaque.\",\"details\":{\"total_faculty\":26,\"compliant\":20,\"pending\":6,\"issues\":2},\"recommendations\":\"Distinctio dignissimos sed aut qui excepturi quos. Deserunt corporis et quo qui maiores et. Quasi ex esse voluptatem incidunt nulla quibusdam nulla voluptate.\"}', 12, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(12, 'Department Performance', '2025-02-07 08:28:08', '{\"title\":\"Department Performance - February 2025\",\"summary\":\"Voluptatibus hic aut vel et. Repellat et eaque quia rerum corrupti dolores consectetur. Qui consequatur laudantium commodi nisi magnam voluptas ut aut.\",\"details\":{\"total_faculty\":45,\"compliant\":22,\"pending\":6,\"issues\":5},\"recommendations\":\"Tempore reprehenderit voluptas odit voluptatibus tenetur dolorem. Amet expedita laborum ut provident saepe est. Sed sint iste velit dolorem nemo.\"}', 12, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(13, 'CHED Submission', '2024-11-28 08:28:08', '{\"title\":\"CHED Submission - November 2024\",\"summary\":\"Autem modi et asperiores. Asperiores saepe ab praesentium quidem commodi distinctio incidunt quaerat. Rerum perferendis inventore aut laudantium ut unde dolor.\",\"details\":{\"total_faculty\":43,\"compliant\":22,\"pending\":10,\"issues\":1},\"recommendations\":\"Molestiae voluptas ab officia est. Perspiciatis odio dolor voluptatum velit accusamus. Sequi voluptatem laboriosam ut non corporis voluptatem.\"}', 11, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(14, 'Quarterly Review', '2024-12-05 08:28:08', '{\"title\":\"Quarterly Review - December 2024\",\"summary\":\"Earum mollitia amet impedit omnis. Ut id quisquam ducimus aut id nihil quia. Consequatur modi in veniam laudantium esse.\",\"details\":{\"total_faculty\":47,\"compliant\":43,\"pending\":11,\"issues\":9},\"recommendations\":\"Atque qui ducimus velit repellendus. Totam explicabo dolores vel perferendis voluptates. Tenetur et aspernatur ut aut natus et.\"}', 11, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(15, 'Annual Evaluation', '2024-11-29 08:28:08', '{\"title\":\"Annual Evaluation - November 2024\",\"summary\":\"Vero quo est temporibus id id alias voluptatibus. Natus sed est velit nihil sapiente.\",\"details\":{\"total_faculty\":37,\"compliant\":16,\"pending\":5,\"issues\":6},\"recommendations\":\"Nulla optio recusandae voluptate doloremque magnam soluta. Distinctio nihil incidunt cum asperiores qui tempora non. Eaque pariatur alias adipisci.\"}', 11, '2025-05-06 08:28:08', '2025-05-06 08:28:08'),
(16, 'Faculty Status', '2025-03-02 08:28:08', '{\"title\":\"Faculty Status - March 2025\",\"summary\":\"Eum vel commodi consequuntur culpa. Ipsa quo et error nam in non eligendi explicabo. Nemo magni quo laboriosam ut. Fugit culpa itaque nesciunt. Sit cupiditate aut repudiandae vero nam.\",\"details\":{\"total_faculty\":28,\"compliant\":23,\"pending\":5,\"issues\":1},\"recommendations\":\"Nemo ea odit vero dicta. Aspernatur quibusdam autem ea. Adipisci qui natus ipsa sit.\"}', 1, '2025-05-06 08:28:08', '2025-05-06 08:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `roles_id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roles_id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Faculty', '2025-05-06 08:28:05', '2025-05-06 08:28:05'),
(2, 'Admin', '2025-05-06 08:28:05', '2025-05-06 08:28:05'),
(3, 'Dean', '2025-05-06 08:28:05', '2025-05-06 08:28:05'),
(4, 'Coordinator', '2025-05-06 08:28:05', '2025-05-06 08:28:05');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('dkjvVuLuSL14fqx8jO16NrFQex4DwAOD5F0fiXYl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZDREeTViZXpZNWRHZmFwNTBESXF2Q3BLa0N0YTFoeTVlN29zVmVLNCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9hcHByb3ZhbHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1746554214),
('ebkmH9DEAovU3YSSXve5BKrQ7o8gkbyMlkbcyfzF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkNKZG1qcUpLM2RxYzVkdWh4d2dQc0lkRnBVNFBXdlk4UXNLUzRZdiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9hcHByb3ZhbHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1746554020);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `audit_logs_faculty_id_foreign` (`faculty_id`),
  ADD KEY `audit_logs_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `ched_compliance_requirements`
--
ALTER TABLE `ched_compliance_requirements`
  ADD PRIMARY KEY (`requirement_id`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`credential_id`),
  ADD KEY `credentials_faculty_id_foreign` (`faculty_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `documents_faculty_id_foreign` (`faculty_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `faculty_compliance_status`
--
ALTER TABLE `faculty_compliance_status`
  ADD PRIMARY KEY (`compliance_id`),
  ADD UNIQUE KEY `faculty_compliance_status_faculty_id_requirement_id_unique` (`faculty_id`,`requirement_id`),
  ADD KEY `faculty_compliance_status_requirement_id_foreign` (`requirement_id`);

--
-- Indexes for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `faculty_profiles_faculty_id_foreign` (`faculty_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `reminders_faculty_id_foreign` (`faculty_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reports_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roles_id`),
  ADD UNIQUE KEY `roles_role_name_unique` (`role_name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `ched_compliance_requirements`
--
ALTER TABLE `ched_compliance_requirements`
  MODIFY `requirement_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `credential_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `faculty_compliance_status`
--
ALTER TABLE `faculty_compliance_status`
  MODIFY `compliance_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  MODIFY `profile_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminder_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roles_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audit_logs_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `credentials`
--
ALTER TABLE `credentials`
  ADD CONSTRAINT `credentials_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`roles_id`);

--
-- Constraints for table `faculty_compliance_status`
--
ALTER TABLE `faculty_compliance_status`
  ADD CONSTRAINT `faculty_compliance_status_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faculty_compliance_status_requirement_id_foreign` FOREIGN KEY (`requirement_id`) REFERENCES `ched_compliance_requirements` (`requirement_id`) ON DELETE CASCADE;

--
-- Constraints for table `faculty_profiles`
--
ALTER TABLE `faculty_profiles`
  ADD CONSTRAINT `faculty_profiles_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
