<?php
// Start session
session_start();
// Database connection
require_once("dbconn.php");

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}


// Get pending submissions
$pendingSubmissions = [];
$query = "SELECT s.faculty_id, s.requirement_id, s.status, s.updated_at,
                 u.first_name, u.last_name, u.profile_image, u.role_id,
                 r.requirement_name, r.description
          FROM faculty_compliance_status s
          JOIN faculty u ON s.faculty_id = u.faculty_id
          JOIN ched_compliance_requirements r ON s.requirement_id = r.requirement_id
          WHERE s.status = 'pending'
          ORDER BY s.updated_at DESC";



$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Structure the data with nested arrays
        $submission = [
            'id' => $row['requirement_id'],
            'file_path' => $row['file_path'] ?? '',
            'updated_at' => $row['updated_at'],
            'faculty' => [
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'profile_image' => $row['profile_image'],
                'role' => [
                    'role_name' => getRoleName($conn, $row['role_id']),
                ]
            ],
            'requirement' => [
                'requirement_name' => $row['requirement_name'],
                'description' => $row['description']
            ]
        ];
        $pendingSubmissions[] = $submission;
    }
}

// Get recently processed submissions
$recentlyProcessed = [];
$query = "SELECT s.requirement_id, s.status, s.updated_at,
          u.first_name, u.last_name,
          r.requirement_name
          FROM faculty_compliance_status s
          JOIN faculty u ON s.faculty_id = u.faculty_id
          JOIN ched_compliance_requirements r ON s.requirement_id = r.requirement_id
          WHERE s.status IN ('approved', 'rejected')
          ORDER BY s.updated_at DESC
          LIMIT 10";

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Structure the data with nested arrays
        $processed = [
            'id' => $row['requirement_id'],
            'status' => $row['status'],
            'updated_at' => $row['updated_at'],
            'faculty' => [
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name']
            ],
            'requirement' => [
                'requirement_name' => $row['requirement_name']
            ]
        ];
        $recentlyProcessed[] = $processed;
    }
}

// Get list of requirements for filter
$requirements = [];
$query = "SELECT requirement_id, requirement_name FROM ched_compliance_requirements ORDER BY requirement_name";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $requirements[] = $row;
    }
}

