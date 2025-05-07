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

// Get the current user info first - this is critical
$user_query = "SELECT * FROM faculty WHERE faculty_id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$faculty_info = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($stmt);

// Now try to get the role information
if ($faculty_info) {
  $role_query = "SELECT role_name FROM roles WHERE roles_id = ?";
  $stmt = mysqli_prepare($conn, $role_query);
  mysqli_stmt_bind_param($stmt, "i", $faculty_info['role_id']);
  mysqli_stmt_execute($stmt);
  $role_result = mysqli_stmt_get_result($stmt);
  $role_info = mysqli_fetch_assoc($role_result);
  mysqli_stmt_close($stmt);

  if ($role_info) {
    $faculty_info['role_name'] = $role_info['role_name'];
  } else {
    $faculty_info['role_name'] = 'Faculty'; // Default role if not found
  }
} else {
  // No faculty found - set default info for display purposes
  $faculty_info = [
    'first_name' => 'User',
    'last_name' => '',
    'email' => 'user@example.com',
    'status' => 'active',
    'profile_image' => '',
    'role_name' => 'Faculty'
  ];
}

// Get faculty credentials
$credentials_query = "SELECT * FROM credentials WHERE faculty_id = ? ORDER BY issue_date DESC";
$stmt = mysqli_prepare($conn, $credentials_query);
mysqli_stmt_bind_param($stmt, "i", $faculty_id);
mysqli_stmt_execute($stmt);
$credentials_result = mysqli_stmt_get_result($stmt);
$credentials = [];
while ($credential = mysqli_fetch_assoc($credentials_result)) {
  $credentials[] = $credential;
}
mysqli_stmt_close($stmt);

// Debug faculty ID
//echo "Faculty ID from session: " . $faculty_id;

// Set default profile image if none exists
$profile_image = $faculty_info && !empty($faculty_info['profile_image']) ? '../uploads/profile/' . $faculty_info['profile_image'] : '../assets/images/default-profile.jpg';

// If profile image file doesn't exist, use default
if (!file_exists($profile_image)) {
  $profile_image = '../assets/images/default-profile.jpg';
}

