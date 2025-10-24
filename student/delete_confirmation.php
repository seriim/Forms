<?php
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}

$found = false;
$student_id = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        $student_id = trim($_POST['student_id']);
        
        if (empty($student_id)) {
            $error_message = 'Please enter your Student ID.';
        } else {
            $filename = DATA_FILE;
            if (file_exists($filename)) {
                $content = file_get_contents($filename);
                // Check if student ID exists and is part of a complete application
                if (strpos($content, 'Student Id: ' . $student_id) !== false && strpos($content, 'END OF APPLICATION') !== false) {
                    $found = true;
                    $_SESSION['delete_student_id'] = $student_id;
                } else {
                    $error_message = 'No application found for this Student ID.';
                }
            } else {
                $error_message = 'No records found.';
            }
        }
    } elseif (isset($_POST['confirm_delete'])) {
        $student_id = $_SESSION['delete_student_id'] ?? '';
        
        if (!empty($student_id)) {
            $filename = DATA_FILE;
            if (file_exists($filename)) {
                $content = file_get_contents($filename);
                $lines = explode("\n", $content);
                $new_lines = array();
                $skip_lines = false;
                $found_student = false;
                
                for ($i = 0; $i < count($lines); $i++) {
                    // Look for the application header before the student ID
                    if (strpos($lines[$i], '=== GRADE FORGIVENESS APPLICATION ===') !== false) {
                        // Check if the next few lines contain our student ID
                        $found_student = false;
                        for ($j = $i; $j < min($i + 10, count($lines)); $j++) {
                            if (strpos($lines[$j], 'Student Id: ' . $student_id) !== false) {
                                $found_student = true;
                                break;
                            }
                        }
                        
                        if ($found_student) {
                            $skip_lines = true;
                        }
                    }
                    
                    if ($skip_lines) {
                        // Skip until we find the end marker
                        if (strpos($lines[$i], 'END OF APPLICATION') !== false) {
                            $skip_lines = false;
                            // Also skip the separator line after END OF APPLICATION
                            if ($i + 1 < count($lines) && strpos($lines[$i + 1], '============================================================') !== false) {
                                $i++;
                            }
                            // Skip any blank lines after
                            if ($i + 1 < count($lines) && trim($lines[$i + 1]) === '') {
                                $i++;
                            }
                            continue;
                        }
                        continue;
                    }
                    
                    $new_lines[] = $lines[$i];
                }
                
                file_put_contents($filename, implode("\n", $new_lines));
                unset($_SESSION['delete_student_id']);
                $success_message = 'Your grade forgiveness application has been deleted successfully.';
            }
        }
    } elseif (isset($_POST['cancel'])) {
        unset($_SESSION['delete_student_id']);
        header('Location: studentAccess.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Grade Forgiveness</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            border-radius: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 5px;
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
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #333;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #333;
            color: white;
        }
        
        .btn-primary:hover {
            background: #555;
        }
        
        .btn-danger {
            background: #cc0000;
            color: white;
        }
        
        .btn-danger:hover {
            background: #990000;
        }
        
        .btn-secondary {
            background: #666;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #888;
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
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            padding: 12px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 3px;
        }
        
        .back-link:hover {
            background: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Delete Grade Forgiveness</h2>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
            <a href="studentAccess.php" class="back-link">Back to Dashboard</a>
        <?php elseif (!$found): ?>
            <?php if (!empty($error_message)): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="student_id">Enter Your Student ID:</label>
                    <input type="text" id="student_id" name="student_id" placeholder="Enter your Student ID" required>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="search" class="btn btn-primary">Search</button>
                    <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        <?php else: ?>
            <div class="warning">
                <strong>WARNING:</strong> This action will permanently delete your grade forgiveness application. This cannot be undone.
            </div>
            
            <p style="margin-bottom: 20px; color: #333;">
                Are you sure you want to delete your application for Student ID: <strong><?php echo htmlspecialchars($student_id); ?></strong>?
            </p>
            
            <form method="POST">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                <div class="button-group">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                    <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