// Helper function to get role name
function getRoleName($conn, $role_id)
{
    $query = "SELECT role_name FROM roles WHERE roles_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $role_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['role_name'];
    }

    return 'Faculty';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Admin Dashboard - FPMS</title>
    <style>
        /* Global Styles */
        :root {
            --primary: #006834;
            --primary-light: rgba(0, 104, 52, 0.1);
            --primary-lighter: rgba(0, 104, 52, 0.05);
            --primary-dark: #005229;
            --secondary: #75d979;
            --secondary-light: rgba(117, 217, 121, 0.15);
            --secondary-lighter: rgba(117, 217, 121, 0.05);
            --accent: #ffde26;
            --accent-light: rgba(255, 222, 38, 0.15);
            --danger: #e74c3c;
            --danger-light: rgba(231, 76, 60, 0.1);
            --danger-lighter: rgba(231, 76, 60, 0.05);
            --success: #2ecc71;
            --success-light: rgba(46, 204, 113, 0.1);
            --warning: #f39c12;
            --warning-light: rgba(243, 156, 18, 0.1);
            --info: #3498db;
            --info-light: rgba(52, 152, 219, 0.1);
            --text-dark: #333333;
            --text-medium: #555555;
            --text-light: #666666;
            --text-lighter: #888888;
            --gray-dark: #e0e0e0;
            --gray-medium: #f0f0f0;
            --gray-light: #f9f9f9;
            --white: #ffffff;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
            --shadow-inset: inset 0 2px 5px rgba(0, 0, 0, 0.05);
            --border-radius-sm: 6px;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --border-radius-xl: 24px;
            --border-radius-full: 9999px;
            --transition-fast: all 0.2s ease;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --transition-bounce: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            --font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--gray-light);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Layout */
        .container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-medium);
            border-radius: var(--border-radius-full);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: var(--border-radius-full);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
            color: white;
            padding: 20px;
            box-shadow: var(--shadow-md);
            position: relative;
            z-index: 10;
            transition: var(--transition);
        }

        .sidebar-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 40px;
            position: relative;
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

        .sidebar h3 {
            color: var(--accent);
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(117, 217, 121, 0.3);
            text-align: center;
            width: 100%;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
        }

        .sidebar a i {
            font-size: 1.1rem;
            width: 24px;
        }

        .sidebar a:hover {
            background-color: rgba(117, 217, 121, 0.15);
            color: white;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: linear-gradient(90deg, var(--secondary) 0%, #63c967 100%);
            color: var(--primary);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(117, 217, 121, 0.3);
        }

        .sidebar a.logout {
            margin-top: auto;
            color: rgba(255, 255, 255, 0.7);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            padding-top: 20px;
        }

        .sidebar a.logout:hover {
            color: var(--accent);
        }

        /* Content Area */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--gray-light);
        }

        /* Header */
        .header {
            background-color: var(--white);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 30px;
            transition: var(--transition);
            background-color: var(--primary-light);
        }

        .user-profile:hover {
            background-color: rgba(0, 104, 52, 0.15);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* Cards */
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--gray-medium);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            padding: 22px 30px;
            border-bottom: 1px solid var(--gray-medium);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            background-color: var(--white);
            position: relative;
        }

        .card-header:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            opacity: 0.5;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            position: relative;
        }

        .card-title i {
            font-size: 1.1em;
            background-color: var(--primary-lighter);
            color: var(--primary);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius-full);
        }

        .card-body {
            padding: 30px;
            position: relative;
        }

        /* Dashboard Stats */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            border: 1px solid var(--gray-medium);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .stat-card.pending {
            border-left: 4px solid var(--warning);
        }

        .stat-card.approved {
            border-left: 4px solid var(--success);
        }

        .stat-card.rejected {
            border-left: 4px solid var(--danger);
        }

        .stat-card.total {
            border-left: 4px solid var(--info);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--border-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .stat-card.pending .stat-icon {
            background-color: var(--warning-light);
            color: var(--warning);
        }

        .stat-card.approved .stat-icon {
            background-color: var(--success-light);
            color: var(--success);
        }

        .stat-card.rejected .stat-icon {
            background-color: var(--danger-light);
            color: var(--danger);
        }

        .stat-card.total .stat-icon {
            background-color: var(--info-light);
            color: var(--info);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .stat-change {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: auto;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: var(--border-radius);
            position: relative;
        }

        .table-loader {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .table-loader.active {
            opacity: 1;
            visibility: visible;
        }

        .table-loader .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--primary-light);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 5px;
        }

        th {
            text-align: left;
            padding: 16px 20px;
            font-weight: 600;
            color: var(--primary);
            background-color: var(--primary-lighter);
            border-bottom: 2px solid var(--primary-light);
            position: relative;
            transition: var(--transition-fast);
            cursor: pointer;
            user-select: none;
        }

        th:hover {
            background-color: var(--primary-light);
        }

        th i {
            margin-left: 5px;
            font-size: 0.9em;
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-medium);
            vertical-align: middle;
            transition: var(--transition-fast);
        }

        tbody tr {
            transition: var(--transition-fast);
        }

        tbody tr:hover {
            background-color: var(--primary-lighter);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Table pagination */
        .table-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--gray-light);
            border-top: 1px solid var(--gray-medium);
            font-size: 0.9rem;
        }

        .pagination-info {
            color: var(--text-light);
        }

        .pagination-controls {
            display: flex;
            gap: 5px;
        }

        .pagination-button {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius-sm);
            background-color: var(--white);
            border: 1px solid var(--gray-medium);
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .pagination-button:hover {
            background-color: var(--primary-lighter);
            border-color: var(--primary-light);
        }

        .pagination-button.active {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .pagination-button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Faculty Info */
        .faculty-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faculty-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .faculty-name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .faculty-role {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition-bounce);
            border: none;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            font-family: var(--font-family);
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: var(--transition-fast);
        }

        .btn:hover::after {
            opacity: 1;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 0.9rem;
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 1.1rem;
        }

        .btn-approve {
            background-color: var(--success);
            color: white;
        }

        .btn-approve:hover {
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
            transform: translateY(-2px);
        }

        .btn-approve.btn-outline {
            background-color: transparent;
            color: var(--success);
            border: 1px solid var(--success);
        }

        .btn-approve.btn-outline:hover {
            background-color: var(--success-light);
        }

        .btn-reject {
            background-color: var(--danger);
            color: white;
        }

        .btn-reject:hover {
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
            transform: translateY(-2px);
        }

        .btn-reject.btn-outline {
            background-color: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .btn-reject.btn-outline:hover {
            background-color: var(--danger-light);
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(0, 104, 52, 0.3);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: var(--primary-dark);
        }

        .btn-secondary:hover {
            box-shadow: 0 4px 12px rgba(117, 217, 121, 0.3);
            transform: translateY(-2px);
        }

        .btn-light {
            background-color: var(--gray-medium);
            color: var(--text-dark);
        }

        .btn-light:hover {
            background-color: var(--gray-dark);
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            border-radius: var(--border-radius-full);
        }

        .btn-icon.btn-sm {
            width: 32px;
            height: 32px;
        }

        .btn-icon.btn-lg {
            width: 48px;
            height: 48px;
        }

        .btn-group {
            display: flex;
            gap: 8px;
        }

        /* Status */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: var(--border-radius-full);
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .status:hover {
            transform: translateY(-2px);
        }

        .status-approved {
            background-color: var(--success-light);
            color: var(--success);
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        .status-rejected {
            background-color: var(--danger-light);
            color: var(--danger);
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        .status-pending {
            background-color: var(--warning-light);
            color: var(--warning);
            border: 1px solid rgba(243, 156, 18, 0.2);
        }

        /* Document links */
        .document-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .document-link:hover {
            background-color: rgba(0, 104, 52, 0.05);
        }

        .no-file {
            color: var(--text-light);
            font-style: italic;
            font-size: 0.9rem;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 0;
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .empty-state h3 {
            font-size: 1.2rem;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-light);
            max-width: 500px;
            margin: 0 auto;
        }

        /* Margin classes */
        .mt-4 {
            margin-top: 20px;
        }

        /* Search box and filters */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            position: relative;
        }

        .filters-wrapper {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 25px;
            border: 1px solid var(--gray-medium);
            transition: var(--transition);
        }

        .filters-wrapper:hover {
            box-shadow: var(--shadow-md);
        }

        .filters-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filters-title i {
            color: var(--primary);
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--gray-medium);
            border-radius: var(--border-radius-sm);
            font-size: 0.95rem;
            transition: var(--transition-fast);
            background-color: var(--white);
            color: var(--text-dark);
            box-shadow: var(--shadow-inset);
            font-family: var(--font-family);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 104, 52, 0.1);
        }

        .search-box input::placeholder {
            color: var(--text-lighter);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1.1rem;
            pointer-events: none;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 200px;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-medium);
        }

        select {
            padding: 12px 15px;
            border: 1px solid var(--gray-medium);
            border-radius: var(--border-radius-sm);
            background-color: var(--white);
            font-size: 0.95rem;
            min-width: 200px;
            color: var(--text-dark);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            transition: var(--transition-fast);
            box-shadow: var(--shadow-inset);
            font-family: var(--font-family);
        }

        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 104, 52, 0.1);
        }

        /* Filter tags */
        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background-color: var(--primary-lighter);
            border: 1px solid var(--primary-light);
            border-radius: var(--border-radius-full);
            font-size: 0.85rem;
            color: var(--primary);
            transition: var(--transition-fast);
        }

        .filter-tag:hover {
            background-color: var(--primary-light);
        }

        .filter-tag i {
            cursor: pointer;
            font-size: 0.8rem;
        }

        /* Active filters indicator */
        .filters-active {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 16px;
            height: 16px;
            background-color: var(--primary);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }

            .sidebar h3,
            .sidebar a span {
                display: none;
            }

            .sidebar a {
                justify-content: center;
            }

            .sidebar a i {
                margin-right: 0;
            }

            .content {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px;
            }

            .header-right {
                margin-top: 10px;
                align-self: flex-end;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters {
                width: 100%;
            }

            .search-box,
            select {
                width: 100%;
            }

            .card-body {
                padding: 15px;
            }

            th,
            td {
                padding: 10px;
            }
        }
        .notification-bell {
            position: relative;
            padding: 12px;
            border-radius: 50%;
            background-color: var(--gray-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .notification-bell:hover {
            background-color: var(--primary-light);
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 15px;
            height: 15px;
            background-color: #ff5252;
            border-radius: 50%;
            font-size: 0.6rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/CCIS-Logo-Official.png" alt="College Logo" class="logo">
                <h3><i>Faculty Project Management System</i></h3>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span></a>
                <a href="approvals.php" class="active"><i class="fa-solid fa-check-to-slot"></i>
                    <span>Approvals</span></a>
                <a href="reports.php"><i class="fa-solid fa-chart-pie"></i> <span>Reports</span></a>
                <a href="faculty_management.php"><i class="fa-solid fa-users-gear"></i> <span>Faculty
                        Management</span></a>
                <a href="ched_compli-audit.php"><i class="fa-solid fa-clipboard-check"></i> <span>CHED Compliance
                        Audit</span></a>
                <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span></a>
            </div>
        </div>

        <div class="content">
            <div class="header">
                <div class="header-left">
                    <h1 class="page-title"><i class="fa-solid fa-check-to-slot"></i> Approvals</h1>
                </div>
                <div class="header-right">
                    <div class="notification-bell">
                        <i class="fa-solid fa-bell"></i>
                        <div class="notification-badge">3</div>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo substr($_SESSION['admin_name'] ?? 'A', 0, 1); ?></div>
                        <div>
                            <p style="font-weight: 600; margin: 0;">
                                <?php echo $_SESSION['admin_name'] ?? 'Admin User'; ?>
                            </p>
                            <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="stats-container">
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value"><?php echo count($pendingSubmissions); ?></div>
                    <div class="stat-label">Pending Submissions</div>
                    <div class="stat-change positive">
                        <i class="fa-solid fa-arrow-up"></i> 12% from last week
                    </div>
                </div>

                <div class="stat-card approved">
                    <div class="stat-icon">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?php
                    $approvedCount = 0;
                    foreach ($recentlyProcessed as $item) {
                        if ($item['status'] == 'approved')
                            $approvedCount++;
                    }
                    echo $approvedCount;
                    ?></div>
                    <div class="stat-label">Recently Approved</div>
                    <div class="stat-change positive">
                        <i class="fa-solid fa-arrow-up"></i> 5% from last week
                    </div>
                </div>

                <div class="stat-card rejected">
                    <div class="stat-icon">
                        <i class="fa-solid fa-times-circle"></i>
                    </div>
                    <div class="stat-value"><?php
                    $rejectedCount = 0;
                    foreach ($recentlyProcessed as $item) {
                        if ($item['status'] == 'rejected')
                            $rejectedCount++;
                    }
                    echo $rejectedCount;
                    ?></div>
                    <div class="stat-label">Recently Rejected</div>
                    <div class="stat-change negative">
                        <i class="fa-solid fa-arrow-down"></i> 3% from last week
                    </div>
                </div>

                <div class="stat-card total">
                    <div class="stat-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-value"><?php
                    // Get total faculty count from the database
                    $query = "SELECT COUNT(*) as total FROM faculty";
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo $row['total'];
                    ?></div>
                    <div class="stat-label">Total Faculty</div>
                    <div class="stat-change positive">
                        <i class="fa-solid fa-arrow-up"></i> 2% from last month
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filters-wrapper">
                <div class="filters-title">
                    <i class="fa-solid fa-filter"></i> Filter Submissions
                </div>
                <div class="filters">
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" placeholder="Search by faculty name or requirement..." id="searchInput">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Requirement Type</label>
                        <select id="requirementFilter">
                            <option value="">All Requirements</option>
                            <?php foreach ($requirements as $req): ?>
                                <option value="<?php echo htmlspecialchars($req['requirement_name']); ?>">
                                    <?php echo htmlspecialchars($req['requirement_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button id="clearFilters" class="btn btn-sm btn-light">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Clear Filters
                    </button>
                </div>
                <div class="filter-tags" id="filterTags" style="display: none;">
                    <!-- Filter tags will be added here dynamically -->
                </div>
            </div>

            <!-- Pending Submissions Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fa-solid fa-hourglass-half"></i>
                        Pending Submissions
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($pendingSubmissions)): ?>
                        <div class="bulk-actions" style="margin-bottom: 20px; display: none;">
                            <div class="bulk-actions-container">
                                <div class="bulk-actions-info">
                                    <div class="bulk-actions-icon">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <div class="bulk-actions-text">
                                        <span id="selectedCount">0</span> submissions selected
                                    </div>
                                </div>
                                <div class="bulk-actions-buttons">
                                    <button id="bulkApprove" class="btn btn-sm btn-approve">
                                        <i class="fa-solid fa-check"></i> Approve All
                                    </button>
                                    <button id="bulkReject" class="btn btn-sm btn-reject">
                                        <i class="fa-solid fa-times"></i> Reject All
                                    </button>
                                    <button id="clearSelection" class="btn btn-sm btn-light">
                                        <i class="fa-solid fa-xmark"></i> Clear Selection
                                    </button>
                                </div>
                            </div>
                        </div>

                        <style>
                            .bulk-actions-container {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                background-color: var(--white);
                                padding: 16px 20px;
                                border-radius: var(--border-radius);
                                box-shadow: var(--shadow-md);
                                border: 1px solid var(--primary-light);
                                position: relative;
                                overflow: hidden;
                            }

                            .bulk-actions-container::before {
                                content: '';
                                position: absolute;
                                left: 0;
                                top: 0;
                                height: 100%;
                                width: 4px;
                                background: linear-gradient(to bottom, var(--primary), var(--secondary));
                            }

                            .bulk-actions-info {
                                display: flex;
                                align-items: center;
                                gap: 12px;
                            }

                            .bulk-actions-icon {
                                width: 40px;
                                height: 40px;
                                border-radius: var(--border-radius-full);
                                background-color: var(--primary-lighter);
                                color: var(--primary);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 1.2rem;
                            }

                            .bulk-actions-text {
                                font-size: 1rem;
                                color: var(--text-dark);
                            }

                            .bulk-actions-text #selectedCount {
                                font-weight: 700;
                                color: var(--primary);
                                font-size: 1.2rem;
                            }

                            .bulk-actions-buttons {
                                display: flex;
                                gap: 10px;
                            }

                            @media (max-width: 768px) {
                                .bulk-actions-container {
                                    flex-direction: column;
                                    gap: 15px;
                                    align-items: flex-start;
                                }

                                .bulk-actions-buttons {
                                    width: 100%;
                                    justify-content: flex-end;
                                }
                            }
                        </style>

                        <div class="table-responsive">
                            <div class="table-loader">
                                <div class="spinner"></div>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAll"
                                                style="width: 18px; height: 18px; cursor: pointer;">
                                        </th>
                                        <th data-sort="name">Faculty Name <i class="fa-solid fa-sort"></i></th>
                                        <th data-sort="requirement">Requirement <i class="fa-solid fa-sort"></i></th>
                                        <th data-sort="date">Date Submitted <i class="fa-solid fa-sort"></i></th>
                                        <th>Files</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingSubmissions as $submission): ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="submission-checkbox"
                                                    data-id="<?php echo $submission['id']; ?>"
                                                    style="width: 18px; height: 18px; cursor: pointer;">
                                            </td>
                                            <td>
                                                <div class="faculty-info">
                                                    <img src="<?php echo $submission['faculty']['profile_image'] ?? '../assets/placeholder.jpg'; ?>"
                                                        alt="<?php echo $submission['faculty']['first_name']; ?>"
                                                        class="faculty-avatar">
                                                    <div>
                                                        <div class="faculty-name">
                                                            <?php echo $submission['faculty']['first_name']; ?>
                                                            <?php echo $submission['faculty']['last_name']; ?>
                                                        </div>
                                                        <div class="faculty-role">
                                                            <?php echo $submission['faculty']['role']['role_name'] ?? 'Faculty'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $submission['requirement']['requirement_name']; ?></td>
                                            <td><?php echo date('M d, Y h:ia', strtotime($submission['updated_at'])); ?></td>
                                            <td>
                                                <div class="file-list">
                                                    <?php if (!empty($submission['file_path'])): ?>
                                                        <a href="<?php echo $submission['file_path']; ?>" class="document-link"
                                                            target="_blank">
                                                            <i class="fa-solid fa-file-pdf"></i>
                                                            View Document
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="no-file">No files attached</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions">
                                                    <button class="btn btn-sm btn-approve"
                                                        onclick="approveSubmission(<?php echo $submission['id']; ?>)">
                                                        <i class="fa-solid fa-check"></i> Approve
                                                    </button>
                                                    <button class="btn btn-sm btn-reject"
                                                        onclick="rejectSubmission(<?php echo $submission['id']; ?>)">
                                                        <i class="fa-solid fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-check-circle empty-icon"></i>
                            <h3>No Pending Submissions</h3>
                            <p>There are no submissions waiting for your approval at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recently Processed -->
            <div class="card mt-4">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fa-solid fa-history"></i>
                        Recently Processed
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentlyProcessed)): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Faculty Name</th>
                                        <th>Requirement</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentlyProcessed as $item): ?>
                                        <tr>
                                            <td><?php echo $item['faculty']['first_name']; ?>
                                                <?php echo $item['faculty']['last_name']; ?>
                                            </td>
                                            <td><?php echo $item['requirement']['requirement_name']; ?></td>
                                            <td><?php echo date('M d, Y h:ia', strtotime($item['updated_at'])); ?></td>
                                            <td>
                                                <span class="status status-<?php echo $item['status']; ?>">
                                                    <i
                                                        class="fa-solid fa-<?php echo $item['status'] == 'approved' ? 'check-circle' : 'times-circle'; ?>"></i>
                                                    <?php echo ucfirst($item['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No recently processed submissions.</p>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                /**
                 * Handle submission approval with confirmation
                 * @param {number} id - The submission ID
                 */
                function approveSubmission(id) {
                    if (confirm('Are you sure you want to approve this submission?')) {
                        // Show loading state
                        const button = event.target.closest('.btn-approve');
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                        button.disabled = true;

                        // Redirect to approve.php
                        window.location.href = 'approve.php?id=' + id;
                    }
                }

                /**
                 * Handle submission rejection with reason prompt
                 * @param {number} id - The submission ID
                 */
                function rejectSubmission(id) {
                    // Create a custom modal for rejection reason
                    const modal = document.createElement('div');
                    modal.style.position = 'fixed';
                    modal.style.top = '0';
                    modal.style.left = '0';
                    modal.style.width = '100%';
                    modal.style.height = '100%';
                    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
                    modal.style.display = 'flex';
                    modal.style.justifyContent = 'center';
                    modal.style.alignItems = 'center';
                    modal.style.zIndex = '1000';

                    const modalContent = document.createElement('div');
                    modalContent.style.backgroundColor = 'white';
                    modalContent.style.padding = '30px';
                    modalContent.style.borderRadius = '10px';
                    modalContent.style.width = '500px';
                    modalContent.style.maxWidth = '90%';
                    modalContent.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';

                    modalContent.innerHTML = `
        <h3 style="margin-bottom: 20px; color: #e74c3c;">Reject Submission</h3>
        <p style="margin-bottom: 15px;">Please provide a reason for rejection:</p>
        <textarea id="rejectionReason" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; margin-bottom: 20px;"></textarea>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button id="cancelReject" style="padding: 8px 15px; border: none; border-radius: 5px; background-color: #f1f1f1; cursor: pointer;">Cancel</button>
            <button id="confirmReject" style="padding: 8px 15px; border: none; border-radius: 5px; background-color: #e74c3c; color: white; cursor: pointer;">Reject</button>
        </div>
    `;

                    modal.appendChild(modalContent);
                    document.body.appendChild(modal);

                    // Focus the textarea
                    setTimeout(() => {
                        document.getElementById('rejectionReason').focus();
                    }, 100);

                    // Handle cancel button
                    document.getElementById('cancelReject').addEventListener('click', function () {
                        document.body.removeChild(modal);
                    });

                    // Handle confirm button
                    document.getElementById('confirmReject').addEventListener('click', function () {
                        const reason = document.getElementById('rejectionReason').value.trim();
                        if (!reason) {
                            alert('Please provide a reason for rejection.');
                            return;
                        }

                        // Show loading state
                        const confirmButton = this;
                        confirmButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                        confirmButton.disabled = true;

                        // Redirect to reject.php
                        window.location.href = 'reject.php?id=' + id + '&reason=' + encodeURIComponent(reason);
                    });

                    // Close modal when clicking outside
                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) {
                            document.body.removeChild(modal);
                        }
                    });

                    // Close modal with Escape key
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && document.body.contains(modal)) {
                            document.body.removeChild(modal);
                        }
                    });
                }

                /**
                 * Apply all filters and search
                 */
                function applyFilters() {
                    const searchText = document.getElementById('searchInput').value.toLowerCase();
                    const requirementFilter = document.getElementById('requirementFilter').value;
                    const rows = document.querySelectorAll('tbody tr');
                    let visibleCount = 0;

                    // Update filter tags
                    updateFilterTags(searchText, requirementFilter);

                    // Show loading state
                    const tableLoader = document.querySelector('.table-loader');
                    if (tableLoader) {
                        tableLoader.classList.add('active');
                    }

                    // Apply filters with a slight delay for animation
                    setTimeout(() => {
                        rows.forEach(row => {
                            // Skip if this is not in the pending submissions table
                            if (!row.querySelector('.faculty-name')) return;

                            const facultyName = row.querySelector('.faculty-name').textContent.toLowerCase();
                            const requirementName = row.cells[2].textContent.toLowerCase();
                            const requirementMatch = !requirementFilter || requirementName.includes(requirementFilter.toLowerCase());
                            const searchMatch = !searchText || facultyName.includes(searchText) || requirementName.includes(searchText);

                            // Show/hide row based on filters
                            if (requirementMatch && searchMatch) {
                                row.style.display = '';
                                visibleCount++;

                                // Add animation class with staggered delay
                                setTimeout(() => {
                                    row.classList.add('filtered-in');
                                    row.classList.remove('filtered-out');
                                }, visibleCount * 30); // Stagger the animations
                            } else {
                                row.style.display = 'none';
                                row.classList.add('filtered-out');
                                row.classList.remove('filtered-in');
                            }
                        });

                        // Show/hide empty state message
                        const emptyState = document.querySelector('.empty-state');
                        const tableContainer = document.querySelector('.table-responsive');

                        if (tableContainer) {
                            if (visibleCount === 0 && rows.length > 0) {
                                // Show custom empty state for filtered results
                                if (!document.getElementById('filtered-empty-state')) {
                                    const filteredEmptyState = document.createElement('div');
                                    filteredEmptyState.id = 'filtered-empty-state';
                                    filteredEmptyState.className = 'empty-state';
                                    filteredEmptyState.innerHTML = `
                                        <i class="fa-solid fa-filter empty-icon"></i>
                                        <h3>No Matching Submissions</h3>
                                        <p>No submissions match your current filters. Try adjusting your search criteria.</p>
                                        <button id="clearFilters" class="btn btn-sm btn-light" style="margin-top: 15px;">
                                            <i class="fa-solid fa-filter-circle-xmark"></i> Clear Filters
                                        </button>
                                    `;
                                    tableContainer.style.display = 'none';
                                    tableContainer.parentNode.appendChild(filteredEmptyState);
                                }
                            } else {
                                // Remove filtered empty state if it exists
                                const filteredEmptyState = document.getElementById('filtered-empty-state');
                                if (filteredEmptyState) {
                                    filteredEmptyState.parentNode.removeChild(filteredEmptyState);
                                    tableContainer.style.display = '';
                                }
                            }
                        }

                        // Update counter if it exists
                        const counter = document.getElementById('submissionCounter');
                        if (counter) {
                            counter.textContent = visibleCount;
                        }

                        // Hide loading state
                        if (tableLoader) {
                            tableLoader.classList.remove('active');
                        }
                    }, 200);
                }

                /**
                 * Update filter tags based on active filters
                 */
                function updateFilterTags(searchText, requirementFilter) {
                    const filterTagsContainer = document.getElementById('filterTags');
                    if (!filterTagsContainer) return;

                    // Clear existing tags
                    filterTagsContainer.innerHTML = '';

                    let hasFilters = false;

                    // Add search tag if search text exists
                    if (searchText) {
                        const searchTag = document.createElement('div');
                        searchTag.className = 'filter-tag';
                        searchTag.innerHTML = `
                            <span>Search: ${searchText}</span>
                            <i class="fa-solid fa-times" data-filter="search"></i>
                        `;
                        filterTagsContainer.appendChild(searchTag);
                        hasFilters = true;
                    }

                    // Add requirement tag if requirement filter exists
                    if (requirementFilter) {
                        const requirementTag = document.createElement('div');
                        requirementTag.className = 'filter-tag';
                        requirementTag.innerHTML = `
                            <span>Requirement: ${requirementFilter}</span>
                            <i class="fa-solid fa-times" data-filter="requirement"></i>
                        `;
                        filterTagsContainer.appendChild(requirementTag);
                        hasFilters = true;
                    }

                    // Show/hide the filter tags container
                    filterTagsContainer.style.display = hasFilters ? 'flex' : 'none';

                    // Add event listeners to remove tags
                    const removeButtons = filterTagsContainer.querySelectorAll('.filter-tag i');
                    removeButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const filterType = this.getAttribute('data-filter');
                            if (filterType === 'search') {
                                document.getElementById('searchInput').value = '';
                            } else if (filterType === 'requirement') {
                                document.getElementById('requirementFilter').value = '';
                            }
                            applyFilters();
                        });
                    });
                }

                /**
                 * Sort table by column
                 * @param {string} column - Column to sort by
                 * @param {boolean} asc - Sort ascending or descending
                 */
                function sortTable(column, asc = true) {
                    const table = document.querySelector('table');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    // Show loading state
                    const tableLoader = document.querySelector('.table-loader');
                    if (tableLoader) {
                        tableLoader.classList.add('active');
                    }

                    // Update sort indicators
                    const headers = table.querySelectorAll('th[data-sort]');
                    headers.forEach(header => {
                        const icon = header.querySelector('i');
                        if (header.getAttribute('data-sort') === column) {
                            icon.className = asc ? 'fa-solid fa-sort-up' : 'fa-solid fa-sort-down';
                            icon.style.opacity = '1';
                        } else {
                            icon.className = 'fa-solid fa-sort';
                            icon.style.opacity = '0.5';
                        }
                    });

                    // Sort the rows
                    const sortedRows = rows.sort((a, b) => {
                        let aValue, bValue;

                        if (column === 'name') {
                            aValue = a.querySelector('.faculty-name').textContent.trim().toLowerCase();
                            bValue = b.querySelector('.faculty-name').textContent.trim().toLowerCase();
                        } else if (column === 'requirement') {
                            aValue = a.cells[2].textContent.trim().toLowerCase();
                            bValue = b.cells[2].textContent.trim().toLowerCase();
                        } else if (column === 'date') {
                            aValue = new Date(a.cells[3].textContent.trim());
                            bValue = new Date(b.cells[3].textContent.trim());
                        }

                        if (aValue < bValue) return asc ? -1 : 1;
                        if (aValue > bValue) return asc ? 1 : -1;
                        return 0;
                    });

                    // Remove all rows
                    rows.forEach(row => {
                        tbody.removeChild(row);
                    });

                    // Add sorted rows with animation
                    sortedRows.forEach((row, index) => {
                        // Remove any existing animation classes
                        row.classList.remove('filtered-in', 'filtered-out', 'sorted');

                        // Append the row
                        tbody.appendChild(row);

                        // Add animation with delay
                        setTimeout(() => {
                            row.classList.add('sorted');
                        }, index * 30);
                    });

                    // Hide loading state after a delay
                    setTimeout(() => {
                        if (tableLoader) {
                            tableLoader.classList.remove('active');
                        }
                    }, 300);
                }

                // Initialize event listeners when DOM is loaded
                document.addEventListener('DOMContentLoaded', function () {
                    // Search input event
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) {
                        searchInput.addEventListener('keyup', applyFilters);
                    }

                    // Requirement filter event
                    const requirementFilter = document.getElementById('requirementFilter');
                    if (requirementFilter) {
                        requirementFilter.addEventListener('change', applyFilters);
                    }

                    // Clear filters button event
                    document.addEventListener('click', function (e) {
                        if (e.target.id === 'clearFilters' || e.target.closest('#clearFilters')) {
                            if (searchInput) searchInput.value = '';
                            if (requirementFilter) requirementFilter.value = '';
                            applyFilters();
                        }
                    });

                    // Sortable columns
                    const sortableHeaders = document.querySelectorAll('th[data-sort]');
                    sortableHeaders.forEach(header => {
                        header.addEventListener('click', function () {
                            const column = this.getAttribute('data-sort');
                            const icon = this.querySelector('i');
                            const isAscending = icon.className.includes('fa-sort-up') || icon.className.includes('fa-sort');
                            sortTable(column, !isAscending);
                        });
                    });

                    // Add submission counter to the card header
                    const cardHeader = document.querySelector('.card-header h2');
                    if (cardHeader && document.querySelectorAll('tbody tr').length > 0) {
                        const count = document.querySelectorAll('tbody tr').length;
                        const counter = document.createElement('span');
                        counter.id = 'submissionCounter';
                        counter.textContent = count;
                        counter.style.marginLeft = '10px';
                        counter.style.backgroundColor = 'var(--primary-light)';
                        counter.style.color = 'var(--primary)';
                        counter.style.borderRadius = 'var(--border-radius-full)';
                        counter.style.padding = '3px 12px';
                        counter.style.fontSize = '0.9rem';
                        counter.style.fontWeight = '600';
                        cardHeader.appendChild(counter);
                    }

                    // Initialize filter tags
                    updateFilterTags('', '');

                    // Add table loader if it doesn't exist
                    const tableResponsive = document.querySelector('.table-responsive');
                    if (tableResponsive && !tableResponsive.querySelector('.table-loader')) {
                        const loader = document.createElement('div');
                        loader.className = 'table-loader';
                        loader.innerHTML = '<div class="spinner"></div>';
                        tableResponsive.prepend(loader);
                    }

                    // Add CSS for animations
                    const style = document.createElement('style');
                    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(10px); }
        }

        .filtered-in {
            animation: fadeIn 0.3s ease forwards;
        }

        .filtered-out {
            animation: fadeOut 0.3s ease forwards;
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    tr.selected {
            background-color: rgba(0, 104, 52, 0.05);
        }

        tr.selected td {
            background-color: rgba(0, 104, 52, 0.05);
        }
    `;
                    document.head.appendChild(style);

                    // Bulk actions functionality
                    const bulkActionsContainer = document.querySelector('.bulk-actions');
                    const selectAllCheckbox = document.getElementById('selectAll');
                    const submissionCheckboxes = document.querySelectorAll('.submission-checkbox');
                    const selectedCountElement = document.getElementById('selectedCount');
                    const bulkApproveButton = document.getElementById('bulkApprove');
                    const bulkRejectButton = document.getElementById('bulkReject');
                    const clearSelectionButton = document.getElementById('clearSelection');

                    // Function to update selected count and show/hide bulk actions
                    function updateSelectedCount() {
                        const selectedCheckboxes = document.querySelectorAll('.submission-checkbox:checked');
                        const count = selectedCheckboxes.length;

                        if (selectedCountElement) {
                            selectedCountElement.textContent = count;
                        }

                        if (bulkActionsContainer) {
                            bulkActionsContainer.style.display = count > 0 ? 'block' : 'none';
                        }

                        // Update "Select All" checkbox state
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = count > 0 && count === submissionCheckboxes.length;
                            selectAllCheckbox.indeterminate = count > 0 && count < submissionCheckboxes.length;
                        }
                    }

                    // Initialize checkboxes
                    if (submissionCheckboxes.length > 0) {
                        submissionCheckboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', function () {
                                // Toggle selected class on the row
                                const row = this.closest('tr');
                                if (this.checked) {
                                    row.classList.add('selected');
                                } else {
                                    row.classList.remove('selected');
                                }

                                updateSelectedCount();
                            });
                        });
                    }

                    // Select All checkbox
                    if (selectAllCheckbox) {
                        selectAllCheckbox.addEventListener('change', function () {
                            const isChecked = this.checked;

                            submissionCheckboxes.forEach(checkbox => {
                                checkbox.checked = isChecked;

                                // Toggle selected class on the row
                                const row = checkbox.closest('tr');
                                if (isChecked) {
                                    row.classList.add('selected');
                                } else {
                                    row.classList.remove('selected');
                                }
                            });

                            updateSelectedCount();
                        });
                    }

                    // Clear Selection button
                    if (clearSelectionButton) {
                        clearSelectionButton.addEventListener('click', function () {
                            submissionCheckboxes.forEach(checkbox => {
                                checkbox.checked = false;
                                const row = checkbox.closest('tr');
                                row.classList.remove('selected');
                            });

                            updateSelectedCount();
                        });
                    }

                    // Bulk Approve button
                    if (bulkApproveButton) {
                        bulkApproveButton.addEventListener('click', function () {
                            const selectedIds = Array.from(document.querySelectorAll('.submission-checkbox:checked'))
                                .map(checkbox => checkbox.getAttribute('data-id'));

                            if (selectedIds.length === 0) {
                                alert('Please select at least one submission to approve.');
                                return;
                            }

                            if (confirm(`Are you sure you want to approve ${selectedIds.length} submission(s)?`)) {
                                // Create and submit a form to process bulk actions
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = 'bulk_actions.php';
                                form.style.display = 'none';

                                // Add action input
                                const actionInput = document.createElement('input');
                                actionInput.type = 'hidden';
                                actionInput.name = 'action';
                                actionInput.value = 'approve';
                                form.appendChild(actionInput);

                                // Add submission IDs
                                selectedIds.forEach(id => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'submission_ids[]';
                                    input.value = id;
                                    form.appendChild(input);
                                });

                                // Submit the form
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    }

                    // Bulk Reject button
                    if (bulkRejectButton) {
                        bulkRejectButton.addEventListener('click', function () {
                            const selectedIds = Array.from(document.querySelectorAll('.submission-checkbox:checked'))
                                .map(checkbox => checkbox.getAttribute('data-id'));

                            if (selectedIds.length === 0) {
                                alert('Please select at least one submission to reject.');
                                return;
                            }

                            // Create a custom modal for rejection reason
                            const modal = document.createElement('div');
                            modal.style.position = 'fixed';
                            modal.style.top = '0';
                            modal.style.left = '0';
                            modal.style.width = '100%';
                            modal.style.height = '100%';
                            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
                            modal.style.display = 'flex';
                            modal.style.justifyContent = 'center';
                            modal.style.alignItems = 'center';
                            modal.style.zIndex = '1000';

                            const modalContent = document.createElement('div');
                            modalContent.style.backgroundColor = 'white';
                            modalContent.style.padding = '30px';
                            modalContent.style.borderRadius = '10px';
                            modalContent.style.width = '500px';
                            modalContent.style.maxWidth = '90%';
                            modalContent.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';

                            modalContent.innerHTML = `
                                <h3 style="margin-bottom: 20px; color: #e74c3c;">Reject ${selectedIds.length} Submission(s)</h3>
                                <p style="margin-bottom: 15px;">Please provide a reason for rejection:</p>
                                <textarea id="bulkRejectionReason" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; margin-bottom: 20px;"></textarea>
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button id="cancelBulkReject" style="padding: 8px 15px; border: none; border-radius: 5px; background-color: #f1f1f1; cursor: pointer;">Cancel</button>
                                    <button id="confirmBulkReject" style="padding: 8px 15px; border: none; border-radius: 5px; background-color: #e74c3c; color: white; cursor: pointer;">Reject All</button>
                                </div>
                            `;

                            modal.appendChild(modalContent);
                            document.body.appendChild(modal);

                            // Focus the textarea
                            setTimeout(() => {
                                document.getElementById('bulkRejectionReason').focus();
                            }, 100);

                            // Handle cancel button
                            document.getElementById('cancelBulkReject').addEventListener('click', function () {
                                document.body.removeChild(modal);
                            });

                            // Handle confirm button
                            document.getElementById('confirmBulkReject').addEventListener('click', function () {
                                const reason = document.getElementById('bulkRejectionReason').value.trim();
                                if (!reason) {
                                    alert('Please provide a reason for rejection.');
                                    return;
                                }

                                // Create and submit a form to process bulk actions
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = 'bulk_actions.php';
                                form.style.display = 'none';

                                // Add action input
                                const actionInput = document.createElement('input');
                                actionInput.type = 'hidden';
                                actionInput.name = 'action';
                                actionInput.value = 'reject';
                                form.appendChild(actionInput);

                                // Add reason input
                                const reasonInput = document.createElement('input');
                                reasonInput.type = 'hidden';
                                reasonInput.name = 'reason';
                                reasonInput.value = reason;
                                form.appendChild(reasonInput);

                                // Add submission IDs
                                selectedIds.forEach(id => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'submission_ids[]';
                                    input.value = id;
                                    form.appendChild(input);
                                });

                                // Submit the form
                                document.body.appendChild(form);
                                form.submit();
                            });

                            // Close modal when clicking outside
                            modal.addEventListener('click', function (e) {
                                if (e.target === modal) {
                                    document.body.removeChild(modal);
                                }
                            });

                            // Close modal with Escape key
                            document.addEventListener('keydown', function (e) {
                                if (e.key === 'Escape' && document.body.contains(modal)) {
                                    document.body.removeChild(modal);
                                }
                            });
                        });
                    }

                    // Initialize selected count
                    updateSelectedCount();
                });
            </script>
</body>

</html>