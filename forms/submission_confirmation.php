<?php
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}

// Get submission data from session
if (!isset($_SESSION['form_data'])) {
    header('Location: ../student/studentAccess.php');
    exit;
}

$data = $_SESSION['form_data'];
$student_info = $data['student_info'] ?? array();
$modules = $data['modules'] ?? array();
$signatures = $data['signatures'] ?? array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted Successfully</title>
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
            padding: 30px;
            border-radius: 5px;
        }
        
        .success-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        
        .success-header h1 {
            color: #155724;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .success-header p {
            color: #666;
            font-size: 16px;
        }
        
        .confirmation-section {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 20px;
        }
        
        .confirmation-section h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            width: 200px;
            color: #333;
        }
        
        .info-value {
            flex: 1;
            color: #666;
        }
        
        .module-item {
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 10px;
        }
        
        .module-item h3 {
            color: #333;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .module-detail {
            display: flex;
            padding: 5px 0;
            font-size: 14px;
        }
        
        .module-detail-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        
        .module-detail-value {
            flex: 1;
            color: #666;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #333;
            color: white;
        }
        
        .btn-primary:hover {
            background: #555;
        }
        
        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 3px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-header">
            <h1>✓ Application Submitted Successfully</h1>
            <p>Your grade forgiveness application has been received and is now under review.</p>
        </div>
        
        <div class="success-message">
            <strong>Thank you!</strong> Your application has been saved. An administrator will review your request and notify you of the decision.
        </div>
        
        <div class="confirmation-section">
            <h2>Student Information</h2>
            <div class="info-row">
                <span class="info-label">Student Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($student_info['student_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Student ID:</span>
                <span class="info-value"><?php echo htmlspecialchars($student_info['student_id'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($student_info['email'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value"><?php echo htmlspecialchars($student_info['phone'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Course of Study:</span>
                <span class="info-value"><?php echo htmlspecialchars($student_info['course_of_study'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Academic Year:</span>
                <span class="info-value"><?php echo htmlspecialchars($student_info['semester'] ?? 'N/A') . ' ' . htmlspecialchars($student_info['academic_year'] ?? 'N/A'); ?></span>
            </div>
        </div>
        
        <div class="confirmation-section">
            <h2>Modules Applied For (<?php echo count($modules); ?>)</h2>
            <?php foreach ($modules as $index => $module): ?>
                <div class="module-item">
                    <h3>Module <?php echo $index + 1; ?></h3>
                    <div class="module-detail">
                        <span class="module-detail-label">Module Name:</span>
                        <span class="module-detail-value"><?php echo htmlspecialchars($module['module_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="module-detail">
                        <span class="module-detail-label">Module Code:</span>
                        <span class="module-detail-value"><?php echo htmlspecialchars($module['module_code'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="module-detail">
                        <span class="module-detail-label">Academic Year:</span>
                        <span class="module-detail-value"><?php echo htmlspecialchars($module['academic_year'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="module-detail">
                        <span class="module-detail-label">Session:</span>
                        <span class="module-detail-value"><?php echo htmlspecialchars($module['academic_session'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="module-detail">
                        <span class="module-detail-label">Initial Grade:</span>
                        <span class="module-detail-value"><?php echo htmlspecialchars($module['initial_grade'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="confirmation-section">
            <h2>Submission Details</h2>
            <div class="info-row">
                <span class="info-label">Student Signature:</span>
                <span class="info-value"><?php echo htmlspecialchars($signatures['student_signature'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date Submitted:</span>
                <span class="info-value"><?php echo htmlspecialchars($signatures['student_date'] ?? 'N/A'); ?></span>
            </div>
        </div>
        
        <div class="button-group">
            <a href="../student/studentAccess.php" class="btn btn-primary">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>
