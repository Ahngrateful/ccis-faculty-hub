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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Additional reminders-specific styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* Reminders-specific styles */
    .reminders-header {
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

    .reminders-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .reminders-info {
      position: relative;
      z-index: 1;
    }

    .reminders-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .reminders-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Filters and table styles */
    .filters {
      display: flex;
      gap: var(--spacing-md);
      margin-bottom: var(--spacing-lg);
      align-items: center;
      flex-wrap: wrap;
    }

    select {
      padding: var(--spacing-sm);
      min-width: 200px;
      border: 1px solid var(--medium-gray);
      border-radius: var(--border-radius-sm);
      font-family: inherit;
      transition: all var(--transition-normal);
    }

    select:focus {
      outline: none;
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 2px rgba(117, 217, 121, 0.2);
    }

    /* Enhanced table */
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: var(--border-radius-md);
      overflow: hidden;
      box-shadow: var(--shadow-md);
      margin-top: var(--spacing-md);
    }

    th {
      background: linear-gradient(to right, var(--primary-color), var(--primary-light));
      color: white;
      padding: var(--spacing-md);
      text-align: left;
      font-weight: 600;
      position: relative;
    }

    td {
      border-bottom: 1px solid var(--medium-gray);
      padding: var(--spacing-md);
      text-align: left;
    }

    tr:hover {
      background-color: var(--light-gray);
    }

    button {
      background-color: var(--primary-color);
      color: white;
      padding: var(--spacing-sm) var(--spacing-md);
      border: none;
      cursor: pointer;
      border-radius: var(--border-radius-sm);
      font-weight: 500;
      transition: all var(--transition-normal);
      display: flex;
      align-items: center;
    }

    button:hover {
      background-color: var(--secondary-color);
      color: var(--primary-color);
    }

    button i {
      margin-right: var(--spacing-xs);
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
        <a href="reminders.php" class="active"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
        <a href="logout.php"><i class="fa-solid fa-door-open"></i> Logout</a>
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
        <!-- Reminders Header Banner -->
        <div class="reminders-header slide-in-left">
          <div class="reminders-info">
            <div class="reminders-title">CHED Compliance Reminders</div>
            <div class="reminders-subtitle">Stay updated with all your compliance requirements and deadlines</div>
          </div>
        </div>

        <h2>Reminders Management</h2>

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

  <script>
    // Add fade-in animations to elements
    $(document).ready(function () {
      // Apply animations to reminders header
      $('.reminders-header').addClass('slide-in-left');

      // Animate the table rows with staggered delay
      $('tbody tr').each(function (i) {
        $(this).addClass('slide-in-right');
        $(this).css('animation-delay', (i * 0.1) + 's');
      });

      // Animate the button
      $('button').addClass('slide-in-left');
      $('button').css('animation-delay', '0.3s');
    });
  </script>
</body>

</html>