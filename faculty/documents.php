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
  <title>Documents - FPMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Additional documents-specific styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* Documents-specific styles */
    .documents-header {
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

    .documents-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .documents-info {
      position: relative;
      z-index: 1;
    }

    .documents-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .documents-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Enhanced document cards */
    .document-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: var(--spacing-lg);
      margin-top: var(--spacing-lg);
    }

    .document-card {
      background-color: white;
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      overflow: hidden;
      transition: all var(--transition-normal);
      position: relative;
      border-bottom: 3px solid transparent;
    }

    .document-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
      border-bottom: 3px solid var(--secondary-color);
    }

    .document-icon {
      padding: var(--spacing-lg);
      font-size: 36px;
      display: flex;
      justify-content: center;
      color: var(--primary-color);
      background-color: rgba(0, 104, 52, 0.05);
    }

    .document-info {
      padding: var(--spacing-md);
    }

    .document-name {
      font-weight: 600;
      margin-bottom: var(--spacing-xs);
      color: var(--primary-color);
    }

    .document-meta {
      font-size: 0.9rem;
      color: var(--dark-gray);
      display: flex;
      justify-content: space-between;
      margin-bottom: var(--spacing-sm);
    }

    .document-actions {
      display: flex;
      justify-content: flex-end;
      padding: var(--spacing-sm);
      gap: var(--spacing-sm);
    }

    .document-action-btn {
      background-color: transparent;
      border: none;
      color: var(--primary-color);
      cursor: pointer;
      border-radius: var(--border-radius-sm);
      padding: var(--spacing-xs) var(--spacing-sm);
      transition: all var(--transition-fast);
    }

    .document-action-btn:hover {
      background-color: rgba(0, 104, 52, 0.1);
      transform: translateY(-2px);
    }

    /* Enhanced table */
    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: var(--border-radius-md);
      overflow: hidden;
      box-shadow: var(--shadow-md);
      margin-top: var(--spacing-lg);
    }

    th {
      background: linear-gradient(to right, var(--primary-color), var(--primary-light));
      color: white;
      padding: var(--spacing-md);
      text-align: left;
      font-weight: 600;
      position: relative;
    }

    /* Custom file input styling */
    .custom-file-upload {
      display: block;
      position: relative;
      cursor: pointer;
      background-color: white;
      padding: var(--spacing-lg);
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      text-align: center;
      margin-bottom: var(--spacing-xl);
      border: 2px dashed var(--medium-gray);
      transition: all var(--transition-normal);
    }

    .custom-file-upload:hover {
      border-color: var(--primary-color);
      background-color: rgba(0, 104, 52, 0.02);
    }

    .upload-icon {
      font-size: 48px;
      color: var(--primary-color);
      margin-bottom: var(--spacing-md);
    }

    .upload-text {
      font-weight: 500;
      color: var(--dark-gray);
      margin-bottom: var(--spacing-sm);
    }

    .upload-subtext {
      font-size: 0.9rem;
      color: var(--dark-gray);
    }

    input[type="file"] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
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

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .modal-content {
      background-color: white;
      border-radius: var(--border-radius-lg);
      box-shadow: var(--shadow-lg);
      width: 100%;
      max-width: 600px;
      animation: modal-appear 0.3s ease-out;
      position: relative;
      overflow: hidden;
    }

    .modal-header {
      background: linear-gradient(to right, var(--primary-color), var(--primary-light));
      color: white;
      padding: var(--spacing-md) var(--spacing-lg);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-title {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .close-modal {
      background: transparent;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.3s ease;
    }

    .close-modal:hover {
      transform: rotate(90deg);
      background: transparent;
      color: white;
    }

    .modal-body {
      padding: var(--spacing-lg);
    }

    .form-group {
      margin-bottom: var(--spacing-md);
    }

    .form-group label {
      display: block;
      margin-bottom: var(--spacing-xs);
      color: var(--dark-gray);
      font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: var(--spacing-sm);
      border: 1px solid var(--medium-gray);
      border-radius: var(--border-radius-sm);
      font-family: inherit;
      font-size: 1rem;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 2px rgba(117, 217, 121, 0.2);
    }

    .modal-footer {
      padding: var(--spacing-md) var(--spacing-lg);
      background-color: var(--light-gray);
      display: flex;
      justify-content: flex-end;
      gap: var(--spacing-sm);
    }

    .modal-btn {
      padding: var(--spacing-sm) var(--spacing-lg);
      border-radius: var(--border-radius-sm);
      font-weight: 500;
      cursor: pointer;
      transition: all var(--transition-normal);
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: white;
      border: none;
    }

    .btn-primary:hover {
      background-color: var(--secondary-color);
      color: var(--primary-color);
    }

    .btn-secondary {
      background-color: white;
      color: var(--dark-gray);
      border: 1px solid var(--medium-gray);
    }

    .btn-secondary:hover {
      background-color: var(--light-gray);
    }

    @keyframes modal-appear {
      from {
        opacity: 0;
        transform: translateY(-50px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
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
        <a href="documents.php" class="active"><i class="fa-solid fa-file-lines"></i> Documents</a>
        <a href="reminders.php"><i class="fa-solid fa-bell"></i> Reminders</a>
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
      </div>

      <div class="main-content">
        <div class="tabs">
          <div class="tab">
            <a href="credentials.php"><i class="fa-solid fa-scroll"></i> Credentials</a>
          </div>
          <div class="tab active">
            <i class="fa-solid fa-file-lines"></i> Documents
          </div>
        </div>

        <h2>Documents Management</h2>

        <table>
          <thead>
            <tr>
              <th>Type</th>
              <th>Details</th>
              <th>Status</th>
              <th>Expiry</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>IT Certification</td>
              <td>AWS Certified Solutions Architect</td>
              <td><span class="status-valid">Valid</span></td>
              <td>2026-05-06</td>
              <td class="action-links">
                <a href="#"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                <a href="#"><i class="fa-solid fa-trash"></i> Delete</a>
              </td>
            </tr>
            <tr>
              <td>License</td>
              <td>Professional Teaching License</td>
              <td><span class="status-pending">Pending</span></td>
              <td>2027-03-15</td>
              <td class="action-links">
                <a href="#"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                <a href="#"><i class="fa-solid fa-trash"></i> Delete</a>
              </td>
            </tr>
            <tr>
              <td>Certification</td>
              <td>Microsoft Certified Educator</td>
              <td><span class="status-expired">Expired</span></td>
              <td>2024-12-31</td>
              <td class="action-links">
                <a href="#"><i class="fa-solid fa-rotate-right"></i> Renew</a>
                <a href="#"><i class="fa-solid fa-trash"></i> Delete</a>
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

  <!-- Edit Document Modal -->
  <div id="editDocumentModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Edit Document</div>
        <button class="close-modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="editDocumentForm">
          <input type="hidden" id="document_id" name="document_id">
          <div class="form-group">
            <label for="document_type">Document Type</label>
            <select id="document_type" name="document_type" required>
              <option value="IT Certification">IT Certification</option>
              <option value="License">License</option>
              <option value="Certification">Certification</option>
              <option value="Diploma">Diploma</option>
              <option value="Certificate">Certificate</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="document_details">Details</label>
            <input type="text" id="document_details" name="document_details" required>
          </div>
          <div class="form-group">
            <label for="document_status">Status</label>
            <select id="document_status" name="document_status" required>
              <option value="valid">Valid</option>
              <option value="pending">Pending</option>
              <option value="expired">Expired</option>
            </select>
          </div>
          <div class="form-group">
            <label for="document_expiry">Expiry Date</label>
            <input type="date" id="document_expiry" name="document_expiry" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="modal-btn btn-secondary close-modal-btn">Cancel</button>
        <button type="button" class="modal-btn btn-primary" id="saveDocumentBtn">Save Changes</button>
      </div>
    </div>
  </div>

  <script>
    // Get modal elements
    const modal = document.getElementById('editDocumentModal');
    const editButtons = document.querySelectorAll('.edit-document-btn');
    const closeButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
    const saveButton = document.getElementById('saveDocumentBtn');

    // Form elements
    const documentIdInput = document.getElementById('document_id');
    const documentTypeSelect = document.getElementById('document_type');
    const documentDetailsInput = document.getElementById('document_details');
    const documentStatusSelect = document.getElementById('document_status');
    const documentExpiryInput = document.getElementById('document_expiry');

    // Add event listeners to all edit buttons
    document.querySelectorAll('a[href="#"]').forEach(link => {
      if (link.innerHTML.includes('Edit')) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          // Get the table row data
          const row = this.closest('tr');
          const cells = row.querySelectorAll('td');

          // Populate modal with data from the row
          documentIdInput.value = '1'; // Placeholder ID
          documentTypeSelect.value = cells[0].textContent.trim();
          documentDetailsInput.value = cells[1].textContent.trim();

          // Extract status from the status span
          const statusSpan = cells[2].querySelector('span');
          if (statusSpan) {
            // Extract class name for status (assuming class names like status-valid)
            const statusClass = Array.from(statusSpan.classList)
              .find(cls => cls.startsWith('status-'));
            if (statusClass) {
              documentStatusSelect.value = statusClass.replace('status-', '');
            }
          }

          // Convert expiry date format from display format to input format (YYYY-MM-DD)
          const expiryDate = cells[3].textContent.trim();
          if (expiryDate) {
            const [year, month, day] = expiryDate.split('-');
            documentExpiryInput.value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
          }

          // Show the modal
          modal.style.display = 'flex';
        });
      }
    });

    // Close modal when clicking close buttons
    closeButtons.forEach(button => {
      button.addEventListener('click', () => {
        modal.style.display = 'none';
      });
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    // Save button functionality (placeholder for now)
    saveButton.addEventListener('click', () => {
      // Here you would normally send an AJAX request to update the document
      // For demonstration purposes, we'll just close the modal and show an alert
      alert('Document updated successfully!');
      modal.style.display = 'none';
    });
  </script>
</body>

</html>