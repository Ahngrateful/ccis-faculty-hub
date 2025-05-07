<?php
// Start session
session_start();
// Database connection
require_once("dbconn.php");

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // For AJAX requests, return JSON error
    if (isset($_GET['action']) && $_GET['action'] == 'get_faculty') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not authenticated']);
        exit();
    }

    // For regular requests, redirect
    header("Location: admin-login.php");
    exit();
}

// Initialize messages
$error_message = "";
$success_message = "";

// Handle GET requests (AJAX)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    if ($_GET['action'] == 'get_faculty' && isset($_GET['faculty_id'])) {
        $faculty_id = mysqli_real_escape_string($conn, $_GET['faculty_id']);

        // Query to get faculty data
        $query = "SELECT f.*, r.role_name
                  FROM faculty f
                  JOIN roles r ON f.role_id = r.roles_id
                  WHERE f.faculty_id = ?";

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $faculty_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                // Return faculty data as JSON
                header('Content-Type: application/json');
                echo json_encode($row);
                exit();
            } else {
                // Faculty not found
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Faculty not found']);
                exit();
            }

            mysqli_stmt_close($stmt);
        } else {
            // Database error
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error']);
            exit();
        }
    }
}

// Handle direct status update (non-AJAX fallback)
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['faculty_id'])) {
    $faculty_id = mysqli_real_escape_string($conn, $_GET['faculty_id']);

    // Get current status
    $query = "SELECT status FROM faculty WHERE faculty_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $faculty_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $current_status);

        if (mysqli_stmt_fetch($stmt)) {
            // Toggle status
            $new_status = ($current_status == 'active') ? 'inactive' : 'active';
            mysqli_stmt_close($stmt);

            // Update status
            $update_query = "UPDATE faculty SET status = ?, updated_at = NOW() WHERE faculty_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);

            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "ss", $new_status, $faculty_id);

                if (mysqli_stmt_execute($update_stmt)) {
                    $success_message = "Faculty status updated to " . ucfirst($new_status);
                } else {
                    $error_message = "Error updating faculty status: " . mysqli_error($conn);
                }

                mysqli_stmt_close($update_stmt);
            } else {
                $error_message = "Database error: Failed to prepare update statement";
            }
        } else {
            mysqli_stmt_close($stmt);
            $error_message = "Faculty not found";
        }
    } else {
        $error_message = "Database error: Failed to prepare statement";
    }

    // Store messages in session
    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
    }
    if (!empty($success_message)) {
        $_SESSION['success_message'] = $success_message;
    }

    // Redirect back to faculty management page
    header("Location: faculty_management.php");
    exit();
}

