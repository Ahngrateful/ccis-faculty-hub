<?php
// Start session
session_start();

// Database connection
require_once("dbconn.php");

// Check if user is logged in
if (!isset($_SESSION['faculty_logged_in']) || $_SESSION['faculty_logged_in'] !== true) {
  header("Location: login.php");
  exit();
}

$faculty_id = $_SESSION['faculty_id']; // assuming faculty_id is stored in session

// Count pending submissions
$query = "SELECT COUNT(*) as total FROM faculty_compliance_status WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
$pending_submissions = 0;
if ($result && $row = mysqli_fetch_assoc($result)) {
  $pending_submissions = $row['total'];
}

// Get last updated date and count of records for the logged-in faculty
$profile_query = "SELECT MAX(updated_at) as last_updated, COUNT(*) as record_count 
                  FROM faculty_compliance_status 
                  WHERE faculty_id = ?";
$stmt = mysqli_prepare($conn, $profile_query);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $last_updated, $record_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Optional: format the date
$last_updated_display = $last_updated ? date("F j, Y, g:i a", strtotime($last_updated)) : "Never Updated";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Faculty Dashboard - FPMS</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    :root {
      --primary-color: #006834;
      --secondary-color: #75d979;
      --accent-color: #ffde26;
      --light-gray: #f9f9f9;
      --medium-gray: #eaeaea;
      --dark-gray: #555;
      --error-color: #f44336;
      --success-color: #4caf50;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: var(--light-gray);
      margin: 0;
      padding: 0;
      color: #333;
      line-height: 1.6;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: var(--primary-color);
      color: white;
      padding: 20px 0;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 10;
    }

    .sidebar-header {
      padding: 0 20px 20px;
      border-bottom: 1px solid var(--secondary-color);
      margin-bottom: 20px;
    }

    .sidebar h3 {
      color: var(--accent-color);
      margin: 0;
      font-size: 1.2rem;
    }

    .nav-menu {
      padding: 0 15px;
    }

    .nav-menu a {
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 12px 15px;
      margin: 5px 0;
      border-radius: 4px;
      transition: all 0.3s ease;
      background: transparent;
      font-size: 15px;
    }

    .nav-menu a:hover {
      background-color: rgba(117, 217, 121, 0.2);
    }

    .nav-menu a.active {
      background-color: var(--secondary-color);
      color: var(--primary-color);
      font-weight: 600;
    }

    .nav-menu i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }

    .content {
      flex: 1;
      padding: 0;
      background-color: #ffffff;
      display: flex;
      flex-direction: column;
    }

    .header {
      background-color: #ffffff;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--medium-gray);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .user-info {
      font-weight: 500;
      color: var(--primary-color);
    }

    .main-content {
      padding: 30px;
      flex: 1;
    }

    h2 {
      color: var(--primary-color);
      border-bottom: 2px solid var(--secondary-color);
      padding-bottom: 10px;
      margin-top: 0;
    }

    .stats {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-box {
      padding: 25px;
      flex: 1;
      text-align: center;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s;
    }

    .stat-box:hover {
      transform: translateY(-5px);
    }

    .stat-box-title {
      font-size: 14px;
      color: var(--dark-gray);
      margin-bottom: 10px;
    }

    .stat-box-value {
      font-size: 24px;
      font-weight: bold;
      color: var(--primary-color);
    }

    .buttons {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }

    button {
      background-color: var(--primary-color);
      color: white;
      padding: 12px 24px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
      font-weight: 500;
      transition: all 0.3s;
    }

    button:hover {
      background-color: var(--secondary-color);
      color: var(--primary-color);
    }

    .alert {
      background-color: var(--accent-color);
      color: var(--primary-color);
      padding: 15px;
      text-align: center;
      border-radius: 8px;
      font-weight: 500;
      margin-top: 20px;
    }

    /* Footer */
    .footer {
      background-color: #f1f1f1;
      padding: 15px;
      text-align: center;
      font-size: 14px;
      color: var(--dark-gray);
      border-top: 1px solid #ddd;
      margin-top: auto;
    }

    .footer a {
      color: var(--primary-color);
      margin-left: 10px;
      text-decoration: none;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
      }

      .nav-menu {
        display: flex;
        overflow-x: auto;
        padding: 0 10px;
      }

      .nav-menu a {
        white-space: nowrap;
      }

      .sidebar-header {
        display: none;
      }

      .stats {
        flex-direction: column;
      }

      .buttons {
        flex-direction: column;
      }
    }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #006834;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
      transition: background-color 0.3s ease;
    }

    .btn i {
      margin-right: 8px;
    }

    .btn:hover {
      background-color: rgb(4, 186, 95);
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="sidebar">
      <div class="sidebar-header">
        <h3>FPMS - CCIS</h3>
      </div>
      <nav class="nav-menu">
        <a href="#" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="credentials.php"><i class="fa-solid fa-scroll"></i> Credentials</a>
        <a href="documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a>
        <a href="reminders.php"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <a href="logout.php"><i class="fa-solid fa-door-open"></i> Logout</a>
      </nav>
    </div>

    <div class="content">
      <div class="header">
        <div class="user-info">
          <i class="fa-solid fa-circle-user"></i>
          <span>Welcome, Prof. Sharleen Olaguir - Faculty</span>
        </div>
      </div>

      <div class="main-content">
        <h2>Faculty Dashboard</h2>

        <div class="stats">
          <div class="stat-box">
            <div class="stat-box-title"><i class="fa-solid fa-calendar-check"></i> Profile Last Updated</div>
            <div class="stat-box-value"><?php echo $last_updated_display; ?></div>
          </div>
          <div class="stat-box">
            <div class="stat-box-title"><i class="fa-solid fa-database"></i> Your Profile Records</div>
            <div class="stat-box-value"><?php echo $record_count; ?></div>
          </div>
          <div class="stat-box">
            <div class="stat-box-title"><i class="fa-solid fa-hourglass-half"></i> Pending Credentials</div>
            <div class="stat-box-value"><?php echo $pending_submissions; ?></div>
          </div>
        </div>

        <div class="buttons">
          <a href="profile.php" class="btn">
            <i class="fa-solid fa-user-pen"></i> Update Profile for CHED
          </a>
          <a href="credentials.php" class="btn">
            <i class="fa-solid fa-file-circle-plus"></i> Add Credential
          </a>
          <a href="documents.php" class="btn">
            <i class="fa-solid fa-upload"></i> Upload Document
          </a>
        </div>

        <div class="alert">
          <strong><i class="fa-solid fa-circle-exclamation"></i> Important Note:</strong>
          Ensure your profile is CHED-compliant by 2025-06-01
        </div>
      </div>

      <div class="footer">
        © 2025 University of Makati FPMS v1.0 |
        <a href="#">Help</a> |
        <a href="#">Contact Support</a>
      </div>
    </div>
  </div>
</body>

</html>