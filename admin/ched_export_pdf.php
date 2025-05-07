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

// Get compliance statistics
$stats_query = "SELECT 
                COUNT(DISTINCT f.faculty_id) as total_faculty,
                COUNT(DISTINCT cr.requirement_id) as total_requirements,
                COUNT(*) as total_submissions,
                SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as total_approved,
                SUM(CASE WHEN fcs.status = 'rejected' THEN 1 ELSE 0 END) as total_rejected,
                SUM(CASE WHEN fcs.status = 'pending' THEN 1 ELSE 0 END) as total_pending
                FROM faculty f
                CROSS JOIN ched_compliance_requirements cr
                LEFT JOIN faculty_compliance_status fcs ON f.faculty_id = fcs.faculty_id AND cr.requirement_id = fcs.requirement_id
                WHERE f.status = 'active' AND f.role_id = '1'";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$total_faculty = $stats['total_faculty'];
$total_requirements = $stats['total_requirements'];
$total_possible = $total_faculty * $total_requirements;
$total_approved = $stats['total_approved'];
$compliance_rate = ($total_possible > 0) ? ($total_approved / $total_possible) * 100 : 0;

// Get faculty compliance data
$faculty_query = "SELECT f.faculty_id, f.first_name, f.last_name, 
                 COUNT(DISTINCT cr.requirement_id) as total_requirements,
                 SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved_requirements
                 FROM faculty f
                 CROSS JOIN ched_compliance_requirements cr
                 LEFT JOIN faculty_compliance_status fcs ON f.faculty_id = fcs.faculty_id AND cr.requirement_id = fcs.requirement_id
                 WHERE f.status = 'active' AND f.role_id = '1'
                 GROUP BY f.faculty_id
                 ORDER BY f.last_name, f.first_name";
$faculty_result = mysqli_query($conn, $faculty_query);
$faculty_data = [];
while ($faculty = mysqli_fetch_assoc($faculty_result)) {
    $faculty_compliance = ($faculty['total_requirements'] > 0) ? 
        ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;
    $faculty['compliance_rate'] = $faculty_compliance;
    $faculty_data[] = $faculty;
}

// Get requirements compliance data
$requirements_query = "SELECT cr.requirement_id, cr.requirement_name, 'General' as category,
                      COUNT(DISTINCT f.faculty_id) as total_faculty,
                      SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved_count
                      FROM ched_compliance_requirements cr
                      CROSS JOIN faculty f
                      LEFT JOIN faculty_compliance_status fcs ON cr.requirement_id = fcs.requirement_id AND f.faculty_id = fcs.faculty_id
                      WHERE f.status = 'active' AND f.role_id = '1'
                      GROUP BY cr.requirement_id
                      ORDER BY cr.requirement_name";
$requirements_result = mysqli_query($conn, $requirements_query);
$requirements_data = [];
while ($requirement = mysqli_fetch_assoc($requirements_result)) {
    $requirement_compliance = ($requirement['total_faculty'] > 0) ? 
        ($requirement['approved_count'] / $requirement['total_faculty']) * 100 : 0;
    $requirement['compliance_rate'] = $requirement_compliance;
    $requirements_data[] = $requirement;
}

// Get faculty with low compliance
$low_compliance_query = "SELECT f.faculty_id, f.first_name, f.last_name,
                        COUNT(DISTINCT cr.requirement_id) as total_requirements,
                        SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved_requirements
                        FROM faculty f
                        CROSS JOIN ched_compliance_requirements cr
                        LEFT JOIN faculty_compliance_status fcs ON f.faculty_id = fcs.faculty_id AND cr.requirement_id = fcs.requirement_id
                        WHERE f.status = 'active' AND f.role_id = '1'
                        GROUP BY f.faculty_id
                        HAVING (SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) / COUNT(DISTINCT cr.requirement_id)) < 0.5
                        ORDER BY (SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) / COUNT(DISTINCT cr.requirement_id)) ASC
                        LIMIT 5";
$low_compliance_result = mysqli_query($conn, $low_compliance_query);
$low_compliance_data = [];
while ($faculty = mysqli_fetch_assoc($low_compliance_result)) {
    $faculty_compliance = ($faculty['total_requirements'] > 0) ? 
        ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;
    $faculty['compliance_rate'] = $faculty_compliance;
    $low_compliance_data[] = $faculty;
}

// Get requirements with low compliance
$low_req_query = "SELECT cr.requirement_id, cr.requirement_name,
                 COUNT(DISTINCT f.faculty_id) as total_faculty,
                 SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved_count
                 FROM ched_compliance_requirements cr
                 CROSS JOIN faculty f
                 LEFT JOIN faculty_compliance_status fcs ON cr.requirement_id = fcs.requirement_id AND f.faculty_id = fcs.faculty_id
                 WHERE f.status = 'active' AND f.role_id = '1'
                 GROUP BY cr.requirement_id
                 HAVING (SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) / COUNT(DISTINCT f.faculty_id)) < 0.5
                 ORDER BY (SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) / COUNT(DISTINCT f.faculty_id)) ASC
                 LIMIT 5";