// If directory doesn't exist, create default placeholder image path
if (!file_exists('../assets/images/')) {
  mkdir('../assets/images/', 0777, true);
  $profile_image = 'https://via.placeholder.com/100';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Faculty Dashboard - FPMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Additional dashboard styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* Dashboard-specific styles */
    .welcome-banner {
      background: linear-gradient(120deg, var(--primary-light), var(--primary-color));
      border-radius: var(--border-radius-md);
      color: white;
      padding: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
      box-shadow: var(--shadow-md);
      display: flex;
      align-items: center;
      overflow: hidden;
      position: relative;
    }

    .welcome-banner::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .welcome-text {
      position: relative;
      z-index: 1;
    }

    .welcome-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .welcome-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Faculty profile card */
    .faculty-profile-card {
      background: white;
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      padding: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .faculty-profile-header {
      display: flex;
      align-items: center;
      margin-bottom: var(--spacing-md);
      padding-bottom: var(--spacing-md);
      border-bottom: 1px solid var(--light-gray);
    }

    .faculty-profile-image {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary-color);
      box-shadow: var(--shadow-sm);
      margin-right: var(--spacing-lg);
    }

    .faculty-info {
      flex: 1;
    }

    .faculty-name {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: var(--spacing-xs);
    }

    .faculty-email {
      font-size: 0.9rem;
      color: var(--dark-gray);
      margin-bottom: var(--spacing-xs);
    }

    .faculty-meta {
      display: flex;
      margin-top: var(--spacing-xs);
    }

    .faculty-status,
    .faculty-role {
      font-size: 0.85rem;
      padding: 4px 12px;
      border-radius: 20px;
      margin-right: var(--spacing-sm);
    }

    .faculty-status {
      background-color: rgba(117, 217, 121, 0.15);
      color: #2d8d31;
    }

    .faculty-status.inactive {
      background-color: rgba(220, 53, 69, 0.15);
      color: #dc3545;
    }

    .faculty-role {
      background-color: rgba(13, 110, 253, 0.15);
      color: #0d6efd;
    }

    .faculty-credentials-section {
      margin-top: var(--spacing-md);
    }

    .credentials-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: var(--spacing-md);
      color: var(--primary-color);
      border-bottom: 1px solid var(--light-gray);
      padding-bottom: var(--spacing-xs);
    }

    .credentials-list {
      display: flex;
      flex-wrap: wrap;
      gap: var(--spacing-sm);
    }

    .credential-item {
      background: var(--lighter-gray);
      padding: 10px 15px;
      border-radius: var(--border-radius-sm);
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: var(--spacing-xs);
    }

    .credential-icon {
      color: var(--primary-color);
    }

    .credential-status {
      font-size: 0.8rem;
      padding: 2px 8px;
      border-radius: 12px;
      margin-left: auto;
    }

    .credential-status.valid {
      background-color: rgba(117, 217, 121, 0.15);
      color: #2d8d31;
    }

    .credential-status.expiring {
      background-color: rgba(255, 193, 7, 0.15);
      color: #ffc107;
    }

    .credential-status.expired {
      background-color: rgba(220, 53, 69, 0.15);
      color: #dc3545;
    }

    /* Stats enhancement */
    .stat-icon {
      font-size: 36px;
      margin-bottom: var(--spacing-md);
      color: var(--primary-color);
      background: rgba(117, 217, 121, 0.15);
      width: 70px;
      height: 70px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin: 0 auto var(--spacing-md) auto;
      transition: all var(--transition-normal);
    }

    .stat-box:hover .stat-icon {
      transform: rotateY(180deg);
      background: var(--primary-color);
      color: white;
    }

    /* Quick actions panel */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: var(--spacing-md);
      margin-bottom: var(--spacing-xl);
    }

    .action-card {
      background: white;
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-md);
      padding: var(--spacing-lg);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: all var(--transition-normal);
      border-bottom: 3px solid transparent;
      cursor: pointer;
    }

    .action-card:hover {
      border-bottom: 3px solid var(--secondary-color);
      transform: translateY(-5px);
    }

    .action-icon {
      font-size: 28px;
      margin-bottom: var(--spacing-md);
      color: var(--primary-color);
      transition: all var(--transition-fast);
    }

    .action-card:hover .action-icon {
      transform: scale(1.2);
    }

    .action-title {
      font-weight: 600;
      margin-bottom: var(--spacing-xs);
      color: var(--primary-color);
    }

    .action-description {
      font-size: 0.9rem;
      color: var(--dark-gray);
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
        <a href="#" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="credentials.php"><i class="fa-solid fa-scroll"></i> Credentials</a>
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
          <i class="fa-solid fa-circle-user"></i>
          <span>Welcome, Prof. Sharleen Olaguir - Faculty</span>
        </div>
      </div>

      <div class="main-content">
        <!-- Welcome Banner -->
        <div class="welcome-banner slide-in-left">
          <div class="welcome-text">
            <div class="welcome-title">Welcome to CCIS Faculty Portal</div>
            <div class="welcome-subtitle">Your one-stop platform for managing faculty credentials and CHED compliance
            </div>
          </div>
        </div>

        <h2>Faculty Dashboard</h2>

        <?php if ($faculty_info): ?>
          <!-- Faculty Information Card -->
          <div class="faculty-profile-card slide-in-left">
            <div class="faculty-profile-header">
              <img src="<?php echo $profile_image; ?>" class="faculty-profile-image" alt="Faculty Profile Image">
              <div class="faculty-info">
                <div class="faculty-name"><?php echo $faculty_info['first_name'] . ' ' . $faculty_info['last_name']; ?>
                </div>
                <div class="faculty-email"><?php echo $faculty_info['email']; ?></div>
                <div class="faculty-meta">
                  <div class="faculty-status <?php echo $faculty_info['status']; ?>">
                    <i
                      class="fa-solid <?php echo $faculty_info['status'] === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                    <?php echo ucfirst($faculty_info['status']); ?>
                  </div>
                  <div class="faculty-role">
                    <i class="fa-solid fa-id-badge"></i>
                    <?php echo $faculty_info['role_name']; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="faculty-credentials-section">
              <div class="credentials-title">
                <i class="fa-solid fa-scroll"></i> Faculty Credentials
              </div>
              <div class="credentials-list">
                <?php if (count($credentials) > 0): ?>
                  <?php foreach (array_slice($credentials, 0, 5) as $credential): ?>
                    <div class="credential-item">
                      <span class="credential-icon"><i class="fa-solid fa-certificate"></i></span>
                      <span><?php echo $credential['credential_type']; ?></span>
                      <?php if (!empty($credential['issue_date'])): ?>
                        <span><?php echo date('M Y', strtotime($credential['issue_date'])); ?></span>
                      <?php endif; ?>
                      <span class="credential-status <?php echo $credential['status']; ?>">
                        <?php echo ucfirst($credential['status']); ?>
                      </span>
                    </div>
                  <?php endforeach; ?>
                  <?php if (count($credentials) > 5): ?>
                    <a href="credentials.php" class="credential-item">
                      <span class="credential-icon"><i class="fa-solid fa-ellipsis"></i></span>
                      <span>View All (<?php echo count($credentials); ?>) Credentials</span>
                    </a>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="credential-item">
                    <span class="credential-icon"><i class="fa-solid fa-info-circle"></i></span>
                    <span>No credentials found. Add your credentials to complete your profile.</span>
                    <a href="credentials.php" class="credential-status">
                      <i class="fa-solid fa-plus"></i> Add
                    </a>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <p class="error">Faculty information not found. Please contact the administrator.</p>
        <?php endif; ?>


        <div class="stats">
          <div class="stat-box">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-box-title">Profile Last Updated</div>
            <div class="stat-box-value"><?php echo $last_updated_display; ?></div>
          </div>
          <div class="stat-box">
            <div class="stat-icon"><i class="fa-solid fa-database"></i></div>
            <div class="stat-box-title">Your Profile Records</div>
            <div class="stat-box-value"><?php echo $record_count; ?></div>
          </div>
          <div class="stat-box">
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="stat-box-title">Pending Credentials</div>
            <div class="stat-box-value"><?php echo $pending_submissions; ?></div>
          </div>
        </div>

        <h3>Quick Actions</h3>
        <div class="quick-actions">
          <div class="action-card" onclick="window.location.href='profile.php'">
            <div class="action-icon"><i class="fa-solid fa-user-pen"></i></div>
            <div class="action-title">Update Profile</div>
            <div class="action-description">Maintain your faculty profile for CHED compliance</div>
          </div>
          <div class="action-card" onclick="window.location.href='credentials.php'">
            <div class="action-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
            <div class="action-title">Add Credential</div>
            <div class="action-description">Add new academic or professional credentials</div>
          </div>
          <div class="action-card" onclick="window.location.href='documents.php'">
            <div class="action-icon"><i class="fa-solid fa-upload"></i></div>
            <div class="action-title">Upload Documents</div>
            <div class="action-description">Submit supporting documentation for your credentials</div>
          </div>
          <div class="action-card" onclick="window.location.href='ched_compliance.php'">
            <div class="action-icon"><i class="fa-solid fa-list-check"></i></div>
            <div class="action-title">CHED Compliance</div>
            <div class="action-description">Check your current CHED compliance status</div>
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

  <script>
    // Add fade-in animations to elements
    $(document).ready(function () {
      // Faculty profile card animation
      $('.faculty-profile-card').addClass('slide-in-left');
      $('.faculty-profile-card').css('animation-delay', '0.1s');

      // Apply animations to credential items
      $('.credential-item').each(function (i) {
        $(this).addClass('slide-in-left');
        $(this).css('animation-delay', (i * 0.05 + 0.2) + 's');
      });

      // Original animations
      $('.stat-box').addClass('slide-in-left');
      $('.stat-box').each(function (i) {
        $(this).css('animation-delay', (i * 0.1 + 0.3) + 's');
      });

      $('.action-card').addClass('slide-in-right');
      $('.action-card').each(function (i) {
        $(this).css('animation-delay', (i * 0.1 + 0.4) + 's');
      });

      $('.btn').addClass('slide-in-left');
      $('.btn').each(function (i) {
        $(this).css('animation-delay', (i * 0.1 + 0.5) + 's');
      });

      $('.alert').addClass('slide-in-right');
      $('.alert').css('animation-delay', '0.7s');
    });
  </script>
</body>

</html>