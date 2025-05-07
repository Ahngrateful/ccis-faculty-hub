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

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $report_type . '_report_' . date('Y-m-d') . '.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Function to get faculty compliance data
function getFacultyComplianceData($conn)
{
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
function getRequirementsSummaryData($conn)
{
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
function getMonthlyActivityData($conn)
{
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

// Generate the appropriate report based on type
switch ($report_type) {
    case 'faculty':
        // Faculty Compliance Report
        $faculty_data = getFacultyComplianceData($conn);

        // CSV Headers
        fputcsv($output, [
            'Faculty ID',
            'First Name',
            'Last Name',
            'Email',
            'Role',
            'Compliance Rate (%)',
            'Completed Requirements',
            'Total Requirements'
        ]);

        // CSV Data
        foreach ($faculty_data as $faculty) {
            fputcsv($output, [
                $faculty['faculty_id'],
                $faculty['first_name'],
                $faculty['last_name'],
                $faculty['email'],
                $faculty['role_name'],
                number_format($faculty['compliance_rate'], 2),
                $faculty['completed_requirements'],
                $faculty['total_requirements']
            ]);
        }
        break;

    case 'requirements':
        // Requirements Summary Report
        $requirements_data = getRequirementsSummaryData($conn);

        // CSV Headers
        fputcsv($output, [
            'Requirement ID',
            'Requirement Name',
            'Category',
            'Description',
            'Compliance Rate (%)',
            'Approved',
            'Rejected',
            'Pending',
            'Not Submitted',
            'Total Faculty'
        ]);

        // CSV Data
        foreach ($requirements_data as $requirement) {
            fputcsv($output, [
                $requirement['requirement_id'],
                $requirement['requirement_name'],
                $requirement['category'],
                $requirement['description'],
                number_format($requirement['compliance_rate'], 2),
                $requirement['approved'],
                $requirement['rejected'],
                $requirement['pending'],
                $requirement['not_submitted'],
                $requirement['total_faculty']
            ]);
        }
        break;

    case 'monthly':
        // Monthly Activity Report
        $monthly_data = getMonthlyActivityData($conn);

        // CSV Headers
        fputcsv($output, ['Month', 'Total Submissions', 'Approvals', 'Rejections', 'Pending']);

        // CSV Data
        foreach ($monthly_data as $month) {
            fputcsv($output, [
                $month['month'],
                $month['total_submissions'],
                $month['approvals'],
                $month['rejections'],
                $month['pending']
            ]);
        }
        break;
}

// Close the file pointer
fclose($output);
exit();
?>