// Process form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_GET['action'] ?? '';

    // Handle AJAX status update
    if ($action == 'update_status' && isset($_POST['faculty_id']) && isset($_POST['status'])) {
        $faculty_id = mysqli_real_escape_string($conn, $_POST['faculty_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        // Validate status
        if ($status != 'active' && $status != 'inactive') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid status value']);
            exit();
        }

        // Check if faculty exists
        $check_query = "SELECT faculty_id FROM faculty WHERE faculty_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);

        if (!$check_stmt) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: Failed to prepare check statement']);
            exit();
        }

        mysqli_stmt_bind_param($check_stmt, "s", $faculty_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) == 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Faculty not found']);
            mysqli_stmt_close($check_stmt);
            exit();
        }

        mysqli_stmt_close($check_stmt);

        // Update faculty status
        $query = "UPDATE faculty SET status = ?, updated_at = NOW() WHERE faculty_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $status, $faculty_id);

            if (mysqli_stmt_execute($stmt)) {
                // Log the status change
                $log_query = "INSERT INTO audit_logs (faculty_id, admin_id, action, details, created_at)
                             VALUES (?, ?, ?, ?, NOW())";
                $log_stmt = mysqli_prepare($conn, $log_query);

                if ($log_stmt) {
                    $admin_id = $_SESSION['admin_id'] ?? null;
                    $action = $status == 'active' ? 'activate_faculty' : 'deactivate_faculty';
                    $details = "Faculty status changed to " . ucfirst($status);

                    mysqli_stmt_bind_param($log_stmt, "ssss", $faculty_id, $admin_id, $action, $details);
                    mysqli_stmt_execute($log_stmt);
                    mysqli_stmt_close($log_stmt);
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Faculty status updated successfully to ' . ucfirst($status)
                ]);

                mysqli_stmt_close($stmt);
                exit();
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
                mysqli_stmt_close($stmt);
                exit();
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error: Failed to prepare statement']);
            exit();
        }
    }

    // Add new faculty
    if ($action == 'add') {
        // Check if it's an AJAX request
        $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Get form data
        $faculty_id = mysqli_real_escape_string($conn, $_POST['faculty_id'] ?? '');
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $account_creation_date = mysqli_real_escape_string($conn, $_POST['account_creation_date'] ?? '');
        $role_id = mysqli_real_escape_string($conn, $_POST['role_id'] ?? '');
        $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

        // Initialize errors array
        $errors = [];

        // Validate inputs
        if (empty($faculty_id)) {
            $errors[] = "Faculty ID is required.";
        } elseif (!preg_match('/^[A-Za-z0-9-]+$/', $faculty_id)) {
            $errors[] = "Faculty ID can only contain letters, numbers, and hyphens.";
        } else {
            // Check if faculty ID already exists
            $check_query = "SELECT faculty_id FROM faculty WHERE faculty_id = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);

            if ($check_stmt) {
                mysqli_stmt_bind_param($check_stmt, "s", $faculty_id);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_store_result($check_stmt);

                if (mysqli_stmt_num_rows($check_stmt) > 0) {
                    $errors[] = "Faculty ID already exists. Please use a different ID.";
                }

                mysqli_stmt_close($check_stmt);
            }
        }

        if (empty($first_name)) {
            $errors[] = "First name is required.";
        } elseif (!preg_match('/^[A-Za-z\s\'-]+$/', $first_name)) {
            $errors[] = "First name can only contain letters, spaces, apostrophes, and hyphens.";
        }

        if (empty($last_name)) {
            $errors[] = "Last name is required.";
        } elseif (!preg_match('/^[A-Za-z\s\'-]+$/', $last_name)) {
            $errors[] = "Last name can only contain letters, spaces, apostrophes, and hyphens.";
        }

        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } else {
            // Check if email already exists
            $check_query = "SELECT email FROM faculty WHERE email = ? AND faculty_id != ?";
            $check_stmt = mysqli_prepare($conn, $check_query);

            if ($check_stmt) {
                mysqli_stmt_bind_param($check_stmt, "ss", $email, $faculty_id);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_store_result($check_stmt);

                if (mysqli_stmt_num_rows($check_stmt) > 0) {
                    $errors[] = "Email already exists. Please use a different email.";
                }

                mysqli_stmt_close($check_stmt);
            }
        }

        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }

        if (empty($account_creation_date)) {
            $errors[] = "Account creation date is required.";
        }

        if (empty($role_id)) {
            $errors[] = "Role is required.";
        }

        if (empty($status)) {
            $errors[] = "Status is required.";
        } elseif ($status != 'Active' && $status != 'Inactive') {
            $errors[] = "Status must be either Active or Inactive.";
        }

        // If there are validation errors
        if (!empty($errors)) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit();
            } else {
                $error_message = implode("<br>", $errors);
            }
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Handle profile image upload
            $profile_image = null;
            $upload_error = null;

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $upload_dir = "../uploads/profile_images/";

                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Get file info
                $file_info = pathinfo($_FILES['profile_image']['name']);
                $file_extension = strtolower($file_info['extension']);

                // Check file type
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($file_extension, $allowed_extensions)) {
                    $upload_error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
                } else {
                    // Generate unique filename
                    $file_name = time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $file_name;

                    // Move uploaded file
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        $profile_image = $target_file;
                    } else {
                        $upload_error = "Failed to upload image. Please try again.";
                    }
                }
            }

            if ($upload_error) {
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'errors' => [$upload_error]]);
                    exit();
                } else {
                    $error_message = $upload_error;
                }
            } else {
                // Convert status to lowercase for database consistency
                $status = strtolower($status);

                // Insert new faculty
                $query = "INSERT INTO faculty (faculty_id, first_name, last_name, email, password_hash,
                          account_creation_date, role_id, status, created_at, updated_at, profile_image)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)";

                $stmt = mysqli_prepare($conn, $query);

                if ($stmt) {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssssssss",
                        $faculty_id,
                        $first_name,
                        $last_name,
                        $email,
                        $password_hash,
                        $account_creation_date,
                        $role_id,
                        $status,
                        $profile_image
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        // Log the action
                        $log_query = "INSERT INTO audit_logs (faculty_id, admin_id, action, details, created_at)
                                     VALUES (?, ?, ?, ?, NOW())";
                        $log_stmt = mysqli_prepare($conn, $log_query);

                        if ($log_stmt) {
                            $admin_id = $_SESSION['admin_id'] ?? null;
                            $action_type = 'add_faculty';
                            $details = "Added new faculty member: $first_name $last_name ($faculty_id)";

                            mysqli_stmt_bind_param($log_stmt, "ssss", $faculty_id, $admin_id, $action_type, $details);
                            mysqli_stmt_execute($log_stmt);
                            mysqli_stmt_close($log_stmt);
                        }

                        if ($is_ajax) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'message' => "Faculty added successfully.",
                                'faculty' => [
                                    'faculty_id' => $faculty_id,
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'email' => $email,
                                    'account_creation_date' => $account_creation_date,
                                    'role_id' => $role_id,
                                    'status' => $status,
                                    'profile_image' => $profile_image
                                ]
                            ]);
                            exit();
                        } else {
                            $success_message = "Faculty added successfully.";
                        }
                    } else {
                        $db_error = mysqli_error($conn);

                        if ($is_ajax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'errors' => ["Database error: $db_error"]]);
                            exit();
                        } else {
                            $error_message = "Error adding faculty: $db_error";
                        }
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    if ($is_ajax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'errors' => ["Database error: Failed to prepare statement."]]);
                        exit();
                    } else {
                        $error_message = "Database error: Failed to prepare statement.";
                    }
                }
            }
        }
    }

    // Edit faculty
    elseif ($action == 'edit') {
        // Get form data
        $faculty_id = mysqli_real_escape_string($conn, $_POST['faculty_id'] ?? '');
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
        $account_creation_date = mysqli_real_escape_string($conn, $_POST['account_creation_date'] ?? '');
        $role_id = mysqli_real_escape_string($conn, $_POST['role_id'] ?? '');
        $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

        // Validate inputs
        if (
            empty($faculty_id) || empty($first_name) || empty($last_name) || empty($email) ||
            empty($account_creation_date) || empty($role_id) || empty($status)
        ) {
            $error_message = "All fields are required.";
        } else {
            // Handle profile image upload
            $profile_image = null;
            $has_new_image = false;

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $upload_dir = "../uploads/profile_images/";

                // Create directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
                $target_file = $upload_dir . $file_name;

                // Move uploaded file
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $profile_image = $target_file;
                    $has_new_image = true;
                }
            }

            // Prepare the query based on whether we have a new image
            if ($has_new_image) {
                $query = "UPDATE faculty SET
                          first_name = ?,
                          last_name = ?,
                          email = ?,
                          account_creation_date = ?,
                          role_id = ?,
                          status = ?,
                          profile_image = ?,
                          updated_at = NOW()
                          WHERE faculty_id = ?";

                $stmt = mysqli_prepare($conn, $query);

                if ($stmt) {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssssssss", // 8 parameters
                        $first_name,
                        $last_name,
                        $email,
                        $account_creation_date,
                        $role_id,
                        $status,
                        $profile_image,
                        $faculty_id
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Faculty updated successfully.";
                    } else {
                        $error_message = "Error updating faculty: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $error_message = "Database error: Failed to prepare statement.";
                }
            } else {
                $query = "UPDATE faculty SET
                          first_name = ?,
                          last_name = ?,
                          email = ?,
                          account_creation_date = ?,
                          role_id = ?,
                          status = ?,
                          updated_at = NOW()
                          WHERE faculty_id = ?";

                $stmt = mysqli_prepare($conn, $query);

                if ($stmt) {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssssss", // 7 parameters
                        $first_name,
                        $last_name,
                        $email,
                        $account_creation_date,
                        $role_id,
                        $status,
                        $faculty_id
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $success_message = "Faculty updated successfully.";
                    } else {
                        $error_message = "Error updating faculty: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $error_message = "Database error: Failed to prepare statement.";
                }
            }
        }
    }
}

// Store messages in session
if (!empty($error_message)) {
    $_SESSION['error_message'] = $error_message;
}
if (!empty($success_message)) {
    $_SESSION['success_message'] = $success_message;
}

// Redirect back to faculty management page
header("Location: faculty_management.php");
exit();
?>