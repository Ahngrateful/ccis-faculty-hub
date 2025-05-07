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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- jsPDF for PDF generation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <!-- Additional credentials-specific styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* Credentials-specific styles */
    .credentials-header {
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

    .credentials-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .credentials-info {
      position: relative;
      z-index: 1;
    }

    .credentials-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .credentials-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Enhanced tabs */
    .tabs {
      display: flex;
      gap: var(--spacing-md);
      margin-bottom: var(--spacing-xl);
      padding-bottom: var(--spacing-md);
      border-bottom: 1px solid var(--medium-gray);
      overflow-x: auto;
      scrollbar-width: thin;
    }

    .tab {
      padding: var(--spacing-md) var(--spacing-lg);
      border-radius: var(--border-radius-md);
      cursor: pointer;
      transition: all var(--transition-normal);
      font-weight: 600;
      text-align: center;
      min-width: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid transparent;
      position: relative;
      overflow: hidden;
    }

    .tab i {
      margin-right: var(--spacing-sm);
      font-size: 18px;
      transition: transform var(--transition-fast);
    }

    .tab:hover {
      background-color: rgba(117, 217, 121, 0.1);
      color: var(--primary-color);
      border-color: var(--primary-light);
    }

    .tab:hover i {
      transform: scale(1.2);
    }

    .tab.active {
      background: linear-gradient(to right, var(--primary-color), var(--primary-light));
      color: white;
      box-shadow: var(--shadow-sm);
    }

    .tab.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 50%;
      transform: translateX(-50%);
      width: 10px;
      height: 10px;
      background: var(--primary-color);
      clip-path: polygon(50% 100%, 0 0, 100% 0);
    }

    /* Enhanced form */
    form {
      background-color: #ffffff;
      padding: var(--spacing-xl);
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      margin-bottom: var(--spacing-xl);
      border-left: 4px solid var(--primary-color);
      transition: all var(--transition-normal);
    }

    form:hover {
      box-shadow: var(--shadow-lg);
      transform: translateY(-3px);
    }

    .form-title {
      color: var(--primary-color);
      font-size: 1.4rem;
      margin-bottom: var(--spacing-lg);
      display: flex;
      align-items: center;
      font-weight: 600;
    }

    .form-title i {
      margin-right: var(--spacing-md);
      color: var(--secondary-color);
      font-size: 1.5rem;
    }

    label {
      display: block;
      margin-top: var(--spacing-md);
      margin-bottom: var(--spacing-xs);
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

    /* CV Generator styles */
    .cv-generator-section {
      background: linear-gradient(45deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
      border: 1px solid var(--medium-gray);
      border-radius: var(--border-radius-md);
      padding: var(--spacing-md);
      margin-bottom: var(--spacing-xl);
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--shadow-sm);
      animation: fadeIn 0.5s ease-out;
    }

    .cv-btn {
      background-color: var(--primary-color);
      background-image: linear-gradient(to right, var(--primary-color), var(--secondary-color));
      color: white;
      padding: var(--spacing-md) var(--spacing-lg);
      border: none;
      border-radius: var(--border-radius-md);
      font-size: 1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(44, 150, 48, 0.3);
    }

    .cv-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(44, 150, 48, 0.5);
      background-image: linear-gradient(to right, var(--primary-color), var(--secondary-light));
    }

    .cv-btn i {
      font-size: 1.2rem;
      margin-right: var(--spacing-xs);
    }

    .cv-info {
      font-size: 0.9rem;
      color: var(--dark-gray);
      max-width: 60%;
      line-height: 1.4;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* CV Modal Styles */
    .cv-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
      padding: var(--spacing-lg);
    }

    .cv-modal-content {
      background-color: white;
      border-radius: var(--border-radius-lg);
      box-shadow: var(--shadow-lg);
      width: 100%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      animation: modal-slide-in 0.4s ease-out;
    }

    .cv-header {
      background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
      color: white;
      padding: var(--spacing-lg);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
      border-top-left-radius: var(--border-radius-lg);
      border-top-right-radius: var(--border-radius-lg);
    }

    .cv-header-info {
      display: flex;
      align-items: center;
      gap: var(--spacing-lg);
    }

    .cv-profile-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .cv-name {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: var(--spacing-xs);
    }

    .cv-role {
      font-size: 1.2rem;
      opacity: 0.9;
      margin-bottom: var(--spacing-sm);
    }

    .cv-contact {
      font-size: 0.9rem;
      opacity: 0.8;
    }

    .cv-content {
      padding: var(--spacing-lg);
    }

    .cv-section {
      margin-bottom: var(--spacing-lg);
      border-bottom: 1px solid var(--light-gray);
      padding-bottom: var(--spacing-md);
    }

    .cv-section-title {
      color: var(--primary-color);
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: var(--spacing-md);
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
    }

    .cv-section-title i {
      color: var(--secondary-color);
    }

    .cv-item {
      margin-bottom: var(--spacing-md);
    }

    .cv-item-title {
      font-weight: 600;
      margin-bottom: var(--spacing-xs);
    }

    .cv-item-subtitle {
      color: var(--dark-gray);
      font-size: 0.9rem;
      margin-bottom: var(--spacing-xs);
    }

    .cv-date {
      font-style: italic;
      color: var(--medium-gray);
      font-size: 0.8rem;
    }

    .cv-description {
      margin-top: var(--spacing-sm);
      font-size: 0.9rem;
      line-height: 1.5;
      color: var(--dark-gray);
    }

    .cv-actions {
      display: flex;
      justify-content: flex-end;
      padding: var(--spacing-md) var(--spacing-lg);
      background: var(--light-gray);
      gap: var(--spacing-md);
      border-bottom-left-radius: var(--border-radius-lg);
      border-bottom-right-radius: var(--border-radius-lg);
    }

    .cv-btn-download {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: var(--spacing-sm) var(--spacing-lg);
      border-radius: var(--border-radius-sm);
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .cv-btn-download:hover {
      background-color: var(--secondary-color);
    }

    .cv-btn-close {
      background-color: #f0f0f0;
      border: 1px solid var(--medium-gray);
      color: var(--dark-gray);
      padding: var(--spacing-sm) var(--spacing-lg);
      border-radius: var(--border-radius-sm);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .cv-btn-close:hover {
      background-color: #e5e5e5;
    }

    @keyframes modal-slide-in {
      from {
        opacity: 0;
        transform: translateY(-50px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      .cv-generator-section {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
      }

      .cv-info {
        max-width: 100%;
      }

      .cv-header-info {
        flex-direction: column;
        text-align: center;
      }

      .cv-profile-img {
        margin-bottom: var(--spacing-md);
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
        <a href="credentials.php" class="active"><i class="fa-solid fa-scroll"></i> Credentials</a>
        <a href="documents.php"><i class="fa-solid fa-file-lines"></i> Documents</a>
        <a href="reminders.php"><i class="fa-solid fa-bell"></i> Reminders</a>
        <a href="ched_compliance.php"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
        <a href="logout.php"><i class="fa-solid fa-door-open"></i> Logout</a>
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

        <!-- Generate CV Button -->
        <div class="cv-generator-section">
          <button id="generateCvBtn" class="cv-btn">
            <i class="fa-solid fa-file-pdf"></i> Generate Faculty CV
          </button>
          <span class="cv-info">Generate a professional CV with your faculty information, education, experience, and
            credentials</span>
        </div>

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
              <input type="text" id="issuing-org" placeholder="Organization name" />
            </div>
          </div>

          <label for="credential-details">Details</label>
          <textarea id="credential-details" placeholder="Enter credential details, license number, etc."></textarea>

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

  <!-- CV Modal -->
  <div id="cvModal" class="cv-modal">
    <div class="cv-modal-content">
      <div class="cv-header">
        <div class="cv-header-info">
          <img src="../assets/profile-default.jpg" alt="Faculty Profile" class="cv-profile-img">
          <div>
            <div class="cv-name">Dr. Sharleen Olaguir</div>
            <div class="cv-role">Associate Professor of Computer Science</div>
            <div class="cv-contact">
              <div><i class="fa-solid fa-envelope"></i> sharleen.olaguir@umak.edu.ph</div>
              <div><i class="fa-solid fa-phone"></i> +63 912 345 6789</div>
              <div><i class="fa-solid fa-location-dot"></i> University of Makati, Philippines</div>
            </div>
          </div>
        </div>
      </div>

      <div class="cv-content">
        <!-- Personal Info Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-user"></i> Personal Information
          </div>
          <div class="cv-item">
            <div class="cv-description">
              Dedicated computer science educator with over 10 years of experience in higher education. Specializing in
              artificial intelligence, data science, and software engineering. Committed to fostering academic
              excellence and innovation in computer science education.
            </div>
          </div>
        </div>

        <!-- Education Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-graduation-cap"></i> Education
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Ph.D. in Computer Science</div>
            <div class="cv-item-subtitle">University of the Philippines</div>
            <div class="cv-date">2015 - 2018</div>
            <div class="cv-description">
              Dissertation: "Advanced Machine Learning Techniques for Natural Language Processing"
            </div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Master of Science in Computer Science</div>
            <div class="cv-item-subtitle">De La Salle University</div>
            <div class="cv-date">2012 - 2014</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Bachelor of Science in Computer Science</div>
            <div class="cv-item-subtitle">University of Santo Tomas</div>
            <div class="cv-date">2008 - 2012</div>
          </div>
        </div>

        <!-- Experience Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-briefcase"></i> Professional Experience
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Associate Professor</div>
            <div class="cv-item-subtitle">University of Makati, College of Computer Science and Information Systems
            </div>
            <div class="cv-date">2018 - Present</div>
            <div class="cv-description">
              Teaching advanced courses in artificial intelligence, data science, and software engineering. Developing
              curriculum and conducting research in machine learning applications.
            </div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Assistant Professor</div>
            <div class="cv-item-subtitle">De La Salle University</div>
            <div class="cv-date">2014 - 2018</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Software Engineer</div>
            <div class="cv-item-subtitle">Accenture Philippines</div>
            <div class="cv-date">2012 - 2014</div>
          </div>
        </div>

        <!-- Teaching Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-chalkboard-teacher"></i> Teaching Experience
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Advanced Artificial Intelligence</div>
            <div class="cv-item-subtitle">Graduate Course</div>
            <div class="cv-date">2019 - Present</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Data Mining and Machine Learning</div>
            <div class="cv-item-subtitle">Undergraduate Course</div>
            <div class="cv-date">2018 - Present</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Software Engineering Principles</div>
            <div class="cv-item-subtitle">Undergraduate Course</div>
            <div class="cv-date">2018 - Present</div>
          </div>
        </div>

        <!-- Research Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-flask"></i> Research
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Machine Learning Applications in Healthcare</div>
            <div class="cv-date">2020 - Present</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Natural Language Processing for Filipino Languages</div>
            <div class="cv-date">2018 - 2020</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Computer Vision Systems for Urban Planning</div>
            <div class="cv-date">2016 - 2018</div>
          </div>
        </div>

        <!-- Seminars Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-chalkboard"></i> Seminars & Workshops
          </div>
          <div class="cv-item">
            <div class="cv-item-title">AI Ethics and Future Challenges</div>
            <div class="cv-item-subtitle">Speaker, National Computer Science Summit</div>
            <div class="cv-date">2024</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Introduction to Deep Learning</div>
            <div class="cv-item-subtitle">Workshop Facilitator, University of Makati</div>
            <div class="cv-date">2023</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Data Science for Social Good</div>
            <div class="cv-item-subtitle">Speaker, DOST Conference</div>
            <div class="cv-date">2022</div>
          </div>
        </div>

        <!-- Awards Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-trophy"></i> Awards & Recognitions
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Outstanding Faculty Award</div>
            <div class="cv-item-subtitle">University of Makati</div>
            <div class="cv-date">2023</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Best Research Paper</div>
            <div class="cv-item-subtitle">Philippine Computing Society Conference</div>
            <div class="cv-date">2022</div>
          </div>
        </div>

        <!-- Licenses & Certifications Section -->
        <div class="cv-section">
          <div class="cv-section-title">
            <i class="fa-solid fa-certificate"></i> Licenses & Certifications
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Professional License in Computer Science</div>
            <div class="cv-item-subtitle">Professional Regulation Commission (PRC)</div>
            <div class="cv-date">Expires: 2026</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">AWS Certified Solutions Architect</div>
            <div class="cv-item-subtitle">Amazon Web Services</div>
            <div class="cv-date">Expires: 2026</div>
          </div>
          <div class="cv-item">
            <div class="cv-item-title">Microsoft Certified: Azure Data Scientist Associate</div>
            <div class="cv-item-subtitle">Microsoft</div>
            <div class="cv-date">Issued: 2022</div>
          </div>
        </div>
      </div>

      <div class="cv-actions">
        <button class="cv-btn-close">Close</button>
        <button class="cv-btn-download">
          <i class="fa-solid fa-download"></i> Download PDF
        </button>
      </div>
    </div>
  </div>

  <script>
    // Get the modal elements
    const cvModal = document.getElementById('cvModal');
    const cvBtn = document.getElementById('generateCvBtn');
    const closeBtn = document.querySelector('.cv-btn-close');
    const downloadBtn = document.querySelector('.cv-btn-download');

    // Open modal when Generate CV button is clicked
    cvBtn.addEventListener('click', function () {
      cvModal.style.display = 'flex';
      document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
    });

    // Close modal when close button is clicked
    closeBtn.addEventListener('click', function () {
      cvModal.style.display = 'none';
      document.body.style.overflow = 'auto'; // Restore scrolling
    });

    // Close modal when clicking outside the modal content
    window.addEventListener('click', function (event) {
      if (event.target === cvModal) {
        cvModal.style.display = 'none';
        document.body.style.overflow = 'auto';
      }
    });

    // Download CV as PDF functionality
    downloadBtn.addEventListener('click', function () {
      // Show loader or downloading message
      const originalText = downloadBtn.innerHTML;
      downloadBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating PDF...';
      downloadBtn.disabled = true;

      // Wait for the next frame to allow the UI to update
      setTimeout(function () {
        // Define the element to capture
        const cvContent = document.querySelector('.cv-modal-content');
        const fileName = 'Faculty_CV_' + new Date().toISOString().slice(0, 10) + '.pdf';

        // Create scale factor for better quality
        const scale = 2;

        // Configure html2canvas
        html2canvas(cvContent, {
          scale: scale,
          useCORS: true,
          allowTaint: true,
          backgroundColor: '#ffffff',
          logging: false,
          onclone: function (clonedDoc) {
            // Hide action buttons in the clone
            const actions = clonedDoc.querySelector('.cv-actions');
            if (actions) actions.style.display = 'none';

            // Make the clone's content visible
            const content = clonedDoc.querySelector('.cv-modal-content');
            if (content) {
              content.style.maxHeight = 'none';
              content.style.overflow = 'visible';
            }
          }
        }).then(canvas => {
          // Initialize jsPDF
          const { jsPDF } = window.jspdf;
          const pdf = new jsPDF('p', 'mm', 'a4');

          // Calculate the PDF dimensions
          const imgWidth = 210; // A4 width in mm
          const pageHeight = 297; // A4 height in mm
          const imgHeight = canvas.height * imgWidth / canvas.width;
          let heightLeft = imgHeight;

          // Add image to PDF (first page)
          const imgData = canvas.toDataURL('image/png');
          pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
          heightLeft -= pageHeight;

          // Add additional pages if needed
          let position = -pageHeight; // Negative to move up for next pages
          while (heightLeft >= 0) {
            position -= pageHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
          }

          // Save the PDF
          pdf.save(fileName);

          // Restore button text
          downloadBtn.innerHTML = originalText;
          downloadBtn.disabled = false;
        }).catch(error => {
          console.error('Error generating PDF:', error);
          alert('There was an error generating the PDF. Please try again.');
          downloadBtn.innerHTML = originalText;
          downloadBtn.disabled = false;
        });
      }, 100);
    });
  </script>
</body>

</html>