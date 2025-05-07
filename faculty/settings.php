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

// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'] ?? 1; // Default for testing

// Process form submissions
$updateMessage = '';
$passwordMessage = '';
$errorMessage = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
  $first_name = $_POST['first_name'] ?? '';
  $last_name = $_POST['last_name'] ?? '';
  $email = $_POST['email'] ?? '';

  // Validate inputs
  if (empty($first_name) || empty($last_name) || empty($email)) {
    $errorMessage = "Please fill all required fields.";
  } else {
    // Update faculty information
    $updateQuery = "UPDATE faculty SET 
                first_name = ?, 
                last_name = ?, 
                email = ? 
                WHERE faculty_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $faculty_id);

    if ($stmt->execute()) {
      $updateMessage = "Profile updated successfully!";
    } else {
      $errorMessage = "Error updating profile: " . $conn->error;
    }
    $stmt->close();
  }
}

// Handle password change
if (isset($_POST['change_password'])) {
  $current_password = $_POST['current_password'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  // Validate password
  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $errorMessage = "Please fill all password fields.";
  } elseif ($new_password !== $confirm_password) {
    $errorMessage = "New passwords do not match.";
  } elseif (strlen($new_password) < 12 || strlen($new_password) > 16) {
    $errorMessage = "Password must be between 12-16 characters.";
  } else {
    // Get current hashed password from database
    $passwordQuery = "SELECT password_hash FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($passwordQuery);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
      $current_hashed = $row['password'];

      // Verify current password
      if (password_verify($current_password, $current_hashed)) {
        // Hash new password
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $updatePasswordQuery = "UPDATE faculty SET password = ? WHERE faculty_id = ?";
        $stmt = $conn->prepare($updatePasswordQuery);
        $stmt->bind_param("si", $new_hashed, $faculty_id);

        if ($stmt->execute()) {
          $passwordMessage = "Password changed successfully!";
        } else {
          $errorMessage = "Error changing password: " . $conn->error;
        }
      } else {
        $errorMessage = "Current password is incorrect.";
      }
    } else {
      $errorMessage = "Error retrieving user information.";
    }
    $stmt->close();
  }
}

// Handle profile image upload
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
  $target_dir = "../uploads/";

  // Create directory if it doesn't exist
  if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  $imageFileType = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
  $target_file = $target_dir . "faculty_" . $faculty_id . "_" . time() . "." . $imageFileType;

  // Check if image file is a actual image
  $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
  if ($check !== false) {
    // Allow certain file formats
    if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
      if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        // Update profile image in database
        $updateImageQuery = "UPDATE faculty SET profile_image = ? WHERE faculty_id = ?";
        $stmt = $conn->prepare($updateImageQuery);
        $relativeImagePath = str_replace("../", "", $target_file);
        $stmt->bind_param("si", $relativeImagePath, $faculty_id);

        if ($stmt->execute()) {
          $updateMessage = "Profile image updated successfully!";
        } else {
          $errorMessage = "Error updating profile image in database: " . $conn->error;
        }
        $stmt->close();
      } else {
        $errorMessage = "Error uploading file.";
      }
    } else {
      $errorMessage = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
  } else {
    $errorMessage = "File is not an image.";
  }
}

