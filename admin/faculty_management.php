<?php
// Start session
session_start();
// Database connection
require_once("dbconn.php");

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Fetch all faculty members
$query = "SELECT f.*, r.role_name
          FROM faculty f
          JOIN roles r ON f.role_id = r.roles_id
          ORDER BY f.faculty_id";
$result = mysqli_query($conn, $query);

// Check for query execution success
if (!$result) {
    $error_message = "Error fetching faculty data: " . mysqli_error($conn);
}

// Store faculty data in array
$faculty_members = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faculty_members[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Faculty Management - FPMS</title>
    <style>
        /* Global Styles */
        :root {
            --primary: #006834;
            --primary-light: rgba(0, 104, 52, 0.1);
            --secondary: #75d979;
            --accent: #ffde26;
            --text-dark: #333333;
            --text-light: #666666;
            --gray-light: #f9f9f9;
            --white: #ffffff;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--gray-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Layout */
        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
            color: white;
            padding: 20px;
            box-shadow: var(--shadow-md);
            position: relative;
            z-index: 10;
            transition: var(--transition);
        }

        .sidebar-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
        }

        .logo {
            height: 90px;
            width: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
            transition: var(--transition);
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .sidebar h3 {
            color: var(--accent);
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(117, 217, 121, 0.3);
            text-align: center;
            width: 100%;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
        }

        .sidebar a i {
            font-size: 1.1rem;
            width: 24px;
        }

        .sidebar a:hover {
            background-color: rgba(117, 217, 121, 0.15);
            color: white;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: linear-gradient(90deg, var(--secondary) 0%, #63c967 100%);
            color: var(--primary);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(117, 217, 121, 0.3);
        }

        .sidebar a.logout {
            margin-top: auto;
            color: rgba(255, 255, 255, 0.7);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            padding-top: 20px;
        }

        .sidebar a.logout:hover {
            color: var(--accent);
        }

        /* Content Area */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--gray-light);
        }

        /* Header */
        .header {
            background-color: var(--white);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 30px;
            transition: var(--transition);
            background-color: var(--primary-light);
        }

        .user-profile:hover {
            background-color: rgba(0, 104, 52, 0.15);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .notification-bell {
            position: relative;
            padding: 12px;
            border-radius: 50%;
            background-color: var(--gray-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .notification-bell:hover {
            background-color: var(--primary-light);
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 15px;
            height: 15px;
            background-color: #ff5252;
            border-radius: 50%;
            font-size: 0.6rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        h2 {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 2px;
        }

        /* Filters */
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 15px;
        }

        .filters-left {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            flex: 1;
        }

        .filters-right {
            display: flex;
            justify-content: flex-end;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-title {
            font-weight: 600;
            color: var(--primary);
            white-space: nowrap;
        }

        /* Filter Stats */
        .filter-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter-stat-item {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 15px 20px;
            box-shadow: var(--shadow-sm);
            flex: 1;
            min-width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: var(--transition);
            border-top: 3px solid var(--primary);
        }

        .filter-stat-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .filter-stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .filter-stat-label {
            font-size: 0.9rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        select,
        input[type="text"],
        input[type="date"] {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: var(--white);
            color: var(--text-dark);
            font-size: 0.95rem;
            transition: var(--transition);
            min-width: 180px;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 104, 52, 0.1);
        }

        .search-box {
            position: relative;
            flex-grow: 1;
        }

        .search-box input {
            width: 100%;
            padding-left: 40px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        /* Button Styles */
        button {
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
            color: white;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0, 104, 52, 0.2);
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 104, 52, 0.25);
        }

        button i {
            font-size: 1.1rem;
        }

        /* Table Styles */
        .table-container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
        }

        .table-loader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .table-loader.active {
            opacity: 1;
            visibility: visible;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 104, 52, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        table,
        th,
        td {
            border: none;
        }

        thead {
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            position: relative;
        }

        th:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.1);
        }

        th:hover {
            background-color: rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }

        th i {
            margin-left: 5px;
            opacity: 0.7;
        }

        tbody tr {
            background-color: var(--white);
            transition: var(--transition);
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        tr.filtered-in {
            animation: fadeIn 0.5s ease forwards;
        }

        tr.filtered-out {
            animation: fadeOut 0.5s ease forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(10px);
            }
        }

        tr:hover {
            background-color: rgba(0, 104, 52, 0.05);
        }

        td {
            padding: 16px 20px;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Animation for filter stats */
        .filter-stat-item {
            opacity: 0;
            transform: translateY(20px);
        }

        .filter-stat-item.animate-in {
            animation: slideUp 0.5s ease forwards;
            animation-delay: calc(var(--item-index, 0) * 0.1s);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Sorted rows animation */
        tr.sorted {
            animation: fadeInSort 0.5s ease forwards;
        }

        @keyframes fadeInSort {
            from {
                opacity: 0.5;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Clear filters button */
        .clear-filters {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #666;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .clear-filters:hover {
            background-color: #e9ecef;
            color: #333;
        }

        /* Status Styles */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .status-inactive {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }

        /* Action links */
        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-edit {
            background-color: rgba(0, 104, 52, 0.1);
            color: var(--primary);
            border: 1px solid rgba(0, 104, 52, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(0, 104, 52, 0.2);
        }

        .btn-activate {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .btn-activate:hover {
            background-color: rgba(76, 175, 80, 0.2);
        }

        .btn-deactivate {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
            border: 1px solid rgba(244, 67, 54, 0.2);
        }

        .btn-deactivate:hover {
            background-color: rgba(244, 67, 54, 0.2);
        }

        /* Footer */
        .footer {
            background-color: var(--white);
            padding: 20px 30px;
            text-align: center;
            color: var(--text-light);
            border-top: 1px solid #eaeaea;
            font-size: 0.9rem;
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .sidebar {
                width: 250px;
            }

            .actions {
                flex-direction: column;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }

            .sidebar h3,
            .sidebar a span {
                display: none;
            }

            .sidebar a {
                justify-content: center;
                padding: 15px;
            }

            .sidebar a i {
                font-size: 1.4rem;
                margin: 0;
            }

            .sidebar-header {
                margin-bottom: 20px;
            }

            .logo {
                height: 60px;
                margin-left: 0;
            }

            .content {
                width: calc(100% - 80px);
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            transition: var(--transition);
        }

        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 50%;
            max-width: 600px;
            box-shadow: var(--shadow-lg);
            transform: translateY(0);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal h2 {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
            font-weight: 600;
        }

        .modal form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal label {
            color: var(--dark);
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }

        .modal input,
        .modal select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .modal input:focus,
        .modal select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(255, 233, 125, 0.2);
            outline: none;
        }

        .modal button[type="submit"] {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal button[type="submit"]:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .close {
            color: #777;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            position: absolute;
            right: 25px;
            top: 15px;
        }

        .close:hover {
            color: var(--primary);
        }

        .image-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .image-upload img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            margin-bottom: 10px;
        }

        .image-upload input[type="file"] {
            width: 100%;
            padding: 8px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row>div {
            flex: 1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                padding: 20px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/CCIS-Logo-Official.png" alt="College Logo" class="logo">
                <h3><i>Faculty Project Management System</i></h3>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span></a>
                <a href="approvals.php"><i class="fa-solid fa-check-to-slot"></i> <span>Approvals</span></a>
                <a href="reports.php"><i class="fa-solid fa-chart-pie"></i> <span>Reports</span></a>
                <a href="faculty_management.php" class="active"><i class="fa-solid fa-users-gear"></i> <span>Faculty
                        Management</span></a>
                <a href="ched_compli-audit.php"><i class="fa-solid fa-clipboard-check"></i> <span>CHED Compliance
                        Audit</span></a>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span></a>
            </div>
        </div>
        <div class="content">
            <div class="header">
                <div class="header-left">
                    <h1 class="page-title"><i class="fa-solid fa-users-gear"></i> Faculty Management</h1>
                </div>
                <div class="header-right">
                    <div class="notification-bell">
                        <i class="fa-solid fa-bell"></i>
                        <div class="notification-badge">3</div>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">A</div>
                        <div>
                            <p style="font-weight: 600; margin: 0;">Admin User</p>
                            <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content">
                <h2>Faculty Management</h2>

                <?php
                // Display success message if set
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;">';
                    echo '<i class="fa-solid fa-check-circle"></i> ' . $_SESSION['success_message'];
                    echo '</div>';
                    unset($_SESSION['success_message']);
                }

                // Display error message if set
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">';
                    echo '<i class="fa-solid fa-exclamation-circle"></i> ' . $_SESSION['error_message'];
                    echo '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>
                <div class="filters">
                    <div class="filters-left">
                        <div class="filter-group">
                            <div class="filter-title">Filter by:</div>
                            <select id="statusFilter">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <div class="filter-title">Role:</div>
                            <select id="roleFilter">
                                <option value="all">All Roles</option>
                                <option value="1">Faculty</option>
                                <option value="2">Admin</option>
                            </select>
                        </div>
                        <div class="search-box">
                            <i class="fa-solid fa-search"></i>
                            <input type="text" placeholder="Search by ID, name or email..." id="searchInput">
                        </div>
                        <button id="clearFilters" class="clear-filters">
                            <i class="fa-solid fa-filter-circle-xmark"></i> Clear Filters
                        </button>
                    </div>
                    <div class="filters-right">
                        <button id="addFacultyBtn"><i class="fa-solid fa-user-plus"></i> Add New Faculty</button>
                    </div>
                </div>
                <div class="filter-stats">
                    <div class="filter-stat-item">
                        <span id="totalFaculty" class="filter-stat-number">0</span>
                        <span class="filter-stat-label">Total Faculty</span>
                    </div>
                    <div class="filter-stat-item">
                        <span id="activeFaculty" class="filter-stat-number">0</span>
                        <span class="filter-stat-label">Active</span>
                    </div>
                    <div class="filter-stat-item">
                        <span id="inactiveFaculty" class="filter-stat-number">0</span>
                        <span class="filter-stat-label">Inactive</span>
                    </div>
                    <div class="filter-stat-item">
                        <span id="filteredCount" class="filter-stat-number">0</span>
                        <span class="filter-stat-label">Filtered Results</span>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-loader">
                        <div class="loader-spinner"></div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th data-sort="id">Faculty ID <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="first_name">First Name <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="last_name">Last Name <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="email">Email <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="date">Account Creation <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="role">Role <i class="fa-solid fa-sort"></i></th>
                                <th data-sort="status">Status <i class="fa-solid fa-sort"></i></th>
                                <th>Profile Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($faculty_members)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">No faculty members found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($faculty_members as $faculty): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($faculty['faculty_id']); ?></td>
                                        <td><?php echo htmlspecialchars($faculty['first_name']); ?></td>
                                        <td><?php echo htmlspecialchars($faculty['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($faculty['email']); ?></td>
                                        <td><?php echo htmlspecialchars($faculty['account_creation_date']); ?></td>
                                        <td><?php echo htmlspecialchars($faculty['role_name']); ?></td>
                                        <td>
                                            <?php if (strtolower($faculty['status']) == 'active'): ?>
                                                <span class="status status-active"><i class="fa-solid fa-circle-check"></i>
                                                    Active</span>
                                            <?php else: ?>
                                                <span class="status status-inactive"><i class="fa-solid fa-circle-xmark"></i>
                                                    Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($faculty['profile_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($faculty['profile_image']); ?>" alt="Profile"
                                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="../assets/placeholder.jpg" alt="Profile"
                                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="#" class="btn btn-edit"
                                                data-faculty-id="<?php echo htmlspecialchars($faculty['faculty_id']); ?>">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <?php if (strtolower($faculty['status']) == 'active'): ?>
                                                <a href="#" class="btn btn-deactivate"
                                                    data-faculty-id="<?php echo htmlspecialchars($faculty['faculty_id']); ?>">
                                                    <i class="fa-solid fa-user-slash"></i> Deactivate
                                                </a>
                                                <noscript>
                                                    <a href="faculty_management_process.php?action=toggle_status&faculty_id=<?php echo htmlspecialchars($faculty['faculty_id']); ?>"
                                                        class="btn btn-deactivate">
                                                        <i class="fa-solid fa-user-slash"></i> Deactivate (No JS)
                                                    </a>
                                                </noscript>
                                            <?php else: ?>
                                                <a href="#" class="btn btn-activate"
                                                    data-faculty-id="<?php echo htmlspecialchars($faculty['faculty_id']); ?>">
                                                    <i class="fa-solid fa-user-check"></i> Activate
                                                </a>
                                                <noscript>
                                                    <a href="faculty_management_process.php?action=toggle_status&faculty_id=<?php echo htmlspecialchars($faculty['faculty_id']); ?>"
                                                        class="btn btn-activate">
                                                        <i class="fa-solid fa-user-check"></i> Activate (No JS)
                                                    </a>
                                                </noscript>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- add faculty modal-->
                <div id="addFacultyModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2><i class="fa-solid fa-user-plus"></i> Add New Faculty</h2>
                        <form id="addFacultyForm" action="faculty_management_process.php?action=add" method="POST"
                            enctype="multipart/form-data">
                            <div class="image-upload">
                                <label>Profile Picture</label>
                                <img src="../assets/placeholder.jpg" class="img-thumbnail" alt="Profile Image"
                                    id="employee_image">
                                <input type="file" class="form-control" name="profile_image" id="imageUpload"
                                    accept="image/*">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="faculty_id">Faculty ID:</label>
                                    <input type="text" name="faculty_id" id="faculty_id" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name:</label>
                                    <input type="text" name="first_name" id="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name:</label>
                                    <input type="text" name="last_name" id="last_name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address:</label>
                                <input type="email" name="email" id="email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password:</label>
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" name="password" id="password" readonly style="flex-grow: 1;">
                                    <button type="button" id="generatePasswordBtn"
                                        style="white-space: nowrap;">Generate</button>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="account_creation_date">Account Creation Date:</label>
                                    <input type="date" name="account_creation_date" id="account_creation_date"
                                        value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="role_id">Role:</label>
                                    <select name="role_id" id="role_id" required>
                                        <option value="">Select Role</option>
                                        <option value="1">Faculty</option>
                                        <option value="2">Admin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select name="status" id="status" required>
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div id="addFacultyErrors" class="form-errors"
                                style="color: #721c24; background-color: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 5px; display: none;">
                            </div>

                            <button type="submit" id="addFacultySubmitBtn">Add Faculty</button>
                        </form>
                    </div>
                </div>

                <!--edit faculty modal-->
                <div id="editFacultyModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2><i class="fa-solid fa-pen-to-square"></i> Edit Faculty Profile</h2>
                        <form action="faculty_management_process.php?action=edit" method="POST"
                            enctype="multipart/form-data">

                            <div class="image-upload">
                                <label>Profile Picture</label>
                                <img src="../assets/placeholder.jpg" class="img-thumbnail" alt="Profile Image"
                                    id="edit_employee_image">
                                <input type="file" class="form-control" name="profile_image" id="edit_imageUpload"
                                    accept="image/*">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit_faculty_id">Faculty ID:</label>
                                    <input type="text" name="faculty_id" id="edit_faculty_id" required readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit_first_name">First Name:</label>
                                    <input type="text" name="first_name" id="edit_first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_last_name">Last Name:</label>
                                    <input type="text" name="last_name" id="edit_last_name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_email">Email Address:</label>
                                <input type="email" name="email" id="edit_email" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="edit_account_creation_date">Account Creation Date:</label>
                                    <input type="date" name="account_creation_date" id="edit_account_creation_date"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_role_id">Role:</label>
                                    <select name="role_id" id="edit_role_id" required>
                                        <option value="">Select Role</option>
                                        <option value="1">Faculty</option>
                                        <option value="2">Admin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_status">Status:</label>
                                <select name="status" id="edit_status" required>
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="submit-btn">Update Faculty</button>
                        </form>
                    </div>
                </div>

                <div class="footer">
                    <p> 2025 University of Makati - CCIS Faculty Project Management System v1.0 | <a href="#">Help
                            Center</a> | <a href="#">Contact Support</a></p>
                </div>
            </div>
        </div>
</body>

<script>
    // Add Faculty Modal functionality
    const addModal = document.getElementById('addFacultyModal');
    const addBtn = document.getElementById('addFacultyBtn');
    const addSpan = addModal.querySelector('.close');

    addBtn.onclick = function () {
        addModal.style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling when modal is open
    }

    addSpan.onclick = function () {
        addModal.style.display = "none";
        document.body.style.overflow = "auto"; // Enable scrolling again
    }

    // Edit Faculty Modal functionality
    const editModal = document.getElementById('editFacultyModal');
    const editSpan = editModal.querySelector('.close');

    // Use event delegation for dynamically added elements
    document.addEventListener('click', function (e) {
        // Handle edit button clicks
        if (e.target.closest('.btn-edit')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-edit');
            const facultyId = btn.getAttribute('data-faculty-id');

            // Show loading state
            document.getElementById('edit_faculty_id').value = "Loading...";
            document.getElementById('edit_first_name').value = "Loading...";
            document.getElementById('edit_last_name').value = "Loading...";
            document.getElementById('edit_email').value = "Loading...";

            // Show the modal
            editModal.style.display = "block";
            document.body.style.overflow = "hidden";

            // Create a new XMLHttpRequest to fetch faculty data
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `faculty_management_process.php?action=get_faculty&faculty_id=${facultyId}`, true);

            xhr.onload = function () {
                if (this.status === 200) {
                    try {
                        const faculty = JSON.parse(this.responseText);

                        // Format date for input (YYYY-MM-DD)
                        let formattedDate = faculty.account_creation_date;

                        // Try to parse the date regardless of format
                        try {
                            const dateObj = new Date(formattedDate);
                            if (!isNaN(dateObj.getTime())) {
                                // Valid date, format as YYYY-MM-DD
                                const year = dateObj.getFullYear();
                                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                                const day = String(dateObj.getDate()).padStart(2, '0');
                                formattedDate = `${year}-${month}-${day}`;
                            }
                        } catch (e) {
                            console.error('Error formatting date:', e);
                        }

                        // Populate the edit form with data from the server
                        document.getElementById('edit_faculty_id').value = faculty.faculty_id;
                        document.getElementById('edit_first_name').value = faculty.first_name;
                        document.getElementById('edit_last_name').value = faculty.last_name;
                        document.getElementById('edit_email').value = faculty.email;
                        document.getElementById('edit_account_creation_date').value = formattedDate;
                        document.getElementById('edit_role_id').value = faculty.role_id;
                        document.getElementById('edit_status').value = faculty.status.charAt(0).toUpperCase() + faculty.status.slice(1);

                        // Update profile image if available
                        if (faculty.profile_image) {
                            document.getElementById('edit_employee_image').src = faculty.profile_image;
                        } else {
                            document.getElementById('edit_employee_image').src = '../assets/placeholder.jpg';
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        alert('Error loading faculty data. Please try again.');
                    }
                } else {
                    alert('Error loading faculty data. Please try again.');
                }
            };

            xhr.onerror = function () {
                alert('Error loading faculty data. Please try again.');
            };

            xhr.send();
        }

        // Handle activate/deactivate button clicks
        if (e.target.closest('.btn-activate') || e.target.closest('.btn-deactivate')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-activate') || e.target.closest('.btn-deactivate');
            const row = btn.closest('tr');
            const facultyId = btn.getAttribute('data-faculty-id');
            const statusCell = row.querySelector('td:nth-child(7) .status');
            const isActive = statusCell.textContent.includes('Active');

            // New status will be the opposite of current status
            const newStatus = isActive ? 'inactive' : 'active';

            // Show loading state
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            // Send AJAX request to update status
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'faculty_management_process.php?action=update_status', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);

                        if (response.success) {
                            // Update UI based on new status
                            if (newStatus === 'inactive') {
                                // Changed to inactive
                                statusCell.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Inactive';
                                statusCell.className = 'status status-inactive';
                                btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Activate';
                                btn.className = 'btn btn-activate';
                            } else {
                                // Changed to active
                                statusCell.innerHTML = '<i class="fa-solid fa-circle-check"></i> Active';
                                statusCell.className = 'status status-active';
                                btn.innerHTML = '<i class="fa-solid fa-user-slash"></i> Deactivate';
                                btn.className = 'btn btn-deactivate';
                            }
                            btn.setAttribute('data-faculty-id', facultyId);

                            // Show success message
                            const successMessage = response.message || 'Faculty status updated successfully';
                            const messageDiv = document.createElement('div');
                            messageDiv.className = 'alert alert-success';
                            messageDiv.style.cssText = 'background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 4px 8px rgba(0,0,0,0.1);';
                            messageDiv.innerHTML = '<i class="fa-solid fa-check-circle"></i> ' + successMessage;
                            document.body.appendChild(messageDiv);

                            // Remove the message after 3 seconds
                            setTimeout(() => {
                                messageDiv.style.opacity = '0';
                                messageDiv.style.transition = 'opacity 0.5s ease';
                                setTimeout(() => {
                                    document.body.removeChild(messageDiv);
                                }, 500);
                            }, 3000);
                        } else {
                            alert('Error: ' + (response.error || 'Failed to update status'));
                            // Reset button to original state
                            if (isActive) {
                                btn.innerHTML = '<i class="fa-solid fa-user-slash"></i> Deactivate';
                                btn.className = 'btn btn-deactivate';
                            } else {
                                btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Activate';
                                btn.className = 'btn btn-activate';
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        alert('Error updating status. Please try again.');
                        // Reset button to original state
                        if (isActive) {
                            btn.innerHTML = '<i class="fa-solid fa-user-slash"></i> Deactivate';
                            btn.className = 'btn btn-deactivate';
                        } else {
                            btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Activate';
                            btn.className = 'btn btn-activate';
                        }
                    }
                } else {
                    alert('Error updating status. Please try again.');
                    // Reset button to original state
                    if (isActive) {
                        btn.innerHTML = '<i class="fa-solid fa-user-slash"></i> Deactivate';
                        btn.className = 'btn btn-deactivate';
                    } else {
                        btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Activate';
                        btn.className = 'btn btn-activate';
                    }
                }

                btn.disabled = false;
            };

            xhr.onerror = function () {
                alert('Error updating status. Please try again.');
                // Reset button to original state
                if (isActive) {
                    btn.innerHTML = '<i class="fa-solid fa-user-slash"></i> Deactivate';
                    btn.className = 'btn btn-deactivate';
                } else {
                    btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Activate';
                    btn.className = 'btn btn-activate';
                }
                btn.disabled = false;
            };

            // Send the request
            xhr.send(`faculty_id=${facultyId}&status=${newStatus}`);
        }
    });

    editSpan.onclick = function () {
        editModal.style.display = "none";
        document.body.style.overflow = "auto";
    }

    // Close modals when clicking outside
    window.onclick = function (event) {
        if (event.target == addModal) {
            addModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
        if (event.target == editModal) {
            editModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }

    // Image upload preview for add modal
    document.getElementById("imageUpload").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("employee_image").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Image upload preview for edit modal
    document.getElementById("edit_imageUpload").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("edit_employee_image").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Generate password
    document.getElementById("generatePasswordBtn").addEventListener("click", function () {
        const password = generatePassword();
        document.getElementById("password").value = password;
    });

    function generatePassword(length = 12) {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    // Generate a password by default when the page loads
    window.addEventListener('DOMContentLoaded', function () {
        document.getElementById("password").value = generatePassword();
    });

    // AJAX form submission for adding new faculty
    document.getElementById('addFacultyForm').addEventListener('submit', function (e) {
        e.preventDefault();

        // Show loading state
        const submitBtn = document.getElementById('addFacultySubmitBtn');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;

        // Clear previous errors
        const errorsDiv = document.getElementById('addFacultyErrors');
        errorsDiv.style.display = 'none';
        errorsDiv.innerHTML = '';

        // Create FormData object
        const formData = new FormData(this);

        // Send AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'faculty_management_process.php?action=add', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function () {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);

                    if (response.success) {
                        // Show success message
                        const successMessage = response.message || 'Faculty added successfully';
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'alert alert-success';
                        messageDiv.style.cssText = 'background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 4px 8px rgba(0,0,0,0.1);';
                        messageDiv.innerHTML = '<i class="fa-solid fa-check-circle"></i> ' + successMessage;
                        document.body.appendChild(messageDiv);

                        // Close the modal
                        addModal.style.display = "none";
                        document.body.style.overflow = "auto";

                        // Reset the form
                        document.getElementById('addFacultyForm').reset();
                        document.getElementById('employee_image').src = '../assets/placeholder.jpg';
                        document.getElementById('password').value = generatePassword();

                        // Add the new faculty to the table
                        addFacultyToTable(response.faculty);

                        // Remove the message after 3 seconds
                        setTimeout(() => {
                            messageDiv.style.opacity = '0';
                            messageDiv.style.transition = 'opacity 0.5s ease';
                            setTimeout(() => {
                                document.body.removeChild(messageDiv);
                            }, 500);
                        }, 3000);
                    } else {
                        // Show errors
                        if (response.errors && response.errors.length > 0) {
                            errorsDiv.innerHTML = '<ul style="margin: 0; padding-left: 20px;">' +
                                response.errors.map(error => '<li>' + error + '</li>').join('') +
                                '</ul>';
                            errorsDiv.style.display = 'block';
                        } else {
                            errorsDiv.innerHTML = '<p>An unknown error occurred. Please try again.</p>';
                            errorsDiv.style.display = 'block';
                        }
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    errorsDiv.innerHTML = '<p>An error occurred while processing your request. Please try again.</p>';
                    errorsDiv.style.display = 'block';
                }
            } else {
                errorsDiv.innerHTML = '<p>Server error: ' + this.status + '. Please try again.</p>';
                errorsDiv.style.display = 'block';
            }

            // Reset button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        };

        xhr.onerror = function () {
            errorsDiv.innerHTML = '<p>Network error. Please check your connection and try again.</p>';
            errorsDiv.style.display = 'block';

            // Reset button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        };

        // Send the form data
        xhr.send(formData);
    });

    // Function to add a new faculty to the table
    function addFacultyToTable(faculty) {
        const tbody = document.querySelector('table tbody');

        // Check if there's a "No faculty members found" message
        const noFacultyRow = tbody.querySelector('tr td[colspan]');
        if (noFacultyRow) {
            tbody.innerHTML = ''; // Clear the "No faculty members found" message
        }

        // Create a new row
        const newRow = document.createElement('tr');

        // Get role name based on role_id
        let roleName = 'Faculty';
        if (faculty.role_id === '2') {
            roleName = 'Admin';
        }

        // Format the status
        const statusHtml = faculty.status === 'active' ?
            '<span class="status status-active"><i class="fa-solid fa-circle-check"></i> Active</span>' :
            '<span class="status status-inactive"><i class="fa-solid fa-circle-xmark"></i> Inactive</span>';

        // Set the row HTML
        newRow.innerHTML = `
            <td>${faculty.faculty_id}</td>
            <td>${faculty.first_name}</td>
            <td>${faculty.last_name}</td>
            <td>${faculty.email}</td>
            <td>${faculty.account_creation_date}</td>
            <td>${roleName}</td>
            <td>${statusHtml}</td>
            <td>
                <img src="${faculty.profile_image || '../assets/placeholder.jpg'}" alt="Profile"
                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            </td>
            <td class="actions">
                <a href="#" class="btn btn-edit" data-faculty-id="${faculty.faculty_id}">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
                ${faculty.status === 'active' ?
                `<a href="#" class="btn btn-deactivate" data-faculty-id="${faculty.faculty_id}">
                        <i class="fa-solid fa-user-slash"></i> Deactivate
                    </a>
                    <noscript>
                        <a href="faculty_management_process.php?action=toggle_status&faculty_id=${faculty.faculty_id}"
                           class="btn btn-deactivate">
                            <i class="fa-solid fa-user-slash"></i> Deactivate (No JS)
                        </a>
                    </noscript>` :
                `<a href="#" class="btn btn-activate" data-faculty-id="${faculty.faculty_id}">
                        <i class="fa-solid fa-user-check"></i> Activate
                    </a>
                    <noscript>
                        <a href="faculty_management_process.php?action=toggle_status&faculty_id=${faculty.faculty_id}"
                           class="btn btn-activate">
                            <i class="fa-solid fa-user-check"></i> Activate (No JS)
                        </a>
                    </noscript>`
            }
            </td>
        `;

        // Add the row to the table
        tbody.appendChild(newRow);
    }

    // Function to update filter stats
    function updateFilterStats() {
        const rows = document.querySelectorAll('tbody tr');
        let totalCount = 0;
        let activeCount = 0;
        let inactiveCount = 0;
        let visibleCount = 0;

        rows.forEach(row => {
            // Skip rows that span multiple columns (like the "No faculty members found" message)
            if (row.querySelector('td[colspan]')) {
                return;
            }

            totalCount++;

            // Get the status cell - it's the 7th column (index 6)
            const statusCell = row.querySelector('td:nth-child(7) .status');
            if (statusCell) {
                const statusText = statusCell.textContent.trim().toLowerCase();
                // Check if the status contains "active" but not as part of "inactive"
                if (statusText.includes('active') && !statusText.includes('inactive')) {
                    activeCount++;
                } else if (statusText.includes('inactive')) {
                    inactiveCount++;
                }
            }

            if (row.style.display !== 'none') {
                visibleCount++;
            }
        });

        // Log counts for debugging
        console.log('Stats:', { total: totalCount, active: activeCount, inactive: inactiveCount, visible: visibleCount });

        // Update the stats display
        document.getElementById('totalFaculty').textContent = totalCount;
        document.getElementById('activeFaculty').textContent = activeCount;
        document.getElementById('inactiveFaculty').textContent = inactiveCount;
        document.getElementById('filteredCount').textContent = visibleCount;
    }

    // Function to apply all filters
    function applyFilters() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value.toLowerCase();

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            // Skip rows that span multiple columns (like the "No faculty members found" message)
            if (row.querySelector('td[colspan]')) {
                return;
            }

            // Get cell values
            const facultyId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const firstName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const lastName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const role = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
            const statusCell = row.querySelector('td:nth-child(7) .status');
            const status = statusCell ? statusCell.textContent.trim().toLowerCase() : '';

            // Check if row matches search criteria
            const matchesSearch = searchValue === '' ||
                facultyId.includes(searchValue) ||
                firstName.includes(searchValue) ||
                lastName.includes(searchValue) ||
                email.includes(searchValue);

            // Check if row matches status filter
            let matchesStatus = false;
            if (statusFilter === 'all') {
                matchesStatus = true;
            } else if (statusFilter === 'active') {
                matchesStatus = status.includes('active') && !status.includes('inactive');
            } else if (statusFilter === 'inactive') {
                matchesStatus = status.includes('inactive');
            }

            // Check if row matches role filter
            const matchesRole = roleFilter === 'all' ||
                (roleFilter === '1' && role.includes('faculty')) ||
                (roleFilter === '2' && role.includes('admin'));

            // Show/hide row based on all filters
            if (matchesSearch && matchesStatus && matchesRole) {
                row.style.display = '';
                row.classList.add('filtered-in');
                row.classList.remove('filtered-out');
            } else {
                row.style.display = 'none';
                row.classList.add('filtered-out');
                row.classList.remove('filtered-in');
            }
        });

        // Update stats after filtering
        updateFilterStats();
    }

    // Initialize filter stats on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Set a small delay to ensure DOM is fully loaded
        setTimeout(() => {
            updateFilterStats();

            // Add animation class to stats with staggered delay
            document.querySelectorAll('.filter-stat-item').forEach((item, index) => {
                item.style.setProperty('--item-index', index);
                item.classList.add('animate-in');
            });

            // Initialize sorting indicators
            document.querySelectorAll('th[data-sort] i').forEach(icon => {
                icon.className = 'fa-solid fa-sort';
                icon.style.opacity = '0.5';
            });
        }, 100);
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function () {
        applyFilters();
    });

    // Status filter functionality
    document.getElementById('statusFilter').addEventListener('change', function () {
        applyFilters();
    });

    // Role filter functionality
    document.getElementById('roleFilter').addEventListener('change', function () {
        applyFilters();
    });

    // Clear filters button
    document.addEventListener('click', function (e) {
        if (e.target.id === 'clearFilters') {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('roleFilter').value = 'all';
            applyFilters();
        }
    });

    // Table sorting functionality
    let currentSort = {
        column: null,
        direction: 'asc'
    };

    // Add click event listeners to table headers
    document.querySelectorAll('th[data-sort]').forEach(header => {
        header.addEventListener('click', function () {
            const sortBy = this.getAttribute('data-sort');
            const sortIcon = this.querySelector('i');

            // Show loading indicator
            document.querySelector('.table-loader').classList.add('active');

            // Update sort direction
            if (currentSort.column === sortBy) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = sortBy;
                currentSort.direction = 'asc';
            }

            // Update all sort icons
            document.querySelectorAll('th[data-sort] i').forEach(icon => {
                icon.className = 'fa-solid fa-sort';
                icon.style.opacity = '0.5';
            });

            // Update clicked header's icon
            if (currentSort.direction === 'asc') {
                sortIcon.className = 'fa-solid fa-sort-up';
            } else {
                sortIcon.className = 'fa-solid fa-sort-down';
            }
            sortIcon.style.opacity = '1';

            // Sort the table
            sortTable(sortBy, currentSort.direction);

            // Hide loading indicator after a short delay
            setTimeout(() => {
                document.querySelector('.table-loader').classList.remove('active');
            }, 300);
        });
    });

    // Function to sort the table
    function sortTable(column, direction) {
        const tbody = document.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr:not([colspan])'));

        // Skip if no rows to sort or only has the "No faculty members found" row
        if (rows.length <= 1) return;

        // Define column indices
        const columnMap = {
            'id': 0,
            'first_name': 1,
            'last_name': 2,
            'email': 3,
            'date': 4,
            'role': 5,
            'status': 6
        };

        const columnIndex = columnMap[column];

        // Sort rows
        const sortedRows = rows.sort((a, b) => {
            // Skip rows with colspan (like "No faculty members found")
            if (a.querySelector('td[colspan]') || b.querySelector('td[colspan]')) return 0;

            let aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
            let bValue = b.cells[columnIndex].textContent.trim().toLowerCase();

            // Special handling for dates
            if (column === 'date') {
                aValue = new Date(aValue);
                bValue = new Date(bValue);

                // Handle invalid dates
                if (isNaN(aValue)) aValue = new Date(0);
                if (isNaN(bValue)) bValue = new Date(0);
            }

            // Compare values
            if (aValue < bValue) {
                return direction === 'asc' ? -1 : 1;
            }
            if (aValue > bValue) {
                return direction === 'asc' ? 1 : -1;
            }
            return 0;
        });

        // Remove all rows
        rows.forEach(row => {
            row.remove();
        });

        // Add sorted rows back to the table
        sortedRows.forEach(row => {
            tbody.appendChild(row);
        });

        // Update row classes for animation
        sortedRows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.add('sorted');
        });
    }
</script>

</html>