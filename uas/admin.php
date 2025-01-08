<?php
session_start();
require_once 'admin_functions.php';

// Debug connection
try {
    // Tambahkan ini di awal file untuk debug
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Test database connection
    $test = $pdo->query('SELECT 1');
    error_log("Database connection successful");
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login-admin.php");
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $submission_id = $_POST['submission_id'];
    $new_status = $_POST['new_status'];
    $note = $_POST['note'];

    try {
        $stmt = $pdo->prepare("UPDATE submissions SET status = ?, note = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$new_status, $note, $submission_id]);
        
        if($result) {
            $_SESSION['success'] = 'Status updated successfully';
            error_log("Status updated successfully for submission ID: " . $submission_id);
        } else {
            $_SESSION['error'] = 'Failed to update status';
            error_log("Failed to update status for submission ID: " . $submission_id);
        }
    } catch (PDOException $e) {
        error_log("Error updating status: " . $e->getMessage());
        $_SESSION['error'] = 'Error updating status: ' . $e->getMessage();
    }
    header("Location: admin.php");
    exit();
}

// Fetch all submissions with debug
try {
    // Simplified query first to test
    $query = "SELECT * FROM submissions ORDER BY tanggal_submit DESC";
    error_log("Executing query: " . $query);
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the number of submissions found
    error_log("Found " . count($submissions) . " submissions");
    
    if(empty($submissions)) {
        error_log("No submissions found in database");
    }
} catch (PDOException $e) {
    error_log("Error fetching submissions: " . $e->getMessage());
    $submissions = [];
    die("Error fetching submissions: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
         .history-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1001;
        }
        .history-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f6fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        .btn-action {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .btn-edit {
            background: #007bff;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .status-submitted { background: #ffc107; color: #000; }
        .status-process { background: #17a2b8; color: #fff; }
        .status-approved { background: #28a745; color: #fff; }
        .status-rejected { background: #dc3545; color: #fff; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border -radius: 8px;
            width: 90%;
            max-width: 500px;
            margin: 2rem auto;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: #28a745;
            color: white;
        }
        .alert-error {
            background: #dc3545;
            color: white;
        }
        </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div>
                <span>Debug Info: Found <?php echo count($submissions); ?> submissions</span>
                <a href="admin-logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Submission Management</h2>
            <!-- Add debug info -->
            <div class="debug-info" style="background: #f8f9fa; padding: 10px; margin-bottom: 10px;">
                <p>PDO Connection Status: <?php echo $pdo ? 'Connected' : 'Not Connected'; ?></p>
                <p>Number of Submissions: <?php echo count($submissions); ?></p>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Tanggal Submit</th>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $index => $submission): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($submission['id']); ?></td>
                                <td><?php echo htmlspecialchars($submission['tanggal_submit']); ?></td>
                                <td><?php echo htmlspecialchars($submission['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($submission['full_name']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($submission['status'] ?? 'pending'); ?>">
                                        <?php echo htmlspecialchars($submission['status'] ?? 'Pending'); ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="openStatusModal(<?php echo $submission['id']; ?>)" class="btn-action btn-edit">
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">
                                No submissions found. 
                                <?php if(isset($pdo)): ?>
                                    Database connected but no records.
                                <?php else: ?>
                                    Database connection issue.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h3>Update Status</h3>
            <form method="POST" action="">
                <input type="hidden" id="submission_id" name="submission_id">
                <div class="form-group">
                    <label>Status</label>
                    <select name="new_status" required>
                        <option value="Submitted">Submitted</option>
                        <option value="Process">In Process</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <textarea name="note" rows="3"></textarea>
                </div>
                <button type="submit" name="update_status" class="btn-action btn-edit">Update</button>
                <button type="button" onclick="closeStatusModal()" class="btn-action btn-delete">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(id) {
            document.getElementById('submission_id').value = id;
            document.getElementById('statusModal').style.display = 'block';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('statusModal')) {
                closeStatusModal();
            }
        }
    </script>
</body>
</html>