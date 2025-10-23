<?php
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$message_type = '';

// Handle signature submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['primary_key'])) {
    $primary_key = trim($_POST['primary_key']);
    $role = trim($_POST['signature_role'] ?? '');
    $name = trim($_POST['signature_name'] ?? '');
    $date = trim($_POST['signature_date'] ?? '');
    
    if (empty($role) || empty($name) || empty($date)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } else {
        $filename = DATA_FILE;
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $lines = explode("\n", $content);
            $found = false;
            
            for ($i = 0; $i < count($lines); $i++) {
                if (strpos($lines[$i], 'Primary Key: ' . $primary_key) !== false) {
                    // Find the end of this application
                    for ($j = $i; $j < count($lines); $j++) {
                        if (strpos($lines[$j], 'END OF APPLICATION') !== false) {
                            // Insert signature before END OF APPLICATION
                            $signature_line = $role . ': ' . $name . ' - ' . $date;
                            array_splice($lines, $j, 0, array($signature_line));
                            $found = true;
                            break;
                        }
                    }
                    break;
                }
            }
            
            if ($found) {
                file_put_contents($filename, implode("\n", $lines));
                $message = 'Signature added successfully!';
                $message_type = 'success';
            } else {
                $message = 'Application not found.';
                $message_type = 'error';
            }
        } else {
            $message = 'Data file not found.';
            $message_type = 'error';
        }
    }
}

// Get all applications
function get_apps_for_signing() {
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
            }
        }
        
        if (!empty($app['primary_key'])) {
            $apps[] = $app;
        }
    }
    
    return $apps;
}

$applications = get_apps_for_signing();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Signatures</title>
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
            max-width: 900px;
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
        
        .form-section {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        
        .form-section h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #333;
        }
        
        .btn-submit {
            background: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-submit:hover {
            background: #555;
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
            <h1>Add Signatures</h1>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2>Add Signature to Application</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="primary_key">Select Application:</label>
                    <select name="primary_key" id="primary_key" required>
                        <option value="">-- Select an application --</option>
                        <?php foreach ($applications as $app): ?>
                            <option value="<?php echo htmlspecialchars($app['primary_key']); ?>">
                                <?php echo htmlspecialchars($app['primary_key'] . ' - ' . $app['student_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="signature_role">Signature Role:</label>
                    <select name="signature_role" id="signature_role" required>
                        <option value="">-- Select a role --</option>
                        <option value="PD">PD - Program Director</option>
                        <option value="HOS">HOS - Head of School</option>
                        <option value="Academic Advisor">Academic Advisor</option>
                        <option value="Faculty Admin">Faculty Admin</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="signature_name">Signatory Name:</label>
                    <input type="text" name="signature_name" id="signature_name" placeholder="Enter full name" required>
                </div>
                
                <div class="form-group">
                    <label for="signature_date">Signature Date:</label>
                    <input type="date" name="signature_date" id="signature_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <button type="submit" class="btn-submit">Add Signature</button>
            </form>
        </div>
        
        <?php if (!empty($applications)): ?>
            <div class="form-section">
                <h2>Available Applications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Primary Key</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['primary_key']); ?></td>
                                <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($app['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($app['status'] ?? 'Pending'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-records">
                <p>No applications found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
