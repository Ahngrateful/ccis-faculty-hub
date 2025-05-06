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


// Get stats
$total_faculty = 0;
$total_submissions = 0;
$pending_submissions = 0;
$recent_activities = [];

// Count total faculty
$query = "SELECT COUNT(*) as total FROM faculty WHERE role_id = '2'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_faculty = $row['total'];
}

// Count total submissions
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $total_submissions = $row['total'];
}

// Count pending submissions
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
  $pending_submissions = $row['total'];
}

// Get recent activities
$query = "SELECT 
            s.faculty_id, s.updated_at, s.status,
            u.first_name, u.last_name,
            r.requirement_name
          FROM 
            faculty_compliance_status s
          JOIN 
            faculty u ON s.faculty_id = u.faculty_id
          JOIN 
            ched_compliance_requirements r ON s.requirement_id = r.requirement_id
          ORDER BY 
            s.updated_at DESC
          LIMIT 5";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_activities[] = [
            'id' => $row['faculty_id'],
            'faculty_name' => $row['first_name'] . ' ' . $row['last_name'],
            'requirement' => $row['requirement_name'],
            'status' => $row['status'],
            'date' => $row['updated_at'],
            //'comments' => $row['admin_comments']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Admin Dashboard - FPMS</title>
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

        /* Notification Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        /* Dashboard Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-box {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-box-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.5rem;
            color: rgba(0, 104, 52, 0.1);
        }

        .stat-box-title {
            font-size: 1rem;
            color: var(--text-light);
            margin-bottom: 12px;
        }

        .stat-box-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-info {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            padding: 4px 10px;
            border-radius: 20px;
            margin-top: 10px;
        }

        .stat-change.positive {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .stat-change.negative {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
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

        /* Actions */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-button {
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: var(--white);
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--text-dark);
            border: none;
        }

        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .action-button i {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            color: white;
        }

        .action-icon-green {
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
        }

        .action-icon-blue {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .action-icon-orange {
            background: linear-gradient(135deg, #f39c12 0%, #d35400 100%);
        }

        .action-button-text {
            display: flex;
            flex-direction: column;
        }

        .action-button-title {
            font-weight: 600;
            font-size: 1rem;
        }

        .action-button-description {
            font-size: 0.85rem;
            color: var(--text-light);
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

        /* Status */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-approved {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .status-pending {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .status-rejected {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
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
            .stats {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
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
            <a href="dashboard.php" class="active"><i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span></a>
            <a href="approvals.php"><i class="fa-solid fa-check-to-slot"></i> <span>Approvals</span></a>
            <a href="reports.php"><i class="fa-solid fa-chart-pie"></i> <span>Reports</span></a>
            <a href="faculty_management.php"><i class="fa-solid fa-users-gear"></i> <span>Faculty Management</span></a>
            <a href="ched_compli-audit.php"><i class="fa-solid fa-clipboard-check"></i> <span>CHED Compliance Audit</span></a>
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </div>
    </div>

    <div class="content">
        <div class="header">
            <div class="header-left">
                <h1 class="page-title"><i class="fa-solid fa-gauge-high"></i> Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="notification-bell">
                    <i class="fa-solid fa-bell"></i>
                    <div class="notification-badge">3</div>
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
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-box-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-box-title">Faculty Members</div>
                    <div class="stat-box-value"><?php echo $total_faculty; ?></div>
                    <div class="stat-info">Total registered faculty</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-icon">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <div class="stat-box-title">Total Submissions</div>
                    <div class="stat-box-value"><?php echo $total_submissions; ?></div>
                    <div class="stat-info">Across all requirements</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-icon">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div class="stat-box-title">Pending Review</div>
                    <div class="stat-box-value"><?php echo $pending_submissions; ?></div>
                    <div class="stat-info">Submissions awaiting approval</div>
                    <?php if ($pending_submissions > 0): ?>
                    <div class="stat-change positive">
                        <i class="fa-solid fa-arrow-up"></i> <?php echo $pending_submissions; ?> new
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="action-buttons">
                <a href="approvals.php" class="action-button">
                    <i class="fa-solid fa-clipboard-check action-icon-green"></i>
                    <div class="action-button-text">
                        <div class="action-button-title">Review Submissions</div>
                        <div class="action-button-description">Approve or reject pending submissions</div>
                    </div>
                </a>
                <a href="faculty_management.php" class="action-button">
                    <i class="fa-solid fa-user-plus action-icon-blue"></i>
                    <div class="action-button-text">
                        <div class="action-button-title">Manage Faculty</div>
                        <div class="action-button-description">Add, edit or remove faculty accounts</div>
                    </div>
                </a>
                <a href="reports.php" class="action-button">
                    <i class="fa-solid fa-chart-column action-icon-orange"></i>
                    <div class="action-button-text">
                        <div class="action-button-title">Generate Reports</div>
                        <div class="action-button-description">Create and export compliance reports</div>
                    </div>
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        Recent Activities
                    </h2>
                    <a href="approvals.php" style="color: var(--primary); text-decoration: none; font-size: 0.9rem;">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Requirement</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_activities)): ?>
                                    <?php foreach($recent_activities as $activity): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($activity['faculty_name']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['requirement']); ?></td>
                                            <td><?php echo date('M d, Y h:ia', strtotime($activity['date'])); ?></td>
                                            <td>
                                                <span class="status status-<?php echo $activity['status']; ?>">
                                                    <i class="fa-solid fa-<?php 
                                                        if ($activity['status'] == 'approved') echo 'check-circle';
                                                        elseif ($activity['status'] == 'rejected') echo 'times-circle';
                                                        else echo 'clock';
                                                    ?>"></i>
                                                    <?php echo ucfirst($activity['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No recent activities found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p> 2025 University of Makati - CCIS Faculty Project Management System v1.0 | <a href="#">Help Center</a> | <a href="#">Contact Support</a></p>
        </div>
    </div>
</div>
</body>
</html>