<?php
require_once '../includes/config.php';

// Check if all form data is available
if (!isset($_SESSION['form_data']['student_info']) || 
    !isset($_SESSION['form_data']['modules']) || 
    !isset($_SESSION['form_data']['signatures'])) {
    header('Location: index.php');
    exit();
}

// Save data to text file using the config function
$data = $_SESSION['form_data'];
$saveResult = save_application_data($data);

// Redirect to confirmation page
header('Location: submission_confirmation.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Report</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: center;
        }
        .report-section {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .report-section h2 {
            background-color: #2c5aa0;
            color: white;
            margin: 0;
            padding: 15px;
            font-size: 18px;
        }
        .report-content {
            padding: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
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
        .module-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .module-table th, .module-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .module-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signature-table th, .signature-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .signature-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #2c5aa0;
            color: white;
        }
        .btn-primary:hover {
            background-color: #1e3d6f;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GRADE FORGIVENESS APPLICATION REPORT</h1>
        
        <div class="success-message">
            <strong>Application Submitted Successfully!</strong><br>
            Your grade forgiveness application has been processed and saved to our records.
        </div>
        
        <div class="report-section">
            <h2>STUDENT INFORMATION</h2>
            <div class="report-content">
                <div class="info-row">
                    <div class="info-label">Student's Name:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['student_name']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Student ID:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['student_id']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Course of Study:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['course_of_study']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Course Code:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['course_code']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Major:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['major']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Minor:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['minor']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email Address:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['email']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone Number:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['phone']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Academic Year:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['academic_year']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Semester:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['semester']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Campus:</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['student_info']['campus']); ?></div>
                </div>
            </div>
        </div>
        
        <div class="report-section">
            <h2>MODULE INFORMATION</h2>
            <div class="report-content">
                <table class="module-table">
                    <thead>
                        <tr>
                            <th>Module Name</th>
                            <th>Module Code</th>
                            <th>Academic Year</th>
                            <th>Academic Session</th>
                            <th>Initial Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['modules'] as $module): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($module['module_name']); ?></td>
                            <td><?php echo htmlspecialchars($module['module_code']); ?></td>
                            <td><?php echo htmlspecialchars($module['academic_year']); ?></td>
                            <td><?php echo htmlspecialchars($module['academic_session']); ?></td>
                            <td><?php echo htmlspecialchars($module['initial_grade']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="report-section">
            <h2>SIGNATURES & APPROVAL</h2>
            <div class="report-content">
                <table class="signature-table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Name & Signature</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Student</td>
                            <td><?php echo htmlspecialchars($data['signatures']['student_signature']); ?></td>
                            <td><?php echo htmlspecialchars($data['signatures']['student_date']); ?></td>
                        </tr>
                        <tr>
                            <td>Advisor</td>
                            <td><em>To be completed by advisor</em></td>
                            <td><em>Pending</em></td>
                        </tr>
                        <tr>
                            <td>Programme Director/HoS/HoD</td>
                            <td><em>To be completed by program director</em></td>
                            <td><em>Pending</em></td>
                        </tr>
                        <tr>
                            <td>College/Faculty Administrator</td>
                            <td><em>To be completed by administrator</em></td>
                            <td><em>Pending</em></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="btn-container">
            <a href="../index.php" class="btn btn-primary">Start New Application</a>
            <button onclick="window.print()" class="btn btn-secondary">Print Report</button>
        </div>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
