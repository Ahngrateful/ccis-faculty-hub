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
  <title>Credentials/Documents - FPMS</title>
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
      /* Prevent double scrollbars */
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
      /* Allow only the menu to scroll if needed */
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
      /* Match sidebar width */
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      /* Enable scrolling for content */
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
    }

    /* Rest of your styles remain the same */
    .tabs {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
      border-bottom: 1px solid var(--medium-gray);
      padding-bottom: 15px;
    }

    .tab {
      padding: 12px 24px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 500;
      border: 1px solid transparent;
    }

    .tab:hover {
      background-color: rgba(117, 217, 121, 0.1);
      color: var(--primary-color);
    }

    .tab.active {
      background-color: var(--primary-color);
      color: white;
    }

    form {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }

    label {
      display: block;
      margin-top: 15px;
      color: var(--primary-color);
      font-weight: 500;
    }

    textarea,
    select,
    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-family: "Segoe UI", Arial, sans-serif;
      transition: all 0.3s;
    }

    textarea {
      height: 120px;
      resize: vertical;
    }

    textarea:focus,
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
      margin-top: 15px;
    }

    button:hover {
      background-color: var(--secondary-color);
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

    .action-links a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
      margin-right: 15px;
    }

    .action-links a:hover {
      color: var(--secondary-color);
    }

    .status-valid {
      color: var(--success-color);
      font-weight: 500;
    }

    .status-pending {
      color: #ff9800;
      font-weight: 500;
    }

    .status-expired {
      color: var(--error-color);
      font-weight: 500;
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

      .tabs {
        flex-direction: column;
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
        <a href="credentials.php" class="active"><i class="fa-solid fa-scroll"></i> Credentials</a>
        <a href="documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a>
        <a href="reminders.php"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <a href="#"><i class="fa-solid fa-door-open"></i> Logout</a>
      </nav>
    </div>

    <div class="content">
      <div class="header">
        <div class="user-info">
          <i class="fa-solid fa-circle-user"></i> Welcome, Sharleen Olaguir! -
          Faculty
        </div>
      </div>

      <div class="main-content">
        <div class="tabs">
          <div class="tab active">
            <i class="fa-solid fa-scroll"></i> Credentials
          </div>
          <div class="tab">
            <a href="documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a>
          </div>
        </div>

        <h2>Credentials Management</h2>

        <form>
          <div class="form-row">
            <div>
              <label for="credential-type">Credential Type (CHED-Required)</label>
              <select id="credential-type">
                <option>License</option>
                <option>Certification</option>
                <option>IT Certification</option>
                <option>Academic Credential</option>
                <option>Professional Certification</option>
              </select>
            </div>
            <div>
              <label for="issuing-org">Issuing Organization</label>
              <input
                type="text"
                id="issuing-org"
                placeholder="Organization name" />
            </div>
          </div>

          <label for="credential-details">Details</label>
          <textarea
            id="credential-details"
            placeholder="Enter credential details, license number, etc."></textarea>

          <div class="form-row">
            <div>
              <label for="issue-date">Issue Date</label>
              <input type="date" id="issue-date" value="2025-05-06" />
            </div>
            <div>
              <label for="expiry-date">Expiry Date</label>
              <input type="date" id="expiry-date" value="2026-05-06" />
            </div>
          </div>

          <label for="credential-file">Upload Document (Proof)</label>
          <input type="file" id="credential-file" />

          <button type="submit">
            <i class="fa-solid fa-paper-plane"></i> Submit for CHED Review
          </button>
        </form>

        <h3>Your Current Credentials</h3>
        <table>
          <thead>
            <tr>
              <th>Type</th>
              <th>Details</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Professional License</td>
              <td>PRC License #1234567</td>
              <td class="status-valid">Valid</td>
              <td class="action-links">
                <a href="#"><i class="fa-solid fa-eye"></i> View</a>
                <a href="#"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
              </td>
            </tr>
            <tr>
              <td>IT Certification</td>
              <td>Microsoft Certified Professional</td>
              <td class="status-pending">Pending Review</td>
              <td class="action-links">
                <a href="#"><i class="fa-solid fa-eye"></i> View</a>
                <a href="#"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
              </td>
            </tr>
            <tr>
              <td>Academic Credential</td>
              <td>PhD in Computer Science</td>
              <td class="status-valid">Valid</td>
              <td class="action-links">
                <a href="#"><i class="fa-solid fa-eye"></i> View</a>
                <a href="#"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
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