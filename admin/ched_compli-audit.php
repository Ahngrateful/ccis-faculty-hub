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
    
    
    // Get compliance stats
    $total_faculty = 0;
    $full_compliance = 0;
    $partial_compliance = 0;
    $low_compliance = 0;
    
    $query = "SELECT COUNT(*) as total FROM faculty WHERE role_id = '1'";
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $total_faculty = $row['total'];
    }
    
    // Get faculty compliance data
    $faculty_compliance = [];
    $query = "SELECT 
                u.faculty_id, 
                u.first_name, 
                u.last_name,
                u.email, u.role_id,
                COUNT(DISTINCT r.requirement_id) as total_requirements,
                COUNT(DISTINCT CASE WHEN s.status = 'approved' THEN s.requirement_id END) as completed_requirements
              FROM 
                faculty u
              LEFT JOIN 
                ched_compliance_requirements r ON r.requirement_id = u.role_id
              LEFT JOIN 
                faculty_compliance_status s ON s.faculty_id = u.faculty_id AND s.requirement_id = r.requirement_id
              WHERE 
                u.role_id = '1'
              GROUP BY 
                u.faculty_id
              ORDER BY 
                (COUNT(DISTINCT CASE WHEN s.status = 'approved' THEN s.requirement_id END) / COUNT(DISTINCT r.requirement_id)) DESC";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $total_reqs = $row['total_requirements'] > 0 ? $row['total_requirements'] : 1; // Avoid division by zero
            $compliance_percentage = ($row['completed_requirements'] / $total_reqs) * 100;
            
            // Update counters
            if ($compliance_percentage >= 90) {
                $full_compliance++;
            } elseif ($compliance_percentage >= 60) {
                $partial_compliance++;
            } else {
                $low_compliance++;
            }
            
            $faculty_compliance[] = [
                'id' => $row['faculty_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'role_id' => $row['role_id'],
                'total_requirements' => $row['total_requirements'],
                'completed_requirements' => $row['completed_requirements'],
                'compliance_percentage' => $compliance_percentage
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
    <title>CHED Compliance Audit - FPMS</title>
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

        /* Card styling */
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background-color: rgba(0, 104, 52, 0.02);
        }

        .card-body {
            padding: 20px;
        }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
        }

        .stat-box-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 2rem;
            opacity: 0.1;
            color: var(--primary);
        }

        .stat-box-title {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .stat-box-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-box-description {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        /* Progress bar */
        .progress-container {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            margin: 10px 0;
        }

        .progress-bar {
            height: 8px;
            border-radius: 10px;
            transition: width 0.4s ease;
        }

        .progress-bar.high {
            background: linear-gradient(90deg, var(--secondary) 0%, #63c967 100%);
        }

        .progress-bar.medium {
            background: linear-gradient(90deg, #ffde26 0%, #ffc107 100%);
        }

        .progress-bar.low {
            background: linear-gradient(90deg, #ff9f43 0%, #ff5e57 100%);
        }

        /* Buttons */
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
            margin-top: 20px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
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

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats {
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

            .main-content {
                padding: 20px;
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
                <a href="faculty_management.php"><i class="fa-solid fa-users-gear"></i> <span>Faculty Management</span></a>
                <a href="ched_compli-audit.php" class="active"><i class="fa-solid fa-clipboard-check"></i> <span>CHED Compliance Audit</span></a>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
            </div>
        </div>
        <div class="content">
            <div class="header">
                <div class="header-left">
                    <h1 class="page-title"><i class="fa-solid fa-clipboard-check"></i> CHED Compliance Audit</h1>
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
                <h2>CHED Compliance Overview</h2>
                <div class="stats">
                    <div class="stat-box">
                        <div class="stat-box-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="stat-box-title">Total Faculty</div>
                        <div class="stat-box-value"><?php echo $total_faculty; ?></div>
                        <div class="stat-box-description">Active Faculty Members</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-icon">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <div class="stat-box-title">Full Compliance</div>
                        <div class="stat-box-value"><?php echo $full_compliance; ?></div>
                        <div class="stat-box-description">90-100% Requirement Completion</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-icon">
                            <i class="fa-solid fa-exclamation-circle"></i>
                        </div>
                        <div class="stat-box-title">Partial Compliance</div>
                        <div class="stat-box-value"><?php echo $partial_compliance; ?></div>
                        <div class="stat-box-description">60-89% Requirement Completion</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-box-icon">
                            <i class="fa-solid fa-times-circle"></i>
                        </div>
                        <div class="stat-box-title">Low Compliance</div>
                        <div class="stat-box-value"><?php echo $low_compliance; ?></div>
                        <div class="stat-box-description">Below 60% Requirement Completion</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Faculty Compliance Status</h3>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Faculty Name</th>
                                    <th>Compliance (%)</th>
                                    <th>Requirements</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faculty_compliance as $faculty): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($faculty['name']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($faculty['role_id']); ?></small>
                                    </td>
                                    <td><?php echo number_format($faculty['compliance_percentage'], 1); ?>%</td>
                                    <td><?php echo $faculty['completed_requirements']; ?> / <?php echo $faculty['total_requirements']; ?></td>
                                    <td>
                                        <div class="progress-container">
                                            <div class="progress-bar <?php 
                                                if ($faculty['compliance_percentage'] >= 90) echo 'high';
                                                else if ($faculty['compliance_percentage'] >= 60) echo 'medium';
                                                else echo 'low';
                                            ?>" style="width: <?php echo $faculty['compliance_percentage']; ?>%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="faculty_details.php?id=<?php echo $faculty['id']; ?>" style="color: var(--primary); text-decoration: none;">
                                            <i class="fa-solid fa-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <button onclick="exportReport()">
                            <i class="fa-solid fa-file-export"></i> Export Compliance Report
                        </button>
                    </div>
                </div>
            </div>
            <div class="footer">
                <p> 2025 University of Makati - CCIS Faculty Project Management System v1.0 | <a href="#">Help Center</a> | <a href="#">Contact Support</a></p>
            </div>
        </div>
    </div>
    
    <script>
        function exportReport() {
            alert('Generating CHED compliance report. The file will be downloaded shortly.');
            // In a real implementation, this would trigger a PHP script to generate a CSV or PDF report
            // window.location.href = 'export_compliance.php';
        }
    </script>
</body>
</html>