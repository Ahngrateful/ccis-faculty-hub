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
    
    // Calculate compliance rate
    $total_faculty = 0;
    $query = "SELECT COUNT(*) as total FROM faculty WHERE role_id = '1'";
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $total_faculty = $row['total'];
    }
    
    if ($total_faculty > 0 && $total_requirements > 0) {
        $compliance_rate = ($approvals / ($total_faculty * $total_requirements)) * 100;
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

        th, td {
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

        /* Chart container */
        .chart-container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 20px;
            margin-bottom: 30px;
            height: 350px;
            display: flex;
            justify-content: center;
            align-items: center;
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

            .sidebar h3, .sidebar a span {
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
                <a href="faculty_management.php"><i class="fa-solid fa-users-gear"></i> <span>Faculty Management</span></a>
                <a href="ched_compli-audit.php"><i class="fa-solid fa-clipboard-check"></i> <span>CHED Compliance Audit</span></a>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
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
                            <p style="font-weight: 600; margin: 0;"><?php echo $_SESSION['admin_name'] ?? 'Admin User'; ?></p>
                            <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fa-solid fa-chart-line"></i>
                            Compliance Overview
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <!-- In a real implementation, this would contain a chart -->
                            <div style="text-align: center;">
                                <div style="font-size: 3rem; font-weight: bold; color: var(--primary);"><?php echo number_format($compliance_rate, 1); ?>%</div>
                                <div style="font-size: 1.2rem; color: var(--text-light);">Overall Faculty Compliance Rate</div>
                                <div style="margin-top: 20px; display: flex; justify-content: center; gap: 30px;">
                                    <div>
                                        <div style="font-size: 1.5rem; font-weight: bold; color: var(--primary);"><?php echo $approvals; ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">Requirements Approved</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 1.5rem; font-weight: bold; color: #e74c3c;"><?php echo $rejections; ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">Requirements Rejected</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 1.5rem; font-weight: bold; color: #3498db;"><?php echo $total_submissions; ?></div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">Total Submissions</div>
                                    </div>
                                </div>
                            </div>
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
                        <a href="generate_report.php?type=faculty" class="download-link">
                            <i class="fa-solid fa-download"></i> Generate Report
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
                        <a href="generate_report.php?type=requirements" class="download-link">
                            <i class="fa-solid fa-download"></i> Generate Report
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
                        <a href="generate_report.php?type=monthly" class="download-link">
                            <i class="fa-solid fa-download"></i> Generate Report
                        </a>
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
                            Generate a comprehensive compliance report suitable for submission to CHED. This export includes faculty qualifications, document status, and compliance percentages.
                        </p>
                        <a href="ched_export.php" class="download-link" style="padding: 12px 24px;">
                            <i class="fa-solid fa-file-pdf"></i> Generate CHED Compliance Export
                        </a>
                    </div>
                </div>
            </div>
            <div class="footer">
                <p> 2025 University of Makati - CCIS Faculty Project Management System v1.0 | <a href="#">Help Center</a> | <a href="#">Contact Support</a></p>
            </div>
        </div>
    </div>
    
    <script>
        // In a real implementation, this would include chart rendering code
        // using a library like Chart.js or D3.js
    </script>
</body>
</html>