// Get faculty information
$faculty_query = "SELECT first_name, last_name, email, profile_image FROM faculty WHERE faculty_id = ?";
$stmt = $conn->prepare($faculty_query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($faculty = $result->fetch_assoc()) {
  $first_name = $faculty['first_name'] ?? '';
  $last_name = $faculty['last_name'] ?? '';
  $email = $faculty['email'] ?? '';
  $profile_image = $faculty['profile_image'] ?? '';
} else {
  $errorMessage = "Error retrieving faculty information.";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings - FPMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Additional settings-specific styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* Settings-specific styles */
    .settings-header {
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

    .settings-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .settings-info {
      position: relative;
      z-index: 1;
    }

    .settings-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .settings-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Settings sections */
    .settings-section {
      background-color: white;
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-sm);
      padding: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
      transition: all var(--transition-normal);
      position: relative;
      border-left: 4px solid var(--primary-color);
    }

    .settings-section:hover {
      box-shadow: var(--shadow-md);
      transform: translateY(-3px);
    }

    .settings-section h3 {
      color: var(--primary-color);
      font-size: 1.3rem;
      margin-bottom: var(--spacing-lg);
      display: flex;
      align-items: center;
    }

    .settings-section h3 i {
      margin-right: var(--spacing-md);
      font-size: 1.5rem;
      color: var(--secondary-color);
      transition: transform var(--transition-fast);
    }

    .settings-section:hover h3 i {
      transform: scale(1.2);
    }

    /* Form styles */
    .form-row {
      display: flex;
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-md);
    }

    .form-row>div {
      flex: 1;
    }

    label {
      display: block;
      margin-bottom: var(--spacing-xs);
      color: var(--dark-gray);
      font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--medium-gray);
      border-radius: var(--border-radius-sm);
      margin-bottom: var(--spacing-md);
      font-family: inherit;
      transition: all 0.3s;
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    input:focus,
    textarea:focus {
      outline: none;
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 2px rgba(117, 217, 121, 0.2);
    }

    .required {
      color: var(--error-color);
      font-size: 0.8rem;
      margin-left: var(--spacing-xs);
    }

    /* Password strength */
    .password-strength {
      margin-top: -10px;
      margin-bottom: var(--spacing-md);
      font-size: 0.8rem;
    }

    .password-strength-meter {
      height: 5px;
      background-color: var(--light-gray);
      border-radius: 10px;
      margin-top: 5px;
      overflow: hidden;
    }

    .password-strength-meter div {
      height: 100%;
      width: 0;
      transition: width 0.3s;
    }

    .password-weak {
      background-color: #ff4d4d;
    }

    .password-medium {
      background-color: #ffcc00;
    }

    .password-strong {
      background-color: var(--secondary-color);
    }

    /* Profile image */
    .profile-image-container {
      display: flex;
      align-items: center;
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-lg);
    }

    .current-image {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary-light);
    }

    .upload-container {
      flex: 1;
    }

    .image-preview {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      display: none;
      border: 3px solid var(--primary-light);
    }

    .custom-file-input {
      position: relative;
      overflow: hidden;
      display: inline-block;
      cursor: pointer;
    }

    .custom-file-input input[type="file"] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      cursor: pointer;
      width: 100%;
      height: 100%;
    }

    /* Messages */
    .success-message {
      background-color: rgba(117, 217, 121, 0.2);
      color: var(--primary-color);
      padding: var(--spacing-md);
      border-radius: var(--border-radius-sm);
      margin-bottom: var(--spacing-md);
    }

    .error-message {
      background-color: rgba(255, 77, 77, 0.2);
      color: var(--error-color);
      padding: var(--spacing-md);
      border-radius: var(--border-radius-sm);
      margin-bottom: var(--spacing-md);
    }

    /* Buttons */
    button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: var(--border-radius-sm);
      cursor: pointer;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-sm);
      transition: all 0.3s;
      font-family: inherit;
    }

    button:hover {
      background-color: var(--primary-dark);
      transform: translateY(-1px);
    }

    button i {
      font-size: 1.1rem;
    }

    /* Sidebar and responsive styles */
    @media (max-width: 768px) {
      .form-row {
        flex-direction: column;
        gap: 0;
      }

      .settings-header {
        flex-direction: column;
        text-align: center;
      }

      .settings-info {
        margin-bottom: var(--spacing-md);
      }

      .profile-image-container {
        flex-direction: column;
        align-items: flex-start;
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
        <a href="ched_compliance.php"><i class="fa-solid fa-list-check"></i> CHED Compliance</a>
        <a href="settings.php" class="active"><i class="fa-solid fa-gear"></i> Settings</a>
        <a href="logout.php"><i class="fa-solid fa-door-open"></i> Logout</a>
      </nav>
    </div>

    <div class="content">
      <div class="header">
        <div class="user-info">
          <i class="fa-solid fa-circle-user"></i> Welcome, <?php echo $first_name . ' ' . $last_name; ?>! -
          Faculty
        </div>
      </div>

      <div class="main-content">
        <!-- Settings Header Banner -->
        <div class="settings-header slide-in-left">
          <div class="settings-info">
            <div class="settings-title">Account Settings</div>
            <div class="settings-subtitle">Manage your personal information and security preferences</div>
          </div>
        </div>

        <!-- Messages Display -->
        <?php if (!empty($updateMessage)): ?>
          <div class="success-message">
            <i class="fa-solid fa-check-circle"></i> <?php echo $updateMessage; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($passwordMessage)): ?>
          <div class="success-message">
            <i class="fa-solid fa-check-circle"></i> <?php echo $passwordMessage; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
          <div class="error-message">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $errorMessage; ?>
          </div>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="settings-section">
          <h3><i class="fa-solid fa-user-pen"></i> Edit Profile Information</h3>

          <form action="settings.php" method="POST" enctype="multipart/form-data">
            <!-- Profile Image -->
            <div class="profile-image-container">
              <img
                src="<?php echo !empty($profile_image) ? '../' . $profile_image : '../assets/profile-default.jpg'; ?>"
                alt="Profile Image" class="current-image" id="currentImage">
              <div class="upload-container">
                <img id="imagePreview" src="#" alt="Image Preview" class="image-preview">
                <div>
                  <label for="profile_image">Profile Picture</label>
                  <div class="custom-file-input">
                    <button type="button">
                      <i class="fa-solid fa-upload"></i> Choose Image
                    </button>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*">
                  </div>
                  <p class="form-hint">Recommended: Square image, 300x300px or larger</p>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div>
                <label for="first_name">First Name <span class="required">*</span></label>
                <input type="text" id="first_name" name="first_name"
                  value="<?php echo htmlspecialchars($first_name); ?>" required>
              </div>
              <div>
                <label for="last_name">Last Name <span class="required">*</span></label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>"
                  required>
              </div>
            </div>

            <div class="form-row">
              <div>
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
              </div>
              <div>
              </div>


              <button type="submit" name="update_profile">
                <i class="fa-solid fa-save"></i> Save Changes
              </button>
          </form>
        </div>

        <!-- Security Section -->
        <div class="settings-section">
          <h3><i class="fa-solid fa-lock"></i> Security Settings</h3>

          <form action="settings.php" method="POST">
            <div>
              <label for="current_password">Current Password <span class="required">*</span></label>
              <input type="password" id="current_password" name="current_password" required>
            </div>

            <div>
              <label for="new_password">New Password <span class="required">*</span> (12-16 characters)</label>
              <input type="password" id="new_password" name="new_password" minlength="12" maxlength="16" required>
              <div class="password-strength">
                <span id="passwordStrengthText">Password strength: Not entered</span>
                <div class="password-strength-meter">
                  <div id="passwordStrengthBar"></div>
                </div>
              </div>
            </div>

            <div>
              <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
              <input type="password" id="confirm_password" name="confirm_password" minlength="12" maxlength="16"
                required>
              <div id="passwordMatch"></div>
            </div>

            <button type="submit" name="change_password">
              <i class="fa-solid fa-key"></i> Change Password
            </button>
          </form>
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
    // Profile image preview
    document.getElementById('profile_image').addEventListener('change', function (e) {
      const preview = document.getElementById('imagePreview');
      const currentImage = document.getElementById('currentImage');
      const file = e.target.files[0];

      if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
          currentImage.style.display = 'none';
        }

        reader.readAsDataURL(file);
      }
    });

    // Password strength checker
    document.getElementById('new_password').addEventListener('input', function () {
      const password = this.value;
      const strengthBar = document.getElementById('passwordStrengthBar');
      const strengthText = document.getElementById('passwordStrengthText');

      if (password.length === 0) {
        strengthBar.style.width = '0';
        strengthBar.className = '';
        strengthText.textContent = 'Password strength: Not entered';
        return;
      }

      // Check strength
      let strength = 0;

      // Check length
      if (password.length >= 12) strength += 25;

      // Check lowercase letters
      if (password.match(/[a-z]/)) strength += 15;

      // Check uppercase letters
      if (password.match(/[A-Z]/)) strength += 15;

      // Check numbers
      if (password.match(/[0-9]/)) strength += 15;

      // Check special characters
      if (password.match(/[^a-zA-Z0-9]/)) strength += 30;

      // Update UI
      strengthBar.style.width = strength + '%';

      if (strength < 40) {
        strengthBar.className = 'password-weak';
        strengthText.textContent = 'Password strength: Weak';
      } else if (strength < 70) {
        strengthBar.className = 'password-medium';
        strengthText.textContent = 'Password strength: Medium';
      } else {
        strengthBar.className = 'password-strong';
        strengthText.textContent = 'Password strength: Strong';
      }
    });

    // Check password match
    document.getElementById('confirm_password').addEventListener('input', function () {
      const password = document.getElementById('new_password').value;
      const confirmPassword = this.value;
      const matchText = document.getElementById('passwordMatch');

      if (confirmPassword.length === 0) {
        matchText.textContent = '';
        return;
      }

      if (password === confirmPassword) {
        matchText.textContent = 'Passwords match ✓';
        matchText.style.color = 'var(--secondary-color)';
      } else {
        matchText.textContent = 'Passwords do not match ✗';
        matchText.style.color = 'var(--error-color)';
      }
    });
  </script>
</body>

</html>