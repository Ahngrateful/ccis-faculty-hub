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

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="ched_compliance_report_' . date('Y-m-d') . '.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

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

// Write the summary section
fputcsv($output, ['CHED Compliance Report - University of Makati']);
fputcsv($output, ['Generated on:', date('F d, Y')]);
fputcsv($output, []);
fputcsv($output, ['Executive Summary']);
fputcsv($output, ['Overall Compliance Rate:', number_format($compliance_rate, 2) . '%']);
fputcsv($output, ['Total Faculty:', $total_faculty]);
fputcsv($output, ['Total Requirements:', $total_requirements]);
fputcsv($output, ['Total Submissions:', $stats['total_submissions']]);
fputcsv($output, ['Approved Submissions:', $total_approved]);
fputcsv($output, ['Rejected Submissions:', $stats['total_rejected']]);
fputcsv($output, ['Pending Submissions:', $stats['total_pending']]);
fputcsv($output, []);

// Faculty Compliance Section
fputcsv($output, ['Faculty Compliance Status']);
fputcsv($output, ['Faculty ID', 'Name', 'Approved Requirements', 'Total Requirements', 'Compliance Rate']);

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

// Write faculty data
while ($faculty = mysqli_fetch_assoc($faculty_result)) {
    $faculty_compliance = ($faculty['total_requirements'] > 0) ?
        ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;

    fputcsv($output, [
        $faculty['faculty_id'],
        $faculty['first_name'] . ' ' . $faculty['last_name'],
        $faculty['approved_requirements'],
        $faculty['total_requirements'],
        number_format($faculty_compliance, 2) . '%'
    ]);
}

fputcsv($output, []);

// Requirements Compliance Section
fputcsv($output, ['Requirements Compliance Status']);
fputcsv($output, ['Requirement ID', 'Requirement Name', 'Category', 'Approved Count', 'Total Faculty', 'Compliance Rate']);

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

// Write requirements data
while ($requirement = mysqli_fetch_assoc($requirements_result)) {
    $requirement_compliance = ($requirement['total_faculty'] > 0) ?
        ($requirement['approved_count'] / $requirement['total_faculty']) * 100 : 0;

    fputcsv($output, [
        $requirement['requirement_id'],
        $requirement['requirement_name'],
        $requirement['category'],
        $requirement['approved_count'],
        $requirement['total_faculty'],
        number_format($requirement_compliance, 2) . '%'
    ]);
}

fputcsv($output, []);

// Recommendations Section
fputcsv($output, ['Recommendations']);

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

fputcsv($output, ['1. Focus on faculty members with low compliance rates:']);

if (mysqli_num_rows($low_compliance_result) > 0) {
    while ($faculty = mysqli_fetch_assoc($low_compliance_result)) {
        $faculty_compliance = ($faculty['total_requirements'] > 0) ?
            ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;

        fputcsv($output, [
            $faculty['first_name'] . ' ' . $faculty['last_name'],
            'Compliance Rate: ' . number_format($faculty_compliance, 2) . '%'
        ]);
    }
} else {
    fputcsv($output, ['All faculty members have compliance rates above 50%.']);
}

fputcsv($output, []);
fputcsv($output, ['2. Address requirements with low compliance rates:']);

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

if (mysqli_num_rows($low_req_result) > 0) {
    while ($req = mysqli_fetch_assoc($low_req_result)) {
        $req_compliance = ($req['total_faculty'] > 0) ?
            ($req['approved_count'] / $req['total_faculty']) * 100 : 0;

        fputcsv($output, [
            $req['requirement_name'],
            'Compliance Rate: ' . number_format($req_compliance, 2) . '%'
        ]);
    }
} else {
    fputcsv($output, ['All requirements have compliance rates above 50%.']);
}

fputcsv($output, []);
fputcsv($output, ['3. General recommendations:']);
fputcsv($output, ['- Conduct regular faculty orientation sessions on CHED requirements']);
fputcsv($output, ['- Provide assistance to faculty members in preparing and submitting required documents']);
fputcsv($output, ['- Implement a regular monitoring system to track compliance progress']);
fputcsv($output, ['- Recognize and reward faculty members with high compliance rates']);
fputcsv($output, ['- Establish clear deadlines for document submissions']);

// Close the file pointer
fclose($output);
exit();
