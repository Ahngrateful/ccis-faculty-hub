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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Additional compliance-specific styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* CHED compliance-specific styles */
    .compliance-header {
      background: linear-gradient(120deg, var(--primary-light), var(--primary-color));
      border-radius: var(--border-radius-md);
      color: white;
      padding: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
    }

    .compliance-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .compliance-info {
      position: relative;
      z-index: 1;
    }

    .compliance-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .compliance-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Enhanced status panel */
    .status {
      background: linear-gradient(to right, rgba(255, 222, 38, 0.1), rgba(255, 222, 38, 0.05));
      padding: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
      border-radius: var(--border-radius-md);
      border-left: 5px solid var(--accent-color);
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
      transition: all var(--transition-normal);
    }

    .status:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
    }

    .status::after {
      content: '';
      position: absolute;
      right: -30px;
      bottom: -30px;
      width: 100px;
      height: 100px;
      background-color: rgba(255, 222, 38, 0.15);
      border-radius: 50%;
      z-index: 0;
    }

    .status-content {
      position: relative;
      z-index: 1;
    }

    /* Enhanced progress bar */
    .progress-container {
      height: 24px;
      background-color: var(--light-gray);
      border-radius: var(--border-radius-lg);
      margin: var(--spacing-md) 0;
      overflow: hidden;
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .progress-bar {
      height: 100%;
      width: 75%;
      background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
      border-radius: var(--border-radius-lg);
      transition: width 1s ease;
      position: relative;
      overflow: hidden;
    }

    .progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg,
          rgba(255, 255, 255, 0.2) 25%,
          transparent 25%,
          transparent 50%,
          rgba(255, 255, 255, 0.2) 50%,
          rgba(255, 255, 255, 0.2) 75%,
          transparent 75%);
      background-size: 30px 30px;
      animation: progress-animation 2s linear infinite;
      z-index: 1;
    }

    @keyframes progress-animation {
      0% {
        background-position: 0 0;
      }

      100% {
        background-position: 30px 0;
      }
    }

    .progress-percentage {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-weight: 600;
      font-size: 14px;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      z-index: 2;
    }

    .deadline {
      color: var(--error-color);
      font-weight: 700;
      background-color: rgba(244, 67, 54, 0.1);
      padding: 3px 8px;
      border-radius: var(--border-radius-sm);
      display: inline-block;
      margin-top: var(--spacing-sm);
    }

    /* Enhanced summary boxes */
    .summary-boxes {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
    }

    .summary-box {
      background-color: #ffffff;
      padding: var(--spacing-lg);
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

    .sidebar-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: var(--spacing-md) 0;
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
  </style>
</head>

<body>
  <div class="container">
    <div class="sidebar">
      <div class="sidebar-header">
        <img src="../assets/CCIS-Logo-Official.png" alt="College Logo" class="logo">
        <h3>CCIS - <i>FACULTY HUB</i></h3>
      </div>
      <nav class="nav-menu">
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="credentials.php"><i class="fa-solid fa-scroll"></i> Credentials</a>
        <a href="documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a>
        <a href="reminders.php"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php" class="active"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
        <form action="{{ route('logout') }}" method="POST">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <button type="submit" style="
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
          <a href="reminders.php" class="action-button"><i class="fa-solid fa-bell"></i> View All Reminders</a>
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