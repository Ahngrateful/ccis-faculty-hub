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
$query = "SELECT s.*, 
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
            'id' => $row['id'],
            'file_path' => $row['file_path'],
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
$query = "SELECT s.*, 
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
function getRoleName($conn, $role_id) {
    $query = "SELECT role_name FROM roles WHERE id = ?";
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
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .card-body {
            padding: 30px;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            text-align: left;
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary);
            background-color: rgba(0, 104, 52, 0.05);
            border-bottom: 2px solid var(--primary-light);
            position: relative;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: rgba(0, 104, 52, 0.02);
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
            gap: 8px;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            border: none;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .btn-approve {
            background-color: rgba(117, 217, 121, 0.15);
            color: var(--primary);
        }

        .btn-approve:hover {
            background-color: rgba(117, 217, 121, 0.3);
        }

        .btn-reject {
            background-color: rgba(255, 0, 0, 0.1);
            color: #e74c3c;
        }

        .btn-reject:hover {
            background-color: rgba(255, 0, 0, 0.2);
        }

        /* Status */
        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-approved {
            background-color: rgba(117, 217, 121, 0.15);
            color: var(--primary);
        }

        .status-rejected {
            background-color: rgba(255, 0, 0, 0.1);
            color: #e74c3c;
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
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 104, 52, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        select {
            padding: 10px 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            background-color: white;
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
        }

        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 104, 52, 0.1);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }

            .sidebar h3, .sidebar a span {
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

            .search-box, select {
                width: 100%;
            }

            .card-body {
                padding: 15px;
            }

            th, td {
                padding: 10px;
            }
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
            <a href="approvals.php" class="active"><i class="fa-solid fa-check-to-slot"></i> <span>Approvals</span></a>
            <a href="reports.php"><i class="fa-solid fa-chart-pie"></i> <span>Reports</span></a>
            <a href="faculty_management.php"><i class="fa-solid fa-users-gear"></i> <span>Faculty Management</span></a>
            <a href="ched_compli-audit.php"><i class="fa-solid fa-clipboard-check"></i> <span>CHED Compliance Audit</span></a>
            <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
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
                        <p style="font-weight: 600; margin: 0;"><?php echo $_SESSION['admin_name'] ?? 'Admin User'; ?></p>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Administrator</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fa-solid fa-hourglass-half"></i>
            Pending Submissions
        </h2>
        <div class="filters">
            <div class="search-box">
                <i class="fa-solid fa-search"></i>
                <input type="text" placeholder="Search faculty or requirement..." id="searchInput">
            </div>
            <select id="requirementFilter">
                <option value="">All Requirements</option>
                <?php foreach($requirements as $req): ?>
                    <option value="<?php echo htmlspecialchars($req['id']); ?>"><?php echo htmlspecialchars($req['requirement_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($pendingSubmissions)): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Faculty Name</th>
                            <th>Requirement</th>
                            <th>Date Submitted</th>
                            <th>Files</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingSubmissions as $submission): ?>
                            <tr>
                                <td>
                                    <div class="faculty-info">
                                        <img src="<?php echo $submission['faculty']['profile_image'] ?? '../assets/placeholder.jpg'; ?>" 
                                            alt="<?php echo $submission['faculty']['first_name']; ?>" 
                                            class="faculty-avatar">
                                        <div>
                                            <div class="faculty-name"><?php echo $submission['faculty']['first_name']; ?> <?php echo $submission['faculty']['last_name']; ?></div>
                                            <div class="faculty-role"><?php echo $submission['faculty']['role']['role_name'] ?? 'Faculty'; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $submission['requirement']['requirement_name']; ?></td>
                                <td><?php echo date('M d, Y h:ia', strtotime($submission['updated_at'])); ?></td>
                                <td>
                                    <div class="file-list">
                                        <?php if (!empty($submission['file_path'])): ?>
                                            <a href="<?php echo $submission['file_path']; ?>" class="document-link" target="_blank">
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
                                        <button class="btn btn-sm btn-approve" onclick="approveSubmission(<?php echo $submission['id']; ?>)">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-sm btn-reject" onclick="rejectSubmission(<?php echo $submission['id']; ?>)">
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
                        <?php foreach($recentlyProcessed as $item): ?>
                            <tr>
                                <td><?php echo $item['faculty']['first_name']; ?> <?php echo $item['faculty']['last_name']; ?></td>
                                <td><?php echo $item['requirement']['requirement_name']; ?></td>
                                <td><?php echo date('M d, Y h:ia', strtotime($item['updated_at'])); ?></td>
                                <td>
                                    <span class="status status-<?php echo $item['status']; ?>">
                                        <i class="fa-solid fa-<?php echo $item['status'] == 'approved' ? 'check-circle' : 'times-circle'; ?>"></i>
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
function approveSubmission(id) {
    if (confirm('Are you sure you want to approve this submission?')) {
        window.location.href = 'approve.php?id=' + id;
    }
}

function rejectSubmission(id) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        window.location.href = 'reject.php?id=' + id + '&reason=' + encodeURIComponent(reason);
    }
}

// Search
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const facultyName = row.querySelector('.faculty-name').textContent.toLowerCase();
        const requirementName = row.cells[1].textContent.toLowerCase();

        row.style.display = (facultyName.includes(searchText) || requirementName.includes(searchText)) ? '' : 'none';
    });
});

// Filter
document.getElementById('requirementFilter').addEventListener('change', function() {
    const selected = this.value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const reqName = row.cells[1].textContent;
        row.style.display = (!selected || reqName.includes(selected)) ? '' : 'none';
    });
});
</script>
</body>
</html>