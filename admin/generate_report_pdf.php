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

// Check if report type is specified
if (!isset($_GET['type'])) {
    header("Location: reports.php");
    exit();
}

$report_type = $_GET['type'];
$valid_types = ['faculty', 'requirements', 'monthly'];

if (!in_array($report_type, $valid_types)) {
    header("Location: reports.php");
    exit();
}

// Function to get faculty compliance data
function getFacultyComplianceData($conn) {
    // Get all faculty members
    $faculty_query = "SELECT f.faculty_id, f.first_name, f.last_name, f.email, r.role_name 
                     FROM faculty f 
                     JOIN roles r ON f.role_id = r.roles_id 
                     WHERE f.status = 'active'
                     ORDER BY f.last_name, f.first_name";
    $faculty_result = mysqli_query($conn, $faculty_query);
    
    if (!$faculty_result) {
        return [];
    }
    
    $faculty_data = [];
    
    while ($faculty = mysqli_fetch_assoc($faculty_result)) {
        // Get compliance data for this faculty
        $compliance_query = "SELECT cr.requirement_name, fcs.status, fcs.created_at, fcs.updated_at 
                            FROM ched_compliance_requirements cr 
                            LEFT JOIN faculty_compliance_status fcs ON cr.requirement_id = fcs.requirement_id 
                            AND fcs.faculty_id = '{$faculty['faculty_id']}'
                            ORDER BY cr.requirement_name";
        $compliance_result = mysqli_query($conn, $compliance_query);
        
        if (!$compliance_result) {
            continue;
        }
        
        $requirements = [];
        $total_requirements = 0;
        $completed_requirements = 0;
        
        while ($requirement = mysqli_fetch_assoc($compliance_result)) {
            $requirements[] = $requirement;
            $total_requirements++;
            
            if ($requirement['status'] == 'approved') {
                $completed_requirements++;
            }
        }
        
        $compliance_rate = ($total_requirements > 0) ? ($completed_requirements / $total_requirements) * 100 : 0;
        
        $faculty['requirements'] = $requirements;
        $faculty['compliance_rate'] = $compliance_rate;
        $faculty['total_requirements'] = $total_requirements;
        $faculty['completed_requirements'] = $completed_requirements;
        
        $faculty_data[] = $faculty;
    }
    
    return $faculty_data;
}

// Function to get requirements summary data
function getRequirementsSummaryData($conn) {
    // Get all requirements
    $requirements_query = "SELECT cr.requirement_id, cr.requirement_name, cr.description, 'General' as category
                          FROM ched_compliance_requirements cr
                          ORDER BY cr.requirement_name";
    $requirements_result = mysqli_query($conn, $requirements_query);
    
    if (!$requirements_result) {
        return [];
    }
    
    $requirements_data = [];
    
    while ($requirement = mysqli_fetch_assoc($requirements_result)) {
        // Get statistics for this requirement
        $stats_query = "SELECT 
                        COUNT(*) as total_faculty,
                        SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN fcs.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN fcs.status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN fcs.status IS NULL THEN 1 ELSE 0 END) as not_submitted
                        FROM faculty f
                        LEFT JOIN faculty_compliance_status fcs ON f.faculty_id = fcs.faculty_id 
                        AND fcs.requirement_id = '{$requirement['requirement_id']}'
                        WHERE f.status = 'active' AND f.role_id = '1'";
        $stats_result = mysqli_query($conn, $stats_query);
        
        if (!$stats_result) {
            continue;
        }
        
        $stats = mysqli_fetch_assoc($stats_result);
        $requirement = array_merge($requirement, $stats);
        
        // Calculate compliance rate
        $requirement['compliance_rate'] = ($stats['total_faculty'] > 0) ? 
            ($stats['approved'] / $stats['total_faculty']) * 100 : 0;
        
        $requirements_data[] = $requirement;
    }
    
    return $requirements_data;
}

// Function to get monthly activity data
function getMonthlyActivityData($conn) {
    // Get data for the last 12 months
    $monthly_data = [];
    
    for ($i = 0; $i < 12; $i++) {
        $month = date('Y-m', strtotime("-$i months"));
        $month_start = $month . '-01';
        $month_end = date('Y-m-t', strtotime($month_start));
        
        $stats_query = "SELECT 
                        COUNT(*) as total_submissions,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approvals,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejections,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                        FROM faculty_compliance_status
                        WHERE created_at BETWEEN '$month_start' AND '$month_end'";
        $stats_result = mysqli_query($conn, $stats_query);
        
        if (!$stats_result) {
            continue;
        }
        
        $stats = mysqli_fetch_assoc($stats_result);
        $stats['month'] = date('F Y', strtotime($month_start));
        $stats['month_code'] = $month;
        
        $monthly_data[] = $stats;
    }
    
    // Reverse to get chronological order
    return array_reverse($monthly_data);
}

