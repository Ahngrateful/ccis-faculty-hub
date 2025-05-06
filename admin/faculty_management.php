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
$query = "SELECT * FROM faculty";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Faculty Management - FPMS</title>
    <style>
        /* Global Styles */
        :root {
            --primary: #006834;
            --primary-light: rgba(0, 104, 52, 0.1);
            --secondary: #75d979;
            --accent: #ffde26;
            --text-dark: #333333;
            --text-light: #666666;
            --gray-light: #f9f9f9;
            --white: #ffffff;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--gray-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Layout */
        .container {
            display: flex;
            min-height: 100vh;
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
            font-weight: bold;
            font-size: 1.2rem;
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

        /* Main Content */
        .main-content {
            padding: 30px;
            flex: 1;
            overflow-y: auto;
        }

        h2 {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 2px;
        }

        /* Filters */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
            align-items: center;
            background-color: var(--white);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .filter-title {
            font-weight: 600;
            color: var(--primary);
            margin-right: 10px;
        }

        select,
        input[type="text"],
        input[type="date"] {
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: var(--white);
            color: var(--text-dark);
            font-size: 0.95rem;
            transition: var(--transition);
            min-width: 180px;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 104, 52, 0.1);
        }

        .search-box {
            position: relative;
            flex-grow: 1;
        }

        .search-box input {
            width: 100%;
            padding-left: 40px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        /* Button Styles */
        button {
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
            color: white;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0, 104, 52, 0.2);
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 104, 52, 0.25);
        }

        button i {
            font-size: 1.1rem;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 30px;
        }

        table,
        th,
        td {
            border: none;
        }

        table {
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, var(--primary) 0%, #005229 100%);
            color: white;
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }

        tr {
            background-color: var(--primary);
            transition: var(--transition);
        }

        tr:hover {
            background-color: rgba(0, 104, 52, 0.02);
        }

        td {
            background-color: var(--white);
            padding: 16px 20px;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Status Styles */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-active {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .status-inactive {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }

        /* Action links */
        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-edit {
            background-color: rgba(0, 104, 52, 0.1);
            color: var(--primary);
            border: 1px solid rgba(0, 104, 52, 0.2);
        }

        .btn-edit:hover {
            background-color: rgba(0, 104, 52, 0.2);
        }

        .btn-activate {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .btn-activate:hover {
            background-color: rgba(76, 175, 80, 0.2);
        }

        .btn-deactivate {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
            border: 1px solid rgba(244, 67, 54, 0.2);
        }

        .btn-deactivate:hover {
            background-color: rgba(244, 67, 54, 0.2);
        }

        /* Footer */
        .footer {
            background-color: var(--white);
            padding: 20px 30px;
            text-align: center;
            color: var(--text-light);
            border-top: 1px solid #eaeaea;
            font-size: 0.9rem;
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .sidebar {
                width: 250px;
            }

            .actions {
                flex-direction: column;
            }
        }

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
                padding: 15px;
            }

            .sidebar a i {
                font-size: 1.4rem;
                margin: 0;
            }

            .sidebar-header {
                margin-bottom: 20px;
            }

            .logo {
                height: 60px;
                margin-left: 0;
            }

            .content {
                width: calc(100% - 80px);
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            transition: var(--transition);
        }

        .modal-content {
            background-color: var(--white);
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 50%;
            max-width: 600px;
            box-shadow: var(--shadow-lg);
            transform: translateY(0);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal h2 {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent);
            font-weight: 600;
        }

        .modal form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal label {
            color: var(--dark);
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }

        .modal input,
        .modal select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .modal input:focus,
        .modal select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(255, 233, 125, 0.2);
            outline: none;
        }

        .modal button[type="submit"] {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal button[type="submit"]:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .close {
            color: #777;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            position: absolute;
            right: 25px;
            top: 15px;
        }

        .close:hover {
            color: var(--primary);
        }

        .image-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .image-upload img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            margin-bottom: 10px;
        }

        .image-upload input[type="file"] {
            width: 100%;
            padding: 8px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row>div {
            flex: 1;
        }

        .form-group {
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                padding: 20px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/CCIS-Logo-Official.png') }}" alt="College Logo" class="logo">
                <h3><i>Faculty Project Management System</i></h3>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span></a>
                <a href="approvals.php"><i class="fa-solid fa-check-to-slot"></i> <span>Approvals</span></a>
                <a href="reports.php"><i class="fa-solid fa-chart-pie"></i> <span>Reports</span></a>
                <a href="faculty_management.php" class="active"><i class="fa-solid fa-users-gear"></i> <span>Faculty
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
                    <h1 class="page-title"><i class="fa-solid fa-users-gear"></i> Faculty Management</h1>
                </div>
                <div class="header-right">
                    <div class="notification-bell">
                        <i class="fa-solid fa-bell"></i>
                        <div class="notification-badge">3</div>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">A</div>
                        <div>
                            <p style="font-weight: 600; margin: 0;">Admin User</p>
                            <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content">
                <h2>Faculty Management</h2>
                <div class="filters">
                    <div class="filter-title">Filter by:</div>
                    <select>
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" placeholder="Search by ID, name or email..." id="searchInput">
                    </div>
                    <button id="addFacultyBtn"><i class="fa-solid fa-user-plus"></i> Add New Faculty</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Faculty ID <i class="fa-solid fa-key" style="color: #ffde26;"></i></th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Account Creation Date</th>
                            <th>Role ID</th>
                            <th>Status</th>
                            <th>Profile Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>F001</td>
                            <td>John</td>
                            <td>Doe</td>
                            <td>john.doe@umak.edu.ph</td>
                            <td>2023-01-15</td>
                            <td>1</td>
                            <td><span class="status status-active"><i class="fa-solid fa-circle-check"></i>
                                    Active</span></td>
                            <td><img src="{{ asset('images/placeholder.jpg') }}" alt="Profile"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                            <td class="actions">
                                <a href="#" class="btn btn-edit" id="editFacultyBtn"><i
                                        class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="#" class="btn btn-deactivate" id="activateBtn"><i
                                        class="fa-solid fa-user-slash"></i> Deactivate</a>
                            </td>
                        </tr>
                        <tr>
                            <td>F002</td>
                            <td>Jane</td>
                            <td>Smith</td>
                            <td>jane.smith@umak.edu.ph</td>
                            <td>2023-02-20</td>
                            <td>1</td>
                            <td><span class="status status-inactive"><i class="fa-solid fa-circle-xmark"></i>
                                    Inactive</span></td>
                            <td><img src="{{ asset('images/placeholder.jpg') }}" alt="Profile"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                            <td class="actions">
                                <a href="#" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="#" class="btn btn-activate"><i class="fa-solid fa-user-check"></i> Activate</a>
                            </td>
                        </tr>
                        <tr>
                            <td>F003</td>
                            <td>Robert</td>
                            <td>Johnson</td>
                            <td>robert.johnson@umak.edu.ph</td>
                            <td>2023-03-10</td>
                            <td>2</td>
                            <td><span class="status status-active"><i class="fa-solid fa-circle-check"></i>
                                    Active</span></td>
                            <td><img src="{{ asset('images/placeholder.jpg') }}" alt="Profile"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                            <td class="actions">
                                <a href="#" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="#" class="btn btn-deactivate"><i class="fa-solid fa-user-slash"></i>
                                    Deactivate</a>
                            </td>
                        </tr>
                        <tr>
                            <td>F004</td>
                            <td>Maria</td>
                            <td>Garcia</td>
                            <td>maria.garcia@umak.edu.ph</td>
                            <td>2023-04-05</td>
                            <td>1</td>
                            <td><span class="status status-active"><i class="fa-solid fa-circle-check"></i>
                                    Active</span></td>
                            <td><img src="{{ asset('images/placeholder.jpg') }}" alt="Profile"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"></td>
                            <td class="actions">
                                <a href="#" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <a href="#" class="btn btn-deactivate"><i class="fa-solid fa-user-slash"></i>
                                    Deactivate</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- add faculty modal-->
            <div id="addFacultyModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2><i class="fa-solid fa-user-plus"></i> Add New Faculty</h2>
                    <form action="{{ url('/admin/faculty-management/store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="image-upload">
                            <label>Profile Picture</label>
                            <img src="{{ asset('images/placeholder.jpg') }}" class="img-thumbnail" alt="Profile Image"
                                id="employee_image">
                            <input type="file" class="form-control" name="profile_image" id="imageUpload"
                                accept="image/*">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="faculty_id">Faculty ID:</label>
                                <input type="text" name="faculty_id" id="faculty_id" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" name="first_name" id="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" name="last_name" id="last_name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" name="email" id="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" name="password" id="password" readonly style="flex-grow: 1;">
                                <button type="button" id="generatePasswordBtn"
                                    style="white-space: nowrap;">Generate</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="account_creation_date">Account Creation Date:</label>
                                <input type="date" name="account_creation_date" id="account_creation_date"
                                    value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="role_id">Role:</label>
                                <select name="role_id" id="role_id" required>
                                    <option value="">Select Role</option>
                                    <option value="1">Faculty</option>
                                    <option value="2">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select name="status" id="status" required>
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <button type="submit">Add Faculty</button>
                    </form>
                </div>
            </div>

            <!--edit faculty modal-->
            <div id="editFacultyModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2><i class="fa-solid fa-pen-to-square"></i> Edit Faculty Profile</h2>
                    <form action="{{ url('/admin/faculty-management/edit') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="image-upload">
                            <label>Profile Picture</label>
                            <img src="{{ asset('images/placeholder.jpg') }}" class="img-thumbnail" alt="Profile Image"
                                id="edit_employee_image">
                            <input type="file" class="form-control" name="profile_image" id="edit_imageUpload"
                                accept="image/*">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_faculty_id">Faculty ID:</label>
                                <input type="text" name="faculty_id" id="edit_faculty_id" required readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_first_name">First Name:</label>
                                <input type="text" name="first_name" id="edit_first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_last_name">Last Name:</label>
                                <input type="text" name="last_name" id="edit_last_name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_email">Email Address:</label>
                            <input type="email" name="email" id="edit_email" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_account_creation_date">Account Creation Date:</label>
                                <input type="date" name="account_creation_date" id="edit_account_creation_date"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="edit_role_id">Role:</label>
                                <select name="role_id" id="edit_role_id" required>
                                    <option value="">Select Role</option>
                                    <option value="1">Faculty</option>
                                    <option value="2">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_status">Status:</label>
                            <select name="status" id="edit_status" required>
                                <option value="">Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="submit-btn">Update Faculty</button>
                    </form>
                </div>
            </div>

            <div class="footer">
                <p> 2025 University of Makati - CCIS Faculty Project Management System v1.0 | <a href="#">Help
                        Center</a> | <a href="#">Contact Support</a></p>
            </div>
        </div>
    </div>
</body>

<script>
    // Add Faculty Modal functionality
    const addModal = document.getElementById('addFacultyModal');
    const addBtn = document.getElementById('addFacultyBtn');
    const addSpan = addModal.querySelector('.close');

    addBtn.onclick = function () {
        addModal.style.display = "block";
        document.body.style.overflow = "hidden"; // Prevent scrolling when modal is open
    }

    addSpan.onclick = function () {
        addModal.style.display = "none";
        document.body.style.overflow = "auto"; // Enable scrolling again
    }

    // Edit Faculty Modal functionality
    const editModal = document.getElementById('editFacultyModal');
    const editBtns = document.querySelectorAll('.btn-edit');
    const editSpan = editModal.querySelector('.close');

    editBtns.forEach(btn => {
        btn.onclick = function (e) {
            e.preventDefault();

            // In a real application, you would fetch faculty data from the server using the faculty ID
            // For now, we'll use placeholder data for demonstration
            const row = this.closest('tr');
            const facultyId = row.querySelector('td:nth-child(1)').textContent;
            const firstName = row.querySelector('td:nth-child(2)').textContent;
            const lastName = row.querySelector('td:nth-child(3)').textContent;
            const email = row.querySelector('td:nth-child(4)').textContent;
            const accountCreationDate = row.querySelector('td:nth-child(5)').textContent;
            const role = row.querySelector('td:nth-child(6)').textContent;
            const status = row.querySelector('td:nth-child(7) .status').textContent.includes('Active') ? 'Active' : 'Inactive';

            // Populate the edit form
            document.getElementById('edit_faculty_id').value = facultyId;
            document.getElementById('edit_first_name').value = firstName;
            document.getElementById('edit_last_name').value = lastName;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_account_creation_date').value = accountCreationDate;
            document.getElementById('edit_role_id').value = role === 'Faculty' ? '1' : '2';
            document.getElementById('edit_status').value = status;

            // Show the modal
            editModal.style.display = "block";
            document.body.style.overflow = "hidden";
        };
    });

    editSpan.onclick = function () {
        editModal.style.display = "none";
        document.body.style.overflow = "auto";
    }

    // Activate/Deactivate button toggle functionality
    const activateBtns = document.querySelectorAll('.btn-deactivate, .btn-activate');

    activateBtns.forEach(btn => {
        btn.onclick = function (e) {
            e.preventDefault();

            const row = this.closest('tr');
            const statusCell = row.querySelector('td:nth-child(7) .status');
            const isActive = statusCell.textContent.includes('Active');

            if (isActive) {
                // Change to inactive
                statusCell.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Inactive';
                statusCell.className = 'status status-inactive';
                this.innerHTML = '<i class="fa-solid fa-user-check"></i> Activate';
                this.className = 'btn btn-activate';
            } else {
                // Change to active
                statusCell.innerHTML = '<i class="fa-solid fa-circle-check"></i> Active';
                statusCell.className = 'status status-active';
                this.innerHTML = '<i class="fa-solid fa-user-slash"></i> Deactivate';
                this.className = 'btn btn-deactivate';
            }

            // In a real application, you would send an AJAX request to update the status in the database
        };
    });

    // Close modals when clicking outside
    window.onclick = function (event) {
        if (event.target == addModal) {
            addModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
        if (event.target == editModal) {
            editModal.style.display = "none";
            document.body.style.overflow = "auto";
        }
    }

    // Image upload preview for add modal
    document.getElementById("imageUpload").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("employee_image").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Image upload preview for edit modal
    document.getElementById("edit_imageUpload").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("edit_employee_image").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Generate password
    document.getElementById("generatePasswordBtn").addEventListener("click", function () {
        const password = generatePassword();
        document.getElementById("password").value = password;
    });

    function generatePassword(length = 12) {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$!";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    // Generate a password by default when the page loads
    window.addEventListener('DOMContentLoaded', function () {
        document.getElementById("password").value = generatePassword();
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const facultyId = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const firstName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const lastName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            if (facultyId.includes(searchValue) ||
                firstName.includes(searchValue) ||
                lastName.includes(searchValue) ||
                email.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</html>