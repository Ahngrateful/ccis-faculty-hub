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


// Get report data
$compliance_rate = 0;
$total_requirements = 0;
$total_submissions = 0;
$approvals = 0;
$rejections = 0;
$pending = 0;
$total_faculty = 0;
$total_active_faculty = 0;
$total_inactive_faculty = 0;
$recent_submissions = [];
$top_faculty = [];
$low_compliance_faculty = [];
$requirement_categories = [];

// Get total requirements
$query = "SELECT COUNT(*) as total FROM ched_compliance_requirements";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_requirements = $row['total'];
}

// Get total submissions
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_submissions = $row['total'];
}

// Get approvals
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status WHERE status = 'approved'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $approvals = $row['total'];
}

// Get rejections
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status WHERE status = 'rejected'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $rejections = $row['total'];
}

// Get pending
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $pending = $row['total'];
}

// Get faculty counts
$query = "SELECT COUNT(*) as total FROM faculty WHERE role_id = '1'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_faculty = $row['total'];
}

$query = "SELECT COUNT(*) as total FROM faculty WHERE role_id = '1' AND status = 'active'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_active_faculty = $row['total'];
}

$query = "SELECT COUNT(*) as total FROM faculty WHERE role_id = '1' AND status = 'inactive'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_inactive_faculty = $row['total'];
}

// Calculate compliance rate
if ($total_active_faculty > 0 && $total_requirements > 0) {
    $compliance_rate = ($approvals / ($total_active_faculty * $total_requirements)) * 100;
}

// Get recent submissions (last 5)
$query = "SELECT fcs.*, f.first_name, f.last_name, cr.requirement_name
          FROM faculty_compliance_status fcs
          JOIN faculty f ON fcs.faculty_id = f.faculty_id
          JOIN ched_compliance_requirements cr ON fcs.requirement_id = cr.requirement_id
          ORDER BY fcs.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_submissions[] = $row;
    }
}

// Get requirement categories (for visualization)
$requirement_categories = [
    'Educational Qualifications' => 0,
    'Professional Experience' => 0,
    'Research & Publications' => 0,
    'Training & Certifications' => 0,
    'Other Requirements' => 0
];

