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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No submission ID provided.";
    header("Location: approvals.php");
    exit();
}

$submission_id = mysqli_real_escape_string($conn, $_GET['id']);
$reason = isset($_GET['reason']) ? mysqli_real_escape_string($conn, $_GET['reason']) : "No reason provided";

// Get submission details before updating
$query = "SELECT s.*, f.first_name, f.last_name, f.email, r.requirement_name
          FROM faculty_compliance_status s
          JOIN faculty f ON s.faculty_id = f.faculty_id
          JOIN ched_compliance_requirements r ON s.requirement_id = r.requirement_id
          WHERE s.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $submission_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error_message'] = "Submission not found.";
    header("Location: approvals.php");
    exit();
}

$submission = mysqli_fetch_assoc($result);

// Check if submission is already processed
if ($submission['status'] !== 'pending') {
    $_SESSION['error_message'] = "This submission has already been processed.";
    header("Location: approvals.php");
    exit();
}

// Update submission status to rejected
$update_query = "UPDATE faculty_compliance_status SET status = 'rejected', updated_at = NOW(), admin_comments = ? WHERE id = ?";
$admin_comments = "Rejected by " . ($_SESSION['admin_name'] ?? 'Administrator') . " on " . date('Y-m-d H:i:s') . ". Reason: " . $reason;

$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "si", $admin_comments, $submission_id);
$update_result = mysqli_stmt_execute($stmt);

if ($update_result) {
    // Send email notification to faculty
    $faculty_email = $submission['email'];
    $faculty_name = $submission['first_name'] . ' ' . $submission['last_name'];
    $requirement_name = $submission['requirement_name'];

    // Send email notification (this is a placeholder - implement actual email sending)
    sendRejectionEmail($faculty_email, $faculty_name, $requirement_name, $reason);

    // Log the rejection action
    logAction($conn, 'reject', $submission_id, $submission['faculty_id'], $submission['requirement_id']);

    $_SESSION['success_message'] = "Submission rejected successfully.";
} else {
    $_SESSION['error_message'] = "Error rejecting submission: " . mysqli_error($conn);
}

header("Location: approvals.php");
exit();

/**
 * Send rejection email notification to faculty
 *
 * @param string $email Faculty email address
 * @param string $name Faculty name
 * @param string $requirement Requirement name
 * @param string $reason Rejection reason
 * @return bool Success status
 */
function sendRejectionEmail($email, $name, $requirement, $reason)
{
    // This is a placeholder function - implement actual email sending
    // using your preferred email library or PHP's mail() function

    $subject = "CHED Compliance Requirement Rejected";
    $message = "Dear $name,\n\n";
    $message .= "Your submission for the requirement '$requirement' has been rejected.\n\n";
    $message .= "Reason for rejection: $reason\n\n";
    $message .= "Please review and resubmit your document.\n\n";
    $message .= "Regards,\nCCIS Faculty Project Management System";

    // Uncomment the line below to actually send emails when you have configured your mail server
    // mail($email, $subject, $message);

    return true;
}

/**
 * Log approval/rejection action
 *
 * @param mysqli $conn Database connection
 * @param string $action Action type (approve/reject)
 * @param int $submission_id Submission ID
 * @param string $faculty_id Faculty ID
 * @param int $requirement_id Requirement ID
 * @return bool Success status
 */
function logAction($conn, $action, $submission_id, $faculty_id, $requirement_id)
{
    $admin_id = $_SESSION['admin_id'] ?? 0;
    $action_type = ($action == 'approve') ? 'submission_approved' : 'submission_rejected';

    $query = "INSERT INTO activity_log (admin_id, faculty_id, action_type, related_id, requirement_id, created_at)
              VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issii", $admin_id, $faculty_id, $action_type, $submission_id, $requirement_id);

    return mysqli_stmt_execute($stmt);
}
?>