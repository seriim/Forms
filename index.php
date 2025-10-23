<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Forgiveness Application System</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Grade Forgiveness Application System</h1>
        
        <div class="authors">
            <h2>Developed by:</h2>
            <p><strong>Group Members:</strong></p>
            <ul>
                <li>Serena Morris - 2208659</li>
                <li>Joshane Beecher - 2304845</li>
                <li>Jahzeal Simms - 2202446</li>
                <li>Abigayle </li>
            </ul>
            <p><strong>Course:</strong> PHP Programming</p>
            <p><strong>Institution:</strong> Computer Science Department</p>
            <p><strong>Date:</strong> <?php echo date('F Y'); ?></p>
        </div>
        
        <div class="description">
            <p>This system allows students to apply for grade forgiveness through a multi-step form process.</p>
            <p>The application includes student information, module details, and approval signatures.</p>
            <p><strong>Features:</strong> Multi-page form, validation, session management, file storage, login system</p>
        </div>
        
        <div class="btn-container">
            <a href="login.php" class="btn btn-primary">Login to System</a>
        </div>
        
        <p style="text-align: center; color: #666; font-size: 14px; margin-top: 20px;">Please login to access the system features</p>
    </div>
    <script src="assets/script.js"></script>
</body>
</html>