// Get the appropriate data based on report type
switch ($report_type) {
    case 'faculty':
        $title = "Faculty Compliance Report";
        $data = getFacultyComplianceData($conn);
        break;
        
    case 'requirements':
        $title = "Requirements Summary Report";
        $data = getRequirementsSummaryData($conn);
        break;
        
    case 'monthly':
        $title = "Monthly Activity Report";
        $data = getMonthlyActivityData($conn);
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - <?php echo date('Y-m-d'); ?></title>
    <style>
        @media print {
            @page {
                size: letter portrait;
                margin: 0.5in;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #006834;
            padding-bottom: 20px;
        }
        
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        
        h1 {
            color: #006834;
            margin: 0;
            font-size: 24px;
        }
        
        h2 {
            color: #006834;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-top: 30px;
            font-size: 20px;
        }
        
        h3 {
            color: #006834;
            margin-top: 25px;
            font-size: 18px;
        }
        
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-top: 5px;
        }
        
        .date {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
        
        .summary-box {
            background-color: #f5f5f5;
            border-left: 4px solid #006834;
            padding: 15px;
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th {
            background-color: #006834;
            color: white;
            padding: 10px;
            text-align: left;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        
        .print-button {
            background-color: #006834;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .print-button:hover {
            background-color: #005229;
        }
        
        .compliance-high {
            color: #28a745;
        }
        
        .compliance-medium {
            color: #ffc107;
        }
        
        .compliance-low {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="print-button" onclick="window.print()">Print Report</button>
        <button class="print-button" onclick="window.location.href='reports.php'" style="background-color: #6c757d;">Back to Reports</button>
    </div>
    
    <div class="container">
        <div class="header">
            <img src="../assets/CCIS-Logo-Official.png" alt="University Logo" class="logo">
            <h1><?php echo strtoupper($title); ?></h1>
            <div class="subtitle">University of Makati - College of Computer Science and Information Technology</div>
            <div class="date">Generated on: <?php echo date('F d, Y'); ?></div>
        </div>
        
        <?php if ($report_type == 'faculty'): ?>
            <div class="summary-box">
                <p>This report provides detailed information about the compliance status of each faculty member with CHED requirements. The report includes faculty information, compliance rates, and the number of completed requirements.</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Faculty ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Completed</th>
                        <th>Total</th>
                        <th>Compliance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $faculty): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($faculty['faculty_id']); ?></td>
                        <td><?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($faculty['email']); ?></td>
                        <td><?php echo htmlspecialchars($faculty['role_name']); ?></td>
                        <td><?php echo $faculty['completed_requirements']; ?></td>
                        <td><?php echo $faculty['total_requirements']; ?></td>
                        <td>
                            <?php 
                            $compliance_class = '';
                            if ($faculty['compliance_rate'] >= 75) {
                                $compliance_class = 'compliance-high';
                            } elseif ($faculty['compliance_rate'] >= 50) {
                                $compliance_class = 'compliance-medium';
                            } else {
                                $compliance_class = 'compliance-low';
                            }
                            ?>
                            <span class="<?php echo $compliance_class; ?>"><?php echo number_format($faculty['compliance_rate'], 2); ?>%</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        <?php elseif ($report_type == 'requirements'): ?>
            <div class="summary-box">
                <p>This report provides a summary of all CHED requirements and their compliance status across faculty members. The report includes requirement details, compliance rates, and submission statistics.</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Requirement ID</th>
                        <th>Requirement Name</th>
                        <th>Category</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Pending</th>
                        <th>Not Submitted</th>
                        <th>Compliance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $requirement): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($requirement['requirement_id']); ?></td>
                        <td><?php echo htmlspecialchars($requirement['requirement_name']); ?></td>
                        <td><?php echo htmlspecialchars($requirement['category']); ?></td>
                        <td><?php echo $requirement['approved']; ?></td>
                        <td><?php echo $requirement['rejected']; ?></td>
                        <td><?php echo $requirement['pending']; ?></td>
                        <td><?php echo $requirement['not_submitted']; ?></td>
                        <td>
                            <?php 
                            $compliance_class = '';
                            if ($requirement['compliance_rate'] >= 75) {
                                $compliance_class = 'compliance-high';
                            } elseif ($requirement['compliance_rate'] >= 50) {
                                $compliance_class = 'compliance-medium';
                            } else {
                                $compliance_class = 'compliance-low';
                            }
                            ?>
                            <span class="<?php echo $compliance_class; ?>"><?php echo number_format($requirement['compliance_rate'], 2); ?>%</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        <?php elseif ($report_type == 'monthly'): ?>
            <div class="summary-box">
                <p>This report provides a monthly breakdown of faculty compliance activity. The report includes submission counts, approvals, rejections, and pending reviews for each month.</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Submissions</th>
                        <th>Approvals</th>
                        <th>Rejections</th>
                        <th>Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $month): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($month['month']); ?></td>
                        <td><?php echo $month['total_submissions']; ?></td>
                        <td><?php echo $month['approvals']; ?></td>
                        <td><?php echo $month['rejections']; ?></td>
                        <td><?php echo $month['pending']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="footer">
            <p>© <?php echo date('Y'); ?> University of Makati - CCIS Faculty Project Management System v1.0</p>
            <p>This report is confidential and intended for internal use only.</p>
        </div>
    </div>
    
    <script>
        // Auto-print when the page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
