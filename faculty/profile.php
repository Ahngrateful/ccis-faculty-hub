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
  <title>Profile Management - FPMS</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <!-- Google Fonts - Optional for better typography -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <!-- Additional profile-specific styles -->
  <style>
    body {
      font-family: 'Inter', var(--font-family);
    }

    /* Profile-specific styles */
    .profile-header {
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

    .profile-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
    }

    .profile-info {
      position: relative;
      z-index: 1;
    }

    .profile-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-sm);
      font-weight: 700;
    }

    .profile-subtitle {
      opacity: 0.9;
      font-size: 1rem;
    }

    /* Enhanced form sections */
    .form-section {
      background-color: white;
      border-radius: var(--border-radius-md);
      box-shadow: var(--shadow-sm);
      padding: var(--spacing-lg);
      margin-bottom: var(--spacing-lg);
      transition: all var(--transition-normal);
      position: relative;
      border-left: 4px solid var(--primary-color);
    }

    .form-section:hover {
      box-shadow: var(--shadow-md);
      transform: translateY(-3px);
    }

    .form-section h3 {
      color: var(--primary-color);
      font-size: 1.3rem;
      margin-bottom: var(--spacing-md);
      display: flex;
      align-items: center;
    }

    .form-section h3 i {
      margin-right: var(--spacing-md);
      font-size: 1.5rem;
      color: var(--secondary-color);
      transition: transform var(--transition-fast);
    }

    .form-section:hover h3 i {
      transform: scale(1.2);
    }

    .required-label {
      color: var(--error-color);
      font-size: 0.8rem;
      font-weight: 500;
      margin-left: var(--spacing-sm);
      background-color: rgba(244, 67, 54, 0.1);
      padding: 2px 6px;
      border-radius: var(--border-radius-sm);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--dark-gray);
    }

    input[type="text"],
    input[type="email"],
    input[type="date"],
    textarea,
    select {
      width: 100%;
      padding: 12px;
      border-radius: 4px;
      border: 1px solid #ddd;
      margin-bottom: 15px;
      font-family: inherit;
      font-size: 15px;
      transition: border-color 0.3s;
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    input:focus,
    textarea:focus,
    select:focus {
      outline: none;
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 2px rgba(117, 217, 121, 0.2);
    }

    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
    }

    .form-row>div {
      flex: 1;
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid var(--medium-gray);
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

    button.secondary {
      background-color: #fff;
      color: var(--primary-color);
      border: 1px solid var(--primary-color);
    }

    button.secondary:hover {
      background-color: var(--light-gray);
    }

    .progress-bar {
      margin-bottom: 30px;
      background-color: var(--medium-gray);
      border-radius: 10px;
      height: 10px;
      overflow: hidden;
    }

    .progress {
      height: 100%;
      background-color: var(--secondary-color);
      width: 12.5%;
      transition: width 0.3s ease;
    }

    .progress-steps {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .step {
      text-align: center;
      flex: 1;
      position: relative;
      color: var(--dark-gray);
      font-size: 14px;
    }

    .step.active {
      color: var(--primary-color);
      font-weight: 500;
    }

    .step.completed {
      color: var(--success-color);
    }

    .step:not(:last-child):after {
      content: "";
      position: absolute;
      top: 10px;
      left: 50%;
      width: 100%;
      height: 2px;
      background-color: var(--medium-gray);
      z-index: -1;
    }

    .step.active:not(:last-child):after,
    .step.completed:not(:last-child):after {
      background-color: var(--secondary-color);
    }

    .step-number {
      display: inline-block;
      width: 20px;
      height: 20px;
      line-height: 20px;
      border-radius: 50%;
      background-color: var(--medium-gray);
      color: #fff;
      margin-bottom: 5px;
      font-size: 12px;
    }

    .step.active .step-number {
      background-color: var(--primary-color);
    }

    .step.completed .step-number {
      background-color: var(--success-color);
    }

    .save-status {
      margin-top: 20px;
      padding: 10px;
      border-radius: 4px;
      display: none;
    }

    .save-status.success {
      display: block;
      background-color: rgba(76, 175, 80, 0.1);
      color: var(--success-color);
      border: 1px solid var(--success-color);
    }

    .save-status.error {
      display: block;
      background-color: rgba(244, 67, 54, 0.1);
      color: var(--error-color);
      border: 1px solid var(--error-color);
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

      .form-row {
        flex-direction: column;
        gap: 0;
      }

      .progress-steps {
        display: none;
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
        <a href="profile.php" class="active"><i class="fa-solid fa-user"></i> Profile</a>
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
          <i class="fa-solid fa-circle-user"></i> Welcome, Prof. Sharleen
          Olaguir - Faculty
        </div>
      </div>

      <div class="main-content">
        <h2>Manage Profile for CHED Compliance</h2>

        <!-- Progress indicator -->
        <div class="progress-bar">
          <div class="progress" id="progress-bar"></div>
        </div>

        <div class="progress-steps">
          <div class="step active" id="step1">
            <div class="step-number">1</div>
            <div>Personal Info</div>
          </div>
          <div class="step" id="step2">
            <div class="step-number">2</div>
            <div>Education</div>
          </div>
          <div class="step" id="step3">
            <div class="step-number">3</div>
            <div>Experience</div>
          </div>
          <div class="step" id="step4">
            <div class="step-number">4</div>
            <div>Teaching</div>
          </div>
          <div class="step" id="step5">
            <div class="step-number">5</div>
            <div>Research</div>
          </div>
          <div class="step" id="step6">
            <div class="step-number">6</div>
            <div>Seminars</div>
          </div>
          <div class="step" id="step7">
            <div class="step-number">7</div>
            <div>Awards</div>
          </div>
          <div class="step" id="step8">
            <div class="step-number">8</div>
            <div>Licenses</div>
          </div>
        </div>

        <div id="save-status" class="save-status"></div>

        <!-- Form Pages (Step-by-Step) -->
        <div class="form-container active" id="personal-info">
          <div class="form-section">
            <h3>
              <i class="fa-solid fa-id-card"></i> Personal Information
              <span class="required-label">(Required for CHED)</span>
            </h3>

            <div class="form-row">
              <div>
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" placeholder="Enter your full name" />
              </div>
              <div>
                <label for="birthdate">Date of Birth</label>
                <input type="date" id="birthdate" />
              </div>
            </div>

            <div class="form-row">
              <div>
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="Enter your email" />
              </div>
              <div>
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" placeholder="Enter your phone number" />
              </div>
            </div>

            <label for="address">Address</label>
            <textarea id="address" placeholder="Enter your complete address"></textarea>

            <label for="bio">Professional Bio</label>
            <textarea id="bio" placeholder="Brief professional biography (max 200 words)"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" disabled>Previous</button>
            <button type="button" onclick="nextPage('educational-background')">
              Next
            </button>
          </div>
        </div>

        <div class="form-container" id="educational-background">
          <div class="form-section">
            <h3>
              <i class="fa-solid fa-graduation-cap"></i> Educational
              Background
            </h3>

            <label for="undergrad">Undergraduate Degree</label>
            <input type="text" id="undergrad" placeholder="Degree, Institution, Year" />

            <label for="graduate">Graduate Degree(s)</label>
            <textarea id="graduate" placeholder="List your graduate degrees with details"></textarea>

            <label for="doctorate">Doctorate (if applicable)</label>
            <input type="text" id="doctorate" placeholder="Degree, Institution, Year" />

            <label for="other-education">Other Educational Qualifications</label>
            <textarea id="other-education" placeholder="Special training, certifications, etc."></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('personal-info')">
              Previous
            </button>
            <button type="button" onclick="nextPage('work-experience')">
              Next
            </button>
          </div>
        </div>

        <div class="form-container" id="work-experience">
          <div class="form-section">
            <h3><i class="fa-solid fa-briefcase"></i> Work Experience</h3>

            <label for="current-position">Current Position</label>
            <input type="text" id="current-position" placeholder="Position, Institution, Dates" />

            <label for="previous-positions">Previous Positions</label>
            <textarea id="previous-positions" placeholder="List your previous positions with details"></textarea>

            <label for="industry-experience">Industry Experience (if applicable)</label>
            <textarea id="industry-experience" placeholder="Relevant industry experience"></textarea>

            <label for="administrative-roles">Administrative Roles</label>
            <textarea id="administrative-roles" placeholder="Any administrative positions held"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('educational-background')">
              Previous
            </button>
            <button type="button" onclick="nextPage('teaching-assignments')">
              Next
            </button>
          </div>
        </div>

        <div class="form-container" id="teaching-assignments">
          <div class="form-section">
            <h3>
              <i class="fa-solid fa-chalkboard-user"></i> Teaching Assignments
            </h3>

            <label for="current-courses">Current Courses</label>
            <textarea id="current-courses" placeholder="Courses currently teaching"></textarea>

            <label for="previous-courses">Previous Courses</label>
            <textarea id="previous-courses" placeholder="Courses previously taught"></textarea>

            <label for="specializations">Teaching Specializations</label>
            <textarea id="specializations" placeholder="Areas of teaching specialization"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('work-experience')">
              Previous
            </button>
            <button type="button" onclick="nextPage('research-output')">
              Next
            </button>
          </div>
        </div>

        <div class="form-container" id="research-output">
          <div class="form-section">
            <h3><i class="fa-solid fa-flask"></i> Research Output</h3>

            <label for="publications">Publications</label>
            <textarea id="publications"
              placeholder="List your publications (format: Author(s), Title, Journal/Conference, Date)"></textarea>

            <label for="research-projects">Research Projects</label>
            <textarea id="research-projects" placeholder="Current and past research projects"></textarea>

            <label for="patents">Patents or Intellectual Property</label>
            <textarea id="patents" placeholder="Any patents or IP developed"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('teaching-assignments')">
              Previous
            </button>
            <button type="button" onclick="nextPage('seminars-trainings')">
              Next
            </button>
          </div>
        </div>

        <div class="form-container" id="seminars-trainings">
          <div class="form-section">
            <h3>
              <i class="fa-solid fa-certificate"></i> Seminars/Trainings
            </h3>

            <label for="recent-trainings">Recent Professional Development (last 3 years)</label>
            <textarea id="recent-trainings" placeholder="List seminars, workshops, trainings"></textarea>

            <label for="certifications">Certifications Earned</label>
            <textarea id="certifications" placeholder="Professional certifications"></textarea>

            <label for="conferences">Conference Participation</label>
            <textarea id="conferences" placeholder="Conferences attended/presented at"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('research-output')">
              Previous
            </button>
            <button type="button" onclick="nextPage('awards')">Next</button>
          </div>
        </div>

        <div class="form-container" id="awards">
          <div class="form-section">
            <h3><i class="fa-solid fa-trophy"></i> Awards and Honors</h3>

            <label for="academic-awards">Academic Awards</label>
            <textarea id="academic-awards" placeholder="Awards received in academic context"></textarea>

            <label for="professional-awards">Professional Awards</label>
            <textarea id="professional-awards" placeholder="Awards from professional organizations"></textarea>

            <label for="grants">Grants Received</label>
            <textarea id="grants" placeholder="Research or project grants awarded"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('seminars-trainings')">
              Previous
            </button>
            <button type="button" onclick="nextPage('licenses-certifications')">
              Next
            </button>
          </div>
        </div>

        <div class="form-container" id="licenses-certifications">
          <div class="form-section">
            <h3>
              <i class="fa-solid fa-id-badge"></i> Professional
              Licenses/Certifications
              <span class="required-label">(CHED Requirement)</span>
            </h3>

            <label for="prc-license">PRC License (if applicable)</label>
            <input type="text" id="prc-license" placeholder="License number, date issued, expiry" />

            <label for="other-licenses">Other Professional Licenses</label>
            <textarea id="other-licenses" placeholder="List other professional licenses"></textarea>

            <label for="board-certifications">Board Certifications</label>
            <textarea id="board-certifications" placeholder="Specialty board certifications"></textarea>

            <label for="ched-requirements">Other CHED Requirements</label>
            <textarea id="ched-requirements" placeholder="Any other requirements specified by CHED"></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="secondary" onclick="prevPage('awards')">
              Previous
            </button>
            <button type="button" onclick="saveForm()">
              Save for CHED Review
            </button>
          </div>
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
    // Current step tracking
    let currentStep = 1;
    const totalSteps = 8;

    // Form navigation
    function nextPage(pageId) {
      const currentPage = document.querySelector(".form-container.active");
      currentPage.classList.remove("active");
      const nextPage = document.getElementById(pageId);
      nextPage.classList.add("active");

      // Update progress
      currentStep++;
      updateProgress();
    }

    function prevPage(pageId) {
      const currentPage = document.querySelector(".form-container.active");
      currentPage.classList.remove("active");
      const prevPage = document.getElementById(pageId);
      prevPage.classList.add("active");

      // Update progress
      currentStep--;
      updateProgress();
    }

    // Update progress bar and steps
    function updateProgress() {
      // Update progress bar
      const progressPercent = (currentStep / totalSteps) * 100;
      document.getElementById("progress-bar").style.width =
        progressPercent + "%";

      // Update step indicators
      for (let i = 1; i <= totalSteps; i++) {
        const step = document.getElementById("step" + i);
        step.classList.remove("active", "completed");

        if (i < currentStep) {
          step.classList.add("completed");
        } else if (i === currentStep) {
          step.classList.add("active");
        }
      }
    }

    // Form submission
    function saveForm() {
      const statusElement = document.getElementById("save-status");

      // Simulate form submission
      statusElement.textContent = "Saving your profile information...";
      statusElement.className = "save-status";

      setTimeout(() => {
        // Simulate successful save
        statusElement.textContent =
          "Your profile has been successfully saved and submitted for CHED review.";
        statusElement.className = "save-status success";

        // You would typically have AJAX form submission here
        // and handle the response appropriately
      }, 1500);
    }

    // Initialize progress display
    updateProgress();
  </script>
</body>

</html>