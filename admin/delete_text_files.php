<?php
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete_files'])) {
        $files_to_delete = $_POST['files'] ?? [];
        
        if (empty($files_to_delete)) {
            $error = 'Please select at least one file to delete.';
        } else {
            $deleted_files = [];
            $failed_files = [];
            
            foreach ($files_to_delete as $file) {
                $file_path = dirname(__DIR__) . '/' . basename($file);
                if (file_exists($file_path)) {
                    if (unlink($file_path)) {
                        $deleted_files[] = basename($file);
                    } else {
                        $failed_files[] = basename($file);
                    }
                }
            }
            
            if (!empty($deleted_files)) {
                $message = 'Successfully deleted: ' . implode(', ', $deleted_files);
            }
            
            if (!empty($failed_files)) {
                $error = 'Failed to delete: ' . implode(', ', $failed_files);
            }
        }
    }
}

$data_file = dirname(__DIR__) . '/grade_forgiveness_records.txt';
$log_file = dirname(__DIR__) . '/application.log';

$text_files = [];

if (file_exists($data_file)) {
    $text_files['grade_forgiveness_records.txt'] = [
        'name' => 'grade_forgiveness_records.txt',
        'size' => filesize($data_file),
        'modified' => date('Y-m-d H:i:s', filemtime($data_file))
    ];
}

if (file_exists($log_file)) {
    $text_files['application.log'] = [
        'name' => 'application.log',
        'size' => filesize($log_file),
        'modified' => date('Y-m-d H:i:s', filemtime($log_file))
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Text Files - System Admin</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #333;
            text-decoration: none;
            padding: 8px 15px;
            background: #f0f0f0;
            border-radius: 3px;
        }
        
        .back-link:hover {
            background: #ddd;
        }
        
        .file-list {
            background: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin: 15px 0;
        }
        
        .file-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .file-item:last-child {
            border-bottom: none;
        }
        
        .file-name {
            font-weight: bold;
            color: #333;
        }
        
        .file-details {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 5px;
        }
        
        .btn-danger {
            background-color: #cc0000;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #990000;
        }
        
        .btn-secondary {
            background-color: #666;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #888;
        }
        
        .message {
            padding: 12px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .no-files {
            text-align: center;
            color: #666;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Admin Dashboard</a>
        <h1>Delete Text Files - System Admin</h1>
        
        <div class="warning">
            <strong>WARNING:</strong> This action will permanently delete the selected text files. This action cannot be undone. Only system administrators should use this feature.
        </div>
        
        <?php if($message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if(empty($text_files)): ?>
            <div class="no-files">No text files found to delete.</div>
        <?php else: ?>
            <form method="POST">
                <div class="file-list">
                    <h3>Select Files to Delete:</h3>
                    
                    <?php foreach($text_files as $file): ?>
                        <div class="file-item">
                            <input type="checkbox" name="files[]" value="<?php echo htmlspecialchars($file['name']); ?>" id="file_<?php echo md5($file['name']); ?>">
                            <label for="file_<?php echo md5($file['name']); ?>" style="margin-left: 10px; flex: 1;">
                                <div class="file-info">
                                    <div class="file-name"><?php echo htmlspecialchars($file['name']); ?></div>
                                    <div class="file-details">
                                        Size: <?php echo number_format($file['size']); ?> bytes | 
                                        Modified: <?php echo $file['modified']; ?>
                                    </div>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" name="confirm_delete_files" class="btn btn-danger">DELETE SELECTED FILES</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
