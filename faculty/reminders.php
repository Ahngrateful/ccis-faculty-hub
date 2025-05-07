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
  <title>Reminders - FPMS</title>
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

    .notification {
      margin-left: 15px;
      background-color: var(--accent-color);
      color: var(--primary-color);
      padding: 5px 12px;
      border-radius: 50px;
      font-weight: bold;
      font-size: 14px;
      display: flex;
      align-items: center;
    }

    .notification i {
      margin-right: 5px;
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

    .filters {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
      align-items: center;
      flex-wrap: wrap;
    }

    select,
    input {
      padding: 12px;
      width: 200px;
      border: 1px solid var(--medium-gray);
      border-radius: 4px;
      font-family: inherit;
      transition: all 0.3s;
    }

    select:focus,
    input:focus {
      outline: none;
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 2px rgba(117, 217, 121, 0.2);
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
      display: flex;
      align-items: center;
    }

    button:hover {
      background-color: var(--secondary-color);
      color: var(--primary-color);
    }

    button i {
      margin-right: 8px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
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

    .action-links a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
      margin-right: 15px;
      display: inline-flex;
      align-items: center;
    }

    .action-links a:hover {
      color: var(--secondary-color);
    }

    .action-links i {
      margin-right: 5px;
    }

    .status-urgent {
      color: var(--error-color);
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .status-upcoming {
      color: var(--warning-color);
      font-weight: 500;
      display: flex;
      align-items: center;
    }

    .status-info {
      color: var(--info-color);
      font-weight: 500;
      display: flex;
      align-items: center;
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

      .filters {
        flex-direction: column;
        align-items: flex-start;
      }

      select,
      input {
        width: 100%;
      }

      table {
        display: block;
        overflow-x: auto;
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
        <a href="reminders.php" class="active"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
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
        <div class="notification"><i class="fa-solid fa-bell"></i> 3</div>
      </div>

      <div class="main-content">
        <h2>CHED Compliance Reminders</h2>

        <div class="filters">
          <select>
            <option>All Reminders</option>
            <option>Urgent</option>
            <option>Upcoming</option>
            <option>Information</option>
          </select>
          <button><i class="fa-solid fa-filter"></i> Filter</button>
        </div>

        <table>
          <thead>
            <tr>
              <th>Reminder Type</th>
              <th>Due Date</th>
              <th>CHED Status</th>
              <th>Message</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <span class="status-urgent"><i class="fa-solid fa-circle-exclamation"></i> Expiring
                  Credential</span>
              </td>
              <td>2025-05-15</td>
              <td>Pending</td>
              <td>
                Your AWS Certification is expiring soon. Please renew to
                maintain CHED compliance.
              </td>
              <td class="action-links">
                <a href="credentials.php"><i class="fa-solid fa-rotate-right"></i> Renew</a>
                <a href="#"><i class="fa-solid fa-check"></i> Mark as Read</a>
              </td>
            </tr>
            <tr>
              <td>
                <span class="status-upcoming"><i class="fa-solid fa-clock"></i> Document Submission</span>
              </td>
              <td>2025-06-01</td>
              <td>Required</td>
              <td>
                Please submit your updated teaching portfolio for the upcoming
                CHED audit.
              </td>
              <td class="action-links">
                <a href="documents.php"><i class="fa-solid fa-upload"></i> Upload</a>
                <a href="#"><i class="fa-solid fa-check"></i> Mark as Read</a>
              </td>
            </tr>
            <tr>
              <td>
                <span class="status-info"><i class="fa-solid fa-circle-info"></i> Profile
                  Update</span>
              </td>
              <td>2025-06-15</td>
              <td>Recommended</td>
              <td>
                Update your professional profile with recent achievements for
                better CHED evaluation.
              </td>
              <td class="action-links">
                <a href="profile.php"><i class="fa-solid fa-pen-to-square"></i> Update</a>
                <a href="#"><i class="fa-solid fa-check"></i> Mark as Read</a>
              </td>
            </tr>
          </tbody>
        </table>
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