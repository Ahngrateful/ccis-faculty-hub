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

// Include TCPDF library
require_once('../vendor/tcpdf/tcpdf.php');

// Create new PDF document
class MYPDF extends TCPDF
{
    // Page header
    public function Header()
    {
        // Logo
        $image_file = '../assets/CCIS-Logo-Official.png';
        $this->Image($image_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Set font
        $this->SetFont('helvetica', 'B', 16);

        // Title
        $this->Cell(0, 15, 'CHED Compliance Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        // Subtitle
        $this->Ln(10);
        $this->SetFont('helvetica', '', 12);
        $this->Cell(0, 15, 'University of Makati - College of Computer Science and Information Technology', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        // Date
        $this->Ln(10);
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 10, 'Generated on: ' . date('F d, Y'), 0, false, 'C', 0, '', 0, false, 'M', 'M');

        // Line
        $this->Ln(15);
        $this->Line(10, $this->GetY(), $this->getPageWidth() - 10, $this->GetY());
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('FPMS');
$pdf->SetAuthor('University of Makati');
$pdf->SetTitle('CHED Compliance Report');
$pdf->SetSubject('Faculty Compliance Status');
$pdf->SetKeywords('CHED, Compliance, Faculty, Report');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP + 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 14);

// Executive Summary
$pdf->Cell(0, 10, 'Executive Summary', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 11);

// Get compliance statistics
$stats_query = "SELECT
                COUNT(DISTINCT f.faculty_id) as total_faculty,
                COUNT(DISTINCT cr.requirement_id) as total_requirements,
                COUNT(DISTINCT fcs.id) as total_submissions,
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

$summary = "This report provides a comprehensive overview of faculty compliance with CHED requirements at the University of Makati's College of Computer Science and Information Technology. As of " . date('F d, Y') . ", the overall compliance rate is " . number_format($compliance_rate, 2) . "%.

The college has $total_faculty active faculty members who are required to comply with $total_requirements CHED requirements. Out of a total possible $total_possible requirement submissions, $total_approved have been approved, " . $stats['total_rejected'] . " have been rejected, and " . $stats['total_pending'] . " are pending review.

This report includes detailed compliance information for each faculty member and each requirement category.";

$pdf->MultiCell(0, 10, $summary, 0, 'L', 0, 1, '', '', true);

// Add a line break
$pdf->Ln(10);

// Faculty Compliance Table
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Faculty Compliance Status', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

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

// Table header
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(30, 7, 'Faculty ID', 1, 0, 'C', 1);
$pdf->Cell(60, 7, 'Name', 1, 0, 'C', 1);
$pdf->Cell(40, 7, 'Approved Requirements', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Total Requirements', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Compliance Rate', 1, 1, 'C', 1);

// Table data
$pdf->SetFont('helvetica', '', 10);
while ($faculty = mysqli_fetch_assoc($faculty_result)) {
    $faculty_compliance = ($faculty['total_requirements'] > 0) ?
        ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;

    $pdf->Cell(30, 7, $faculty['faculty_id'], 1, 0, 'C');
    $pdf->Cell(60, 7, $faculty['first_name'] . ' ' . $faculty['last_name'], 1, 0, 'L');
    $pdf->Cell(40, 7, $faculty['approved_requirements'], 1, 0, 'C');
    $pdf->Cell(30, 7, $faculty['total_requirements'], 1, 0, 'C');
    $pdf->Cell(30, 7, number_format($faculty_compliance, 2) . '%', 1, 1, 'C');
}

// Add a page break
$pdf->AddPage();

// Requirements Compliance Table
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Requirements Compliance Status', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

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

// Table header
$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 7, 'Requirement ID', 1, 0, 'C', 1);
$pdf->Cell(60, 7, 'Requirement Name', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Category', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Approved Count', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Compliance Rate', 1, 1, 'C', 1);

// Table data
$pdf->SetFont('helvetica', '', 10);
$current_category = '';
while ($requirement = mysqli_fetch_assoc($requirements_result)) {
    // Add category header if category changes
    if ($current_category != $requirement['category']) {
        $current_category = $requirement['category'];
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 10, $current_category, 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
    }

    $requirement_compliance = ($requirement['total_faculty'] > 0) ?
        ($requirement['approved_count'] / $requirement['total_faculty']) * 100 : 0;

    $pdf->Cell(40, 7, $requirement['requirement_id'], 1, 0, 'C');
    $pdf->Cell(60, 7, $requirement['requirement_name'], 1, 0, 'L');
    $pdf->Cell(30, 7, $requirement['category'], 1, 0, 'C');
    $pdf->Cell(30, 7, $requirement['approved_count'] . '/' . $requirement['total_faculty'], 1, 0, 'C');
    $pdf->Cell(30, 7, number_format($requirement_compliance, 2) . '%', 1, 1, 'C');
}

// Add a page break
$pdf->AddPage();

// Recommendations
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Recommendations', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 11);

// Get faculty with low compliance
$low_compliance_query = "SELECT f.faculty_id, f.first_name, f.last_name,
                        COUNT(DISTINCT cr.requirement_id) as total_requirements,
                        SUM(CASE WHEN fcs.status = 'approved' THEN 1 ELSE 0 END) as approved_requirements
                        FROM faculty f
                        CROSS JOIN ched_compliance_requirements cr
                        LEFT JOIN faculty_compliance_status fcs ON f.faculty_id = fcs.faculty_id AND cr.requirement_id = fcs.requirement_id
                        WHERE f.status = 'active' AND f.role_id = '1'
                        GROUP BY f.faculty_id
                        HAVING (approved_requirements / total_requirements) < 0.5
                        ORDER BY (approved_requirements / total_requirements) ASC
                        LIMIT 5";
$low_compliance_result = mysqli_query($conn, $low_compliance_query);

$recommendations = "Based on the current compliance data, the following recommendations are provided to improve the overall CHED compliance rate:

1. Focus on faculty members with low compliance rates:";

if (mysqli_num_rows($low_compliance_result) > 0) {
    while ($faculty = mysqli_fetch_assoc($low_compliance_result)) {
        $faculty_compliance = ($faculty['total_requirements'] > 0) ?
            ($faculty['approved_requirements'] / $faculty['total_requirements']) * 100 : 0;

        $recommendations .= "\n   - " . $faculty['first_name'] . ' ' . $faculty['last_name'] .
            " (Compliance Rate: " . number_format($faculty_compliance, 2) . "%)";
    }
} else {
    $recommendations .= "\n   - All faculty members have compliance rates above 50%.";
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
                 HAVING (approved_count / total_faculty) < 0.5
                 ORDER BY (approved_count / total_faculty) ASC
                 LIMIT 5";
$low_req_result = mysqli_query($conn, $low_req_query);

$recommendations .= "\n\n2. Address requirements with low compliance rates:";

if (mysqli_num_rows($low_req_result) > 0) {
    while ($req = mysqli_fetch_assoc($low_req_result)) {
        $req_compliance = ($req['total_faculty'] > 0) ?
            ($req['approved_count'] / $req['total_faculty']) * 100 : 0;

        $recommendations .= "\n   - " . $req['requirement_name'] .
            " (Compliance Rate: " . number_format($req_compliance, 2) . "%)";
    }
} else {
    $recommendations .= "\n   - All requirements have compliance rates above 50%.";
}

$recommendations .= "\n\n3. General recommendations:
   - Conduct regular faculty orientation sessions on CHED requirements
   - Provide assistance to faculty members in preparing and submitting required documents
   - Implement a regular monitoring system to track compliance progress
   - Recognize and reward faculty members with high compliance rates
   - Establish clear deadlines for document submissions";

$pdf->MultiCell(0, 10, $recommendations, 0, 'L', 0, 1, '', '', true);

// Output the PDF
$pdf->Output('ched_compliance_report_' . date('Y-m-d') . '.pdf', 'D');
?>