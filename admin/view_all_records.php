<?php
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

function get_all_apps() {
    $filename = DATA_FILE;
    $apps = array();
    
    if (!file_exists($filename)) return $apps;
    
    $content = file_get_contents($filename);
    $parts = explode('=== GRADE FORGIVENESS APPLICATION ===', $content);
    
    foreach ($parts as $part) {
        if (empty(trim($part))) continue;
        
        // Only include complete applications (those with END OF APPLICATION marker)
        if (strpos($part, 'END OF APPLICATION') === false) continue;
        
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
            } elseif (strpos($line, 'Email:') === 0) {
                $app['email'] = trim(substr($line, 6));
            } elseif (strpos($line, 'Course Of Study:') === 0) {
                $app['course_of_study'] = trim(substr($line, 16));
            }
        }
        
        if (!empty($app['primary_key'])) {
            $apps[] = $app;
        }
    }
    
    return $apps;
}

$applications = get_all_apps();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View All Grade Forgiveness Records</title>
    <style>
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
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
            padding: 8px 16px;
            background: #f0f0f0;
            border-radius: 3px;
        }
        .back-link:hover {
            background: #ddd;
        }
        .record {
            background: #f9f9f9;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 15px 0;
        }
        .record h3 {
            color: #333;
            margin-top: 0;
        }
        .record-info {
            margin: 10px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-denied {
            background-color: #f8d7da;
            color: #721c24;
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
        <h1>All Grade Forgiveness Records</h1>
        
        <?php
        $record_count = 0;
        if (!empty($applications)) {
            foreach ($applications as $app) {
                $record_count++;
                $status = $app['status'] ?? 'Pending';
                $status_class = 'status-pending';
                if ($status === 'Approved') $status_class = 'status-approved';
                if ($status === 'Denied') $status_class = 'status-denied';
                
                echo "<div class='record'>";
                echo "<h3>" . htmlspecialchars($app['primary_key']) . "</h3>";
                echo "<p><strong>Status:</strong> <span class='status-badge $status_class'>" . htmlspecialchars($status) . "</span></p>";
                echo "<div class='record-info'>";
                echo "<p><strong>Student Name:</strong> " . htmlspecialchars($app['student_name'] ?? 'N/A') . "</p>";
                echo "<p><strong>Student ID:</strong> " . htmlspecialchars($app['student_id'] ?? 'N/A') . "</p>";
                echo "<p><strong>Course:</strong> " . htmlspecialchars($app['course_of_study'] ?? 'N/A') . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($app['email'] ?? 'N/A') . "</p>";
                echo "<p><strong>Submitted:</strong> " . htmlspecialchars($app['timestamp'] ?? 'N/A') . "</p>";
                echo "</div>";
                echo "</div>";
            }
            echo "<p style='text-align: center; color: #666; margin-top: 20px;'><strong>Total Records:</strong> $record_count</p>";
        } else {
            echo "<div class='no-records'>No grade forgiveness records found.</div>";
        }
        ?>
    </div>
</body>
</html>