$low_req_result = mysqli_query($conn, $low_req_query);
$low_req_data = [];
while ($req = mysqli_fetch_assoc($low_req_result)) {
    $req_compliance = ($req['total_faculty'] > 0) ? 
        ($req['approved_count'] / $req['total_faculty']) * 100 : 0;
    $req['compliance_rate'] = $req_compliance;
    $low_req_data[] = $req;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHED Compliance Report - <?php echo date('Y-m-d'); ?></title>
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
        
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0;
        }
        
        .stat-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            width: calc(33% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #006834;
            margin: 10px 0;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
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
        
        .recommendations {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .recommendation-item {
            margin-bottom: 15px;
            padding-left: 20px;
            position: relative;
        }
        
        .recommendation-item:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #006834;
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
    </div>
    
    <div class="container">
        <div class="header">
            <img src="../assets/CCIS-Logo-Official.png" alt="University Logo" class="logo">
            <h1>CHED COMPLIANCE REPORT</h1>
            <div class="subtitle">University of Makati - College of Computer Science and Information Technology</div>
            <div class="date">Generated on: <?php echo date('F d, Y'); ?></div>
        </div>
        
        <h2>Executive Summary</h2>
        <div class="summary-box">
            <p>This report provides a comprehensive overview of faculty compliance with CHED requirements at the University of Makati's College of Computer Science and Information Technology. As of <?php echo date('F d, Y'); ?>, the overall compliance rate is <strong><?php echo number_format($compliance_rate, 2); ?>%</strong>.</p>
            <p>The college has <?php echo $total_faculty; ?> active faculty members who are required to comply with <?php echo $total_requirements; ?> CHED requirements. Out of a total possible <?php echo $total_possible; ?> requirement submissions, <?php echo $total_approved; ?> have been approved, <?php echo $stats['total_rejected']; ?> have been rejected, and <?php echo $stats['total_pending']; ?> are pending review.</p>
            <p>This report includes detailed compliance information for each faculty member and each requirement category.</p>
        </div>
        
        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-value"><?php echo number_format($compliance_rate, 2); ?>%</div>
                <div class="stat-label">Overall Compliance Rate</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo $total_approved; ?></div>
                <div class="stat-label">Approved Requirements</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo $total_faculty; ?></div>
                <div class="stat-label">Active Faculty Members</div>
            </div>
        </div>
        
        <h2>Faculty Compliance Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Faculty ID</th>
                    <th>Name</th>
                    <th>Approved</th>
                    <th>Total</th>
                    <th>Compliance Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faculty_data as $faculty): ?>
                <tr>
                    <td><?php echo htmlspecialchars($faculty['faculty_id']); ?></td>
                    <td><?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?></td>
                    <td><?php echo $faculty['approved_requirements']; ?></td>
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
        
        <div class="page-break"></div>
        
        <h2>Requirements Compliance Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Requirement ID</th>
                    <th>Requirement Name</th>
                    <th>Category</th>
                    <th>Approved</th>
                    <th>Total Faculty</th>
                    <th>Compliance Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requirements_data as $requirement): ?>
                <tr>
                    <td><?php echo htmlspecialchars($requirement['requirement_id']); ?></td>
                    <td><?php echo htmlspecialchars($requirement['requirement_name']); ?></td>
                    <td><?php echo htmlspecialchars($requirement['category']); ?></td>
                    <td><?php echo $requirement['approved_count']; ?></td>
                    <td><?php echo $requirement['total_faculty']; ?></td>
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
        
        <div class="page-break"></div>
        
        <h2>Recommendations</h2>
        <div class="recommendations">
            <h3>1. Focus on faculty members with low compliance rates:</h3>
            <?php if (count($low_compliance_data) > 0): ?>
                <ul>
                    <?php foreach ($low_compliance_data as $faculty): ?>
                        <li>
                            <?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?> 
                            (Compliance Rate: <span class="compliance-low"><?php echo number_format($faculty['compliance_rate'], 2); ?>%</span>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>All faculty members have compliance rates above 50%.</p>
            <?php endif; ?>
            
            <h3>2. Address requirements with low compliance rates:</h3>
            <?php if (count($low_req_data) > 0): ?>
                <ul>
                    <?php foreach ($low_req_data as $req): ?>
                        <li>
                            <?php echo htmlspecialchars($req['requirement_name']); ?> 
                            (Compliance Rate: <span class="compliance-low"><?php echo number_format($req['compliance_rate'], 2); ?>%</span>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>All requirements have compliance rates above 50%.</p>
            <?php endif; ?>
            
            <h3>3. General recommendations:</h3>
            <ul>
                <li>Conduct regular faculty orientation sessions on CHED requirements</li>
                <li>Provide assistance to faculty members in preparing and submitting required documents</li>
                <li>Implement a regular monitoring system to track compliance progress</li>
                <li>Recognize and reward faculty members with high compliance rates</li>
                <li>Establish clear deadlines for document submissions</li>
            </ul>
        </div>
        
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
