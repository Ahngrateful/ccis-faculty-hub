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


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CHED Compliance - FPMS</title>
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
      --warning-color: #ff9800;
      --info-color: #2196f3;
      --success-color: #4caf50;
      --pink-color: #e91e63;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html,
    body {
      height: 100%;
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: var(--light-gray);
      color: #333;
      line-height: 1.6;
      overflow: hidden;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    /* Fixed Sidebar */
    .sidebar {
      width: 250px;
      background-color: var(--primary-color);
      color: white;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      position: fixed;
      height: 100vh;
      display: flex;
      flex-direction: column;
      z-index: 10;
    }

    .sidebar-header {
      padding: 20px;
      border-bottom: 1px solid var(--secondary-color);
    }

    .sidebar h3 {
      color: var(--accent-color);
      margin: 0;
      font-size: 1.2rem;
    }

    .nav-menu {
      padding: 15px;
      overflow-y: auto;
      flex-grow: 1;
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

    /* Scrollable Content Area */
    .content {
      flex: 1;
      margin-left: 250px;
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      background-color: #ffffff;
    }

    .header {
      background-color: #ffffff;
      padding: 15px 30px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      border-bottom: 1px solid var(--medium-gray);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 5;
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
      margin-bottom: 25px;
    }

    .status {
      background-color: #fff9e6;
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 8px;
      border-left: 4px solid var(--accent-color);
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }

    .progress-container {
      height: 20px;
      background-color: var(--light-gray);
      border-radius: 10px;
      margin: 15px 0;
      overflow: hidden;
    }

    .progress-bar {
      height: 100%;
      width: 75%;
      background-color: var(--primary-color);
      border-radius: 10px;
      transition: width 0.5s ease;
    }

    .deadline {
      color: var(--error-color);
      font-weight: bold;
    }

    .summary-boxes {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }

    .summary-box {
      flex: 1;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      text-align: center;
      transition: transform 0.3s;
    }

    .summary-box:hover {
      transform: translateY(-5px);
    }

    .summary-box-title {
      font-size: 14px;
      color: var(--dark-gray);
      margin-bottom: 10px;
    }

    .summary-box-value {
      font-size: 24px;
      font-weight: bold;
      color: var(--primary-color);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      margin-top: 20px;
    }

    th {
      background-color: var(--primary-color);
      color: white;
      padding: 15px;
      text-align: left;
    }

    td {
      border-bottom: 1px solid var(--medium-gray);
      padding: 15px;
      text-align: left;
    }

    tr:hover {
      background-color: var(--light-gray);
    }

    .status-approved {
      color: var(--success-color);
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .status-pending {
      color: var(--warning-color);
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .status-missing {
      color: var(--error-color);
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .status-returned {
      color: var(--pink-color);
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .action-button {
      display: inline-flex;
      align-items: center;
      padding: 8px 15px;
      background-color: var(--primary-color);
      color: white;
      border-radius: 4px;
      transition: all 0.3s;
      text-decoration: none;
      font-weight: 500;
    }

    .action-button:hover {
      background-color: var(--secondary-color);
      color: var(--primary-color);
    }

    .action-button i {
      margin-right: 8px;
    }

    .action-buttons {
      margin-top: 30px;
      display: flex;
      gap: 15px;
    }

    /* Footer */
    .footer {
      background-color: #f1f1f1;
      padding: 15px;
      text-align: center;
      font-size: 14px;
      color: var(--dark-gray);
      border-top: 1px solid #ddd;
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
        height: auto;
        position: relative;
      }

      .nav-menu {
        display: flex;
        overflow-x: auto;
        padding: 0 10px;
        flex-wrap: nowrap;
      }

      .nav-menu a {
        white-space: nowrap;
      }

      .sidebar-header {
        display: none;
      }

      .content {
        margin-left: 0;
      }

      .summary-boxes {
        flex-direction: column;
      }

      table {
        display: block;
        overflow-x: auto;
      }

      .action-buttons {
        flex-direction: column;
        gap: 10px;
      }
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
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="credentials.php"><i class="fa-solid fa-scroll"></i> Credentials</a>
        <a href="documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a>
        <a href="reminders.php"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php" class="active"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <form action="{{ route('logout') }}" method="POST">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <button
            type="submit"
            style="
                background: none;
                border: none;
                padding: 0;
                text-align: left;
                width: 100%;
              ">
            <a href="#"><i class="fa-solid fa-door-open"></i> Logout</a>
          </button>
        </form>
      </nav>
    </div>

    <div class="content">
      <div class="header">
        <div class="user-info">
          <i class="fa-solid fa-circle-user"></i> Welcome, Prof. Sharleen
          Olaguir! - Faculty
        </div>
      </div>

      <div class="main-content">
        <h2>CHED Compliance Status</h2>

        <div class="status">
          <h3 style="margin-top: 0">
            <i class="fa-solid fa-chart-line"></i> Overall Compliance: 75%
          </h3>
          <div class="progress-container">
            <div class="progress-bar"></div>
          </div>
          <p>
            <i class="fa-solid fa-calendar-days"></i> Action Required by
            <span class="deadline">2025-06-01</span>
          </p>
        </div>

        <div class="summary-boxes">
          <div class="summary-box">
            <div class="summary-box-title">
              <i class="fa-solid fa-circle-check"></i> Approved Items
            </div>
            <div class="summary-box-value">1</div>
          </div>
          <div class="summary-box">
            <div class="summary-box-title">
              <i class="fa-solid fa-clock"></i> Pending Items
            </div>
            <div class="summary-box-value">1</div>
          </div>
          <div class="summary-box">
            <div class="summary-box-title">
              <i class="fa-solid fa-circle-xmark"></i> Missing Items
            </div>
            <div class="summary-box-value">1</div>
          </div>
          <div class="summary-box">
            <div class="summary-box-title">
              <i class="fa-solid fa-rotate-left"></i> Returned Items
            </div>
            <div class="summary-box-value">1</div>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Requirement</th>
              <th>Status</th>
              <th>Last Updated</th>
              <th>Notes</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Personal Info</td>
              <td>
                <span class="status-pending"><i class="fa-solid fa-clock"></i> Submitted - Pending
                  Approval</span>
              </td>
              <td>2025-04-15</td>
              <td>Awaiting admin verification</td>
              <td>
                <a href="profile.php" class="action-button"><i class="fa-solid fa-eye"></i> View</a>
              </td>
            </tr>
            <tr>
              <td>Educational Background</td>
              <td>
                <span class="status-approved"><i class="fa-solid fa-circle-check"></i> Approved</span>
              </td>
              <td>2025-03-20</td>
              <td>All requirements met</td>
              <td>
                <a href="profile.php" class="action-button"><i class="fa-solid fa-eye"></i> View</a>
              </td>
            </tr>
            <tr>
              <td>Professional Licenses</td>
              <td>
                <span class="status-missing"><i class="fa-solid fa-circle-xmark"></i> Missing</span>
              </td>
              <td>-</td>
              <td>Required for CHED compliance</td>
              <td>
                <a href="credentials.php" class="action-button"><i class="fa-solid fa-plus"></i> Add</a>
              </td>
            </tr>
            <tr>
              <td>Service Record</td>
              <td>
                <span class="status-returned"><i class="fa-solid fa-rotate-left"></i> Submitted -
                  Returned</span>
              </td>
              <td>2025-04-10</td>
              <td>Needs additional documentation</td>
              <td>
                <a href="documents.php" class="action-button"><i class="fa-solid fa-pen-to-square"></i> Revise</a>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="action-buttons">
          <a href="reminders.html" class="action-button"><i class="fa-solid fa-bell"></i> View All Reminders</a>
          <a href="#" class="action-button"><i class="fa-solid fa-file-arrow-down"></i> Download Compliance
            Report</a>
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