<?php
require_once '../includes/config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$error_message = '';
$application = null;
$student_id = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = trim($_POST['student_id']);
    
    if (empty($student_id)) {
        $error_message = 'Please enter a student ID.';
    } else {
        // Get all applications and find the specific one
        $all_applications = get_all_applications();
        $found_applications = array_filter($all_applications, function($app) use ($student_id) {
            return ($app['student_id'] ?? '') === $student_id;
        });
        
        if (!empty($found_applications)) {
            // Get the most recent application for this student
            $application = end($found_applications);
        } else {
            $error_message = 'No application found for student ID: ' . htmlspecialchars($student_id);
        }
    }
}

// Function to get all applications (reuse from admin.php)
function get_all_applications() {
    $filename = DATA_FILE;
    $applications = array();
    
    if (!file_exists($filename)) {
        return $applications;
    }
    
    $file = fopen($filename, 'r');
    if ($file === false) {
        return $applications;
    }
    
    $current_app = null;
    $in_app = false;
    
    while (($line = fgets($file)) !== false) {
        $line = rtrim($line);
        
        if (strpos($line, '=== GRADE FORGIVENESS APPLICATION ===') !== false) {
            if ($current_app !== null) {
                $applications[] = $current_app;
            }
            $current_app = array();
            $in_app = true;
        } elseif ($in_app && $current_app !== null) {
            if (strpos($line, 'Student Id:') === 0) {
                $current_app['student_id'] = trim(substr($line, 11));
            } elseif (strpos($line, 'Student Name:') === 0) {
                $current_app['full_name'] = trim(substr($line, 13));
            } elseif (strpos($line, 'First Name:') === 0) {
                $current_app['first_name'] = trim(substr($line, 11));
            } elseif (strpos($line, 'Last Name:') === 0) {
                $current_app['last_name'] = trim(substr($line, 10));
            } elseif (strpos($line, 'Timestamp:') === 0) {
                $current_app['timestamp'] = trim(substr($line, 10));
            } elseif (strpos($line, 'Status:') === 0) {
                $current_app['status'] = trim(substr($line, 7));
            } elseif (strpos($line, 'Decision:') === 0) {
                $current_app['decision'] = trim(substr($line, 9));
            } elseif (strpos($line, 'Admin Comment:') === 0) {
                $current_app['admin_comment'] = trim(substr($line, 14));
            } elseif (strpos($line, 'Reviewed By:') === 0) {
                $current_app['reviewed_by'] = trim(substr($line, 12));
            } elseif (strpos($line, 'Review Date:') === 0) {
                $current_app['review_date'] = trim(substr($line, 12));
            } elseif (strpos($line, 'Module Name:') === 0) {
                if (!isset($current_app['modules'])) {
                    $current_app['modules'] = array();
                }
                $current_app['modules'][] = trim(substr($line, 12));
            } elseif (strpos($line, '=== END APPLICATION ===') !== false) {
                $in_app = false;
            }
        }
    }
    
    if ($current_app !== null) {
        $applications[] = $current_app;
    }
    
    fclose($file);
    return $applications;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View One Record</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
        
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 15px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 3px;
        }
        
        .back-link:hover {
            background: #ddd;
        }
        
        .search-form {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .search-form h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 12px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #333;
        }
        
        .btn {
            background: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        
        .btn:hover {
            background: #555;
        }
        
        .application-details {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-top: 15px;
        }
        
        .application-details h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            width: 150px;
        }
        
        .detail-value {
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-denied {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .message {
            padding: 12px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .no-records {
            text-align: center;
            color: #666;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        
        <div class="header">
            <h1>View One Record</h1>
        </div>
        
        <div class="search-form">
            <h3>Search Application</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="student_id">Student ID:</label>
                    <input type="text" name="student_id" id="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
                </div>
                <button type="submit" class="btn">Search</button>
            </form>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($application): ?>
            <div class="application-details">
                <h3>Application Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Primary Key:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($application['primary_key'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Student Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($application['full_name'] ?? $application['student_name'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Student ID:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($application['student_id'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <?php 
                        $status = $application['status'] ?? 'Pending';
                        $status_class = 'status-pending';
                        if ($status === 'Approved') $status_class = 'status-approved';
                        if ($status === 'Denied') $status_class = 'status-denied';
                        ?>
                        <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($application['timestamp'] ?? 'N/A'); ?></span>
                </div>
                <?php if (!empty($application['modules'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Modules:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(implode(', ', $application['modules'])); ?></span>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
