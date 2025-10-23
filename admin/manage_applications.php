<?php
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$message_type = '';

// Handle approve/deny
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['primary_key'])) {
    $primary_key = trim($_POST['primary_key']);
    $action = trim($_POST['action']);
    $filename = DATA_FILE;
    
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $found = false;
        
        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines[$i], 'Primary Key: ' . $primary_key) !== false) {
                for ($j = $i; $j < $i + 20 && $j < count($lines); $j++) {
                    if (strpos(trim($lines[$j]), 'Status:') === 0) {
                        if ($action === 'approve') {
                            $lines[$j] = 'Status: Approved';
                            $message = 'Application approved!';
                            $message_type = 'success';
                        } elseif ($action === 'deny') {
                            $lines[$j] = 'Status: Denied';
                            $message = 'Application denied!';
                            $message_type = 'success';
                        }
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    file_put_contents($filename, implode("\n", $lines));
                    break;
                }
            }
        }
        
        if (!$found) {
            $message = 'Application not found.';
            $message_type = 'error';
        }
    }
}

// Get all applications
function get_apps() {
    $filename = DATA_FILE;
    $apps = array();
    
    if (!file_exists($filename)) return $apps;
    
    $content = file_get_contents($filename);
    $parts = explode('=== GRADE FORGIVENESS APPLICATION ===', $content);
    
    foreach ($parts as $part) {
        if (empty(trim($part))) continue;
        
        $app = array();
        $lines = explode("\n", $part);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'Primary Key:') === 0) {
                $app['primary_key'] = trim(substr($line, 12));
            } elseif (strpos($line, 'Student Name:') === 0) {
                $app['student_name'] = trim(substr($line, 13));
            } elseif (strpos($line, 'Student Id:') === 0) {
                $app['student_id'] = trim(substr($line, 11));
            } elseif (strpos($line, 'Status:') === 0) {
                $app['status'] = trim(substr($line, 7));
            } elseif (strpos($line, 'Timestamp:') === 0) {
                $app['timestamp'] = trim(substr($line, 10));
            }
        }
        
        if (!empty($app['primary_key'])) {
            $apps[] = $app;
        }
    }
    
    return $apps;
}

$applications = get_apps();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications</title>
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
        
        .message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table th {
            background: #f0f0f0;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            color: #333;
        }
        
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            color: #333;
        }
        
        table tr:hover {
            background: #f9f9f9;
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-denied {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-small {
            padding: 6px 12px;
            background: #333;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }
        
        .btn-small:hover {
            background: #555;
        }
        
        .btn-approve {
            background: #28a745;
        }
        
        .btn-approve:hover {
            background: #218838;
        }
        
        .btn-deny {
            background: #dc3545;
        }
        
        .btn-deny:hover {
            background: #c82333;
        }
        
        .no-records {
            text-align: center;
            padding: 30px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        
        <div class="header">
            <h1>Manage Applications</h1>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($applications)): ?>
            <div class="no-records">
                <p>No applications found.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Primary Key</th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['primary_key'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($app['student_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($app['student_id'] ?? 'N/A'); ?></td>
                            <td>
                                <?php 
                                $status = $app['status'] ?? 'Pending';
                                $status_class = 'status-pending';
                                if ($status === 'Approved') $status_class = 'status-approved';
                                if ($status === 'Denied') $status_class = 'status-denied';
                                ?>
                                <span class="status <?php echo $status_class; ?>"><?php echo htmlspecialchars($status); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($app['timestamp'] ?? 'N/A'); ?></td>
                            <td>
                                <div class="actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="primary_key" value="<?php echo htmlspecialchars($app['primary_key']); ?>">
                                        <button type="submit" class="btn-small btn-approve">Approve</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="deny">
                                        <input type="hidden" name="primary_key" value="<?php echo htmlspecialchars($app['primary_key']); ?>">
                                        <button type="submit" class="btn-small btn-deny">Deny</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
