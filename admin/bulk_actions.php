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

// Check if action and IDs are provided
if (!isset($_POST['action']) || !isset($_POST['submission_ids']) || empty($_POST['submission_ids'])) {
    $_SESSION['error_message'] = "Invalid request. Action and submission IDs are required.";
    header("Location: approvals.php");
    exit();
}

$action = $_POST['action'];
$submission_ids = $_POST['submission_ids'];
$reason = isset($_POST['reason']) ? $_POST['reason'] : "Bulk action by administrator";

// Validate action
if ($action !== 'approve' && $action !== 'reject') {
    $_SESSION['error_message'] = "Invalid action. Only 'approve' and 'reject' are allowed.";
    header("Location: approvals.php");
    exit();
}

// Process each submission
$success_count = 0;
$error_count = 0;
$processed_submissions = [];

foreach ($submission_ids as $submission_id) {
    $submission_id = intval($submission_id);

    // Get submission details
    $query = "SELECT s.*, f.first_name, f.last_name, f.email, r.requirement_name
              FROM faculty_compliance_status s
              JOIN faculty f ON s.faculty_id = f.faculty_id
              JOIN ched_compliance_requirements r ON s.requirement_id = r.requirement_id
              WHERE s.id = ? AND s.status = 'pending'";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $submission_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) == 0) {
        $error_count++;
        continue;
    }

    $submission = mysqli_fetch_assoc($result);

    // Update submission status
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';
    $admin_comments = ($action === 'approve')
        ? "Approved by " . ($_SESSION['admin_name'] ?? 'Administrator') . " on " . date('Y-m-d H:i:s') . " (Bulk action)"
        : "Rejected by " . ($_SESSION['admin_name'] ?? 'Administrator') . " on " . date('Y-m-d H:i:s') . ". Reason: " . $reason . " (Bulk action)";

    $update_query = "UPDATE faculty_compliance_status SET status = ?, updated_at = NOW(), admin_comments = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssi", $new_status, $admin_comments, $submission_id);
    $update_result = mysqli_stmt_execute($stmt);

    if ($update_result) {
        $success_count++;
        $processed_submissions[] = [
            'id' => $submission_id,
            'faculty_name' => $submission['first_name'] . ' ' . $submission['last_name'],
            'requirement' => $submission['requirement_name'],
            'email' => $submission['email']
        ];

        // Log the action
        logAction($conn, $action, $submission_id, $submission['faculty_id'], $submission['requirement_id']);
    } else {
        $error_count++;
    }
}

// Send email notifications (in a real application, you might want to batch these)
if ($action === 'approve') {
    foreach ($processed_submissions as $submission) {
        sendApprovalEmail($submission['email'], $submission['faculty_name'], $submission['requirement']);
    }
} else {
    foreach ($processed_submissions as $submission) {
        sendRejectionEmail($submission['email'], $submission['faculty_name'], $submission['requirement'], $reason);
    }
}

// Set session message
if ($success_count > 0) {
    $action_text = ($action === 'approve') ? 'approved' : 'rejected';
    $_SESSION['success_message'] = "$success_count submissions successfully $action_text.";
}

if ($error_count > 0) {
    $_SESSION['error_message'] = "$error_count submissions could not be processed.";
}

// Redirect back to approvals page
header("Location: approvals.php");
exit();

/**
 * Send approval email notification to faculty
 *
 * @param string $email Faculty email address
 * @param string $name Faculty name
 * @param string $requirement Requirement name
 * @return bool Success status
 */
function sendApprovalEmail($email, $name, $requirement)
{
    // This is a placeholder function - implement actual email sending
    // using your preferred email library or PHP's mail() function

    $subject = "CHED Compliance Requirement Approved";
    $message = "Dear $name,\n\n";
    $message .= "Your submission for the requirement '$requirement' has been approved.\n\n";
    $message .= "Thank you for your compliance.\n\n";
    $message .= "Regards,\nCCIS Faculty Project Management System";

    // Uncomment the line below to actually send emails when you have configured your mail server
    // mail($email, $subject, $message);

    return true;
}

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