// Since we don't have actual categories in the database, we'll assign them based on requirement_id
$query = "SELECT requirement_id, requirement_name FROM ched_compliance_requirements";
$result = mysqli_query($conn, $query);
if ($result) {
    $i = 0;
    $categories = array_keys($requirement_categories);
    while ($row = mysqli_fetch_assoc($result)) {
        $category = $categories[$i % count($categories)];
        $requirement_categories[$category]++;
        $i++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Reports - FPMS</title>
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
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        /* Cards */
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 25px;
        }

        /* Report options */
        .report-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .report-item {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
        }

        .report-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .report-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .report-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .report-description {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        /* Download link */
        .download-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .download-link:hover {
            background-color: #005229;
            transform: translateY(-2px);
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: rgba(0, 104, 52, 0.05);
            color: var(--primary);
            font-weight: 600;
        }

        td {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
            background-color: var(--primary-light);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 15px;
        }

        .stat-progress {
            height: 6px;
            background-color: #f1f1f1;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 3px;
        }

        .stat-footer {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .stat-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 12px;
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .stat-badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .stat-badge-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .stat-badge-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .stat-badge-inactive {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        /* Chart container */
        .chart-container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .chart-item {
            position: relative;
            height: 250px;
            display: flex;
            flex-direction: column;
        }

        .chart-item canvas {
            flex: 1;
            width: 100% !important;
            height: 200px !important;
        }

        .chart-title {
            text-align: center;
            font-size: 1rem;
            color: var(--text-light);
            margin-top: 15px;
            padding-bottom: 5px;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-approved {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .status-rejected {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        /* Action links */
        .action-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 4px;
            transition: var(--transition);
        }

        .action-link:hover {
            background-color: var(--primary-light);
        }

        /* Card actions */
        .card-actions {
            display: flex;
            gap: 10px;
        }

        .form-select {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 0.9rem;
            color: var(--text-dark);
            cursor: pointer;
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 5px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-sm:hover {
            background-color: var(--primary);
            color: white;
        }

        .text-center {
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            padding: 15px 30px;
            background-color: var(--white);
            color: var(--text-light);
            text-align: center;
            font-size: 0.9rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
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
                margin-right: 0;
            }

            .content {
                width: calc(100% - 80px);
            }
        }

        @media (max-width: 768px) {
            .report-options {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .header-right {
                width: 100%;
                justify-content: flex-end;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
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
                <a href="reports.php" class="active"><i class="fa-solid fa-chart-pie"></i> <span>Reports</span></a>
                <a href="faculty_management.php"><i class="fa-solid fa-users-gear"></i> <span>Faculty
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
                    <h1 class="page-title"><i class="fa-solid fa-chart-pie"></i> Reports</h1>
                </div>
                <div class="header-right">
                    <div class="notification-bell">
                        <i class="fa-solid fa-bell"></i>
                        <div class="notification-badge">2</div>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo substr($_SESSION['admin_name'] ?? 'A', 0, 1); ?></div>
                        <div>
                            <p style="font-weight: 600; margin: 0;">
                                <?php echo $_SESSION['admin_name'] ?? 'Admin User'; ?>
                            </p>
                            <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content">
                <!-- Summary Stats Cards -->
                <div class="stats-overview">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo number_format($compliance_rate, 1); ?>%</div>
                            <div class="stat-label">Overall Compliance</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: <?php echo min(100, $compliance_rate); ?>%;"></div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_active_faculty; ?></div>
                            <div class="stat-label">Active Faculty</div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-badge">Total: <?php echo $total_faculty; ?></span>
                            <span class="stat-badge stat-badge-inactive">Inactive:
                                <?php echo $total_inactive_faculty; ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_requirements; ?></div>
                            <div class="stat-label">Total Requirements</div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-badge">Categories: <?php echo count($requirement_categories); ?></span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo $total_submissions; ?></div>
                            <div class="stat-label">Total Submissions</div>
                        </div>
                        <div class="stat-footer">
                            <span class="stat-badge stat-badge-success">Approved: <?php echo $approvals; ?></span>
                            <span class="stat-badge stat-badge-danger">Rejected: <?php echo $rejections; ?></span>
                            <span class="stat-badge stat-badge-warning">Pending: <?php echo $pending; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Compliance Overview Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-chart-pie"></i>
                            Compliance Overview
                        </h2>
                        <div class="card-actions">
                            <select id="timeRangeSelector" class="form-select">
                                <option value="all">All Time</option>
                                <option value="year">This Year</option>
                                <option value="quarter">This Quarter</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div class="chart-grid">
                                <div class="chart-item">
                                    <div style="position: relative; height: 200px; width: 100%;">
                                        <canvas id="complianceChart"></canvas>
                                    </div>
                                    <div class="chart-title">Overall Faculty Compliance Rate</div>
                                </div>
                                <div class="chart-item">
                                    <div style="position: relative; height: 200px; width: 100%;">
                                        <canvas id="submissionsChart"></canvas>
                                    </div>
                                    <div class="chart-title">Submission Status</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Recent Submission Activity
                        </h2>
                        <div class="card-actions">
                            <a href="generate_report_pdf.php?type=activity" class="btn-sm">
                                <i class="fa-solid fa-file-pdf"></i> Export Activity
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Faculty</th>
                                        <th>Requirement</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($recent_submissions) > 0): ?>
                                        <?php foreach ($recent_submissions as $submission): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($submission['requirement_name']); ?></td>
                                                <td>
                                                    <?php if ($submission['status'] == 'approved'): ?>
                                                        <span class="status-badge status-approved">Approved</span>
                                                    <?php elseif ($submission['status'] == 'rejected'): ?>
                                                        <span class="status-badge status-rejected">Rejected</span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-pending">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($submission['created_at'])); ?></td>
                                                <td>
                                                    <a href="#" class="action-link">
                                                        <i class="fa-solid fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No recent submissions found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="report-options">
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="report-title">Faculty Compliance Report</div>
                        <div class="report-description">
                            Detailed report on each faculty member's compliance with CHED requirements.
                        </div>
                        <a href="generate_report_pdf.php?type=faculty" class="download-link">
                            <i class="fa-solid fa-file-pdf"></i> Generate PDF Report
                        </a>
                    </div>
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </div>
                        <div class="report-title">Requirements Summary</div>
                        <div class="report-description">
                            Summary of all requirements and their completion status across faculty.
                        </div>
                        <a href="generate_report_pdf.php?type=requirements" class="download-link">
                            <i class="fa-solid fa-file-pdf"></i> Generate PDF Report
                        </a>
                    </div>
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fa-solid fa-chart-column"></i>
                        </div>
                        <div class="report-title">Monthly Activity Report</div>
                        <div class="report-description">
                            Monthly breakdown of submissions, approvals, and rejections.
                        </div>
                        <a href="generate_report_pdf.php?type=monthly" class="download-link">
                            <i class="fa-solid fa-file-pdf"></i> Generate PDF Report
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-ranking-star"></i>
                            Top Faculty by Compliance
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Faculty Name</th>
                                        <th>Compliance Rate</th>
                                        <th>Approved Requirements</th>
                                        <th>Total Requirements</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get top 5 faculty by compliance rate
                                    $top_faculty_query = "SELECT f.faculty_id, f.first_name, f.last_name,
                                                         COUNT(DISTINCT cr.requirement_id) as total_requirements,
                                                         SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved_requirements
                                                         FROM faculty f
                                                         CROSS JOIN ched_compliance_requirements cr
                                                         LEFT JOIN faculty_compliance_status fcs ON f.faculty_id = fcs.faculty_id AND cr.requirement_id = fcs.requirement_id
                                                         WHERE f.status = 'active' AND f.role_id = '1'
                                                         GROUP BY f.faculty_id
                                                         ORDER BY (SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) / COUNT(DISTINCT cr.requirement_id)) DESC, f.last_name, f.first_name
                                                         LIMIT 5";
                                    $top_faculty_result = mysqli_query($conn, $top_faculty_query);

                                    if ($top_faculty_result) {
                                        $rank = 1;
                                        while ($faculty = mysqli_fetch_assoc($top_faculty_result)) {
                                            $faculty_compliance = ($faculty['total_requirements'] > 0) ?
                                                ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;

                                            echo "<tr>";
                                            echo "<td>" . $rank . "</td>";
                                            echo "<td>" . htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']) . "</td>";
                                            echo "<td>" . number_format($faculty_compliance, 2) . "%</td>";
                                            echo "<td>" . $faculty['approved_requirements'] . "</td>";
                                            echo "<td>" . $faculty['total_requirements'] . "</td>";
                                            echo "</tr>";

                                            $rank++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No data available</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-file-export"></i>
                            CHED Compliance Export
                        </h2>
                    </div>
                    <div class="card-body">
                        <p style="margin-bottom: 20px;">
                            Generate a comprehensive compliance report suitable for submission to CHED. This export
                            includes faculty qualifications, document status, and compliance percentages.
                        </p>
                        <a href="ched_export_pdf.php" class="download-link" style="padding: 12px 24px;">
                            <i class="fa-solid fa-file-pdf"></i> Generate CHED Compliance Report (PDF)
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer">
                <p> 2025 University of Makati - CCIS Faculty Project Management System v1.0 | <a href="#">Help
                        Center</a> | <a href="#">Contact Support</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Compliance gauge chart
        const complianceCtx = document.getElementById('complianceChart').getContext('2d');
        const complianceRate = <?php echo number_format($compliance_rate, 1); ?>;

        new Chart(complianceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Compliant', 'Non-Compliant'],
                datasets: [{
                    data: [complianceRate, 100 - complianceRate],
                    backgroundColor: [
                        '#006834',
                        '#f1f1f1'
                    ],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw: function (chart) {
                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;

                    ctx.restore();
                    ctx.font = 'bold 24px Arial';
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#006834';
                    ctx.fillText(complianceRate + '%', width / 2, height / 2);
                    ctx.save();
                }
            }]
        });

        // Submissions pie chart
        const submissionsCtx = document.getElementById('submissionsChart').getContext('2d');
        const approvals = <?php echo $approvals; ?>;
        const rejections = <?php echo $rejections; ?>;
        const pending = <?php echo $pending; ?>;

        new Chart(submissionsCtx, {
            type: 'pie',
            data: {
                labels: ['Approved', 'Rejected', 'Pending'],
                datasets: [{
                    data: [approvals, rejections, pending],
                    backgroundColor: [
                        '#28a745',
                        '#dc3545',
                        '#ffc107'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.parsed / total) * 100);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Categories chart
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            const categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: [
                        <?php
                        foreach ($requirement_categories as $category => $count) {
                            echo "'" . $category . "', ";
                        }
                        ?>
                    ],
                    datasets: [{
                        label: 'Requirements by Category',
                        data: [
                            <?php
                            foreach ($requirement_categories as $category => $count) {
                                echo $count . ", ";
                            }
                            ?>
                        ],
                        backgroundColor: [
                            'rgba(0, 104, 52, 0.7)',
                            'rgba(117, 217, 121, 0.7)',
                            'rgba(52, 152, 219, 0.7)',
                            'rgba(155, 89, 182, 0.7)',
                            'rgba(255, 222, 38, 0.7)'
                        ],
                        borderColor: [
                            'rgba(0, 104, 52, 1)',
                            'rgba(117, 217, 121, 1)',
                            'rgba(52, 152, 219, 1)',
                            'rgba(155, 89, 182, 1)',
                            'rgba(255, 222, 38, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Get monthly data for a trend chart
        <?php
        // Get data for the last 6 months
        $monthly_data = [];
        $monthly_labels = [];
        $monthly_submissions = [];
        $monthly_approvals = [];
        $monthly_rejections = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $month_start = $month . '-01';
            $month_end = date('Y-m-t', strtotime($month_start));

            $stats_query = "SELECT
                           COUNT(*) as total_submissions,
                           SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approvals,
                           SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejections
                           FROM faculty_compliance_status
                           WHERE created_at BETWEEN '$month_start' AND '$month_end'";
            $stats_result = mysqli_query($conn, $stats_query);

            if ($stats_result && $stats = mysqli_fetch_assoc($stats_result)) {
                $monthly_labels[] = date('M Y', strtotime($month_start));
                $monthly_submissions[] = (int) $stats['total_submissions'];
                $monthly_approvals[] = (int) $stats['approvals'];
                $monthly_rejections[] = (int) $stats['rejections'];
            }
        }
        ?>

        // Add a trend chart to the page
        const trendChartContainer = document.createElement('div');
        trendChartContainer.style.marginTop = '30px';
        trendChartContainer.innerHTML = `
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fa-solid fa-chart-line"></i>
                        Monthly Submission Trends
                    </h2>
                    <div class="card-actions">
                        <select id="trendTimeRange" class="form-select">
                            <option value="6">Last 6 Months</option>
                            <option value="12">Last 12 Months</option>
                            <option value="3">Last 3 Months</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        `;

        // Insert the trend chart after the compliance overview card
        document.querySelector('.main-content').insertBefore(trendChartContainer, document.querySelector('.report-options'));

        // Time range selector functionality
        document.getElementById('timeRangeSelector').addEventListener('change', function () {
            // In a real implementation, this would filter the data based on the selected time range
            const timeRange = this.value;
            console.log('Filtering compliance data to show: ' + timeRange);
            // Here you would make an AJAX call to get filtered data and update the charts
        });

        // Trend time range selector functionality
        document.addEventListener('DOMContentLoaded', function () {
            const trendTimeRange = document.getElementById('trendTimeRange');
            if (trendTimeRange) {
                trendTimeRange.addEventListener('change', function () {
                    // In a real implementation, this would update the trend chart with the selected time range
                    const months = parseInt(this.value);
                    console.log('Updating trend chart to show last ' + months + ' months');
                    // Here you would make an AJAX call to get trend data for the selected time range
                });
            }
        });

        // Render the trend chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthly_labels); ?>,
                datasets: [
                    {
                        label: 'Total Submissions',
                        data: <?php echo json_encode($monthly_submissions); ?>,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Approvals',
                        data: <?php echo json_encode($monthly_approvals); ?>,
                        borderColor: '#006834',
                        backgroundColor: 'rgba(0, 104, 52, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Rejections',
                        data: <?php echo json_encode($monthly_rejections); ?>,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    </script>
</body>

</html>