<?php
session_start();
require_once 'includes/config.php';

// Handle login form submission
if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['pw'];
    
    // Check for student credentials (username: student, password: 240825)
    if($username === 'student' && $password === '240825')
    {
        $_SESSION['role'] = 'student';
        $_SESSION['username'] = $username;
        header('Location: student/studentAccess.php');
        exit;
    }
    // Check for admin credentials
    elseif($username === 'admin' && $password === '240825')
    {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header('Location: admin/dashboard.php');
        exit;
    }
    // Check for additional student usernames
    elseif(($username === 'student1' || $username === 'student2' || $username === 'student3') && $password === '240825')
    {
        $_SESSION['role'] = 'student';
        $_SESSION['username'] = $username;
        header('Location: student/studentAccess.php');
        exit;
    }
    // Check for additional admin usernames
    elseif(($username === 'admin1' || $username === 'admin2' || $username === 'staff') && $password === '240825')
    {
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = $username;
        header('Location: admin/dashboard.php');
        exit;
    }
    else
    {
        $_SESSION['errorMsg'] = "Invalid credentials. Use 'student'/'admin' with password '240825'";
        header("Location: login.php");
        exit;
    }
}
?>
<html>
	<head>
		<title>Login Page</title>
	
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
			
			.login-container {
				background: white;
				padding: 30px;
				width: 100%;
				max-width: 400px;
			}
			
			.login-header {
				text-align: center;
				margin-bottom: 30px;
			}
			
			.login-header h2 {
				color: #333;
				font-size: 24px;
				margin-bottom: 10px;
			}
			
			.login-header p {
				color: #666;
				font-size: 14px;
			}
			
			.form-group {
				margin-bottom: 15px;
			}
			
			.form-group label {
				display: block;
				margin-bottom: 5px;
				color: #333;
				font-size: 14px;
				font-weight: bold;
			}
			
			input[type="text"],
			input[type="password"] {
				width: 100%;
				padding: 10px;
				border: 1px solid #ccc;
				border-radius: 3px;
				font-size: 14px;
			}
			
			input[type="text"]:focus,
			input[type="password"]:focus {
				outline: none;
				border-color: #333;
			}
			
			.login-btn {
				width: 100%;
				padding: 12px;
				background: #333;
				color: white;
				border: none;
				border-radius: 3px;
				font-size: 14px;
				font-weight: bold;
				cursor: pointer;
				margin-top: 10px;
			}
			
			.login-btn:hover {
				background: #555;
			}
			
			.error-message {
				background: #ffe6e6;
				color: #cc0000;
				padding: 10px;
				border-radius: 3px;
				margin-bottom: 15px;
				font-size: 14px;
				border: 1px solid #ff9999;
			}
			
			.credentials-info {
				background: #f9f9f9;
				border: 1px solid #ccc;
				border-radius: 3px;
				padding: 15px;
				margin-top: 20px;
				font-size: 13px;
				color: #333;
			}
			
			.credentials-info h4 {
				margin-bottom: 8px;
				font-size: 14px;
			}
			
			.credentials-info ul {
				margin-left: 16px;
			}
			
			.credentials-info li {
				margin-bottom: 4px;
			}
		</style>
	</head>

	<body>
		<div class="login-container">
			<div class="login-header">
				<h2>Welcome Back</h2>
				<p>Sign in to access the Grade Forgiveness System</p>
			</div>
			
			<form method="POST" action="">
				<?php
				$errorMsg = $_SESSION['errorMsg'] ?? '';
				?>
				
				<?php if($errorMsg):?>
					<div class="error-message">
						<?php
							echo htmlspecialchars($errorMsg);
							unset($_SESSION['errorMsg']);
						?>
					</div>
				<?php endif?>
				
				<div class="form-group">
					<label for="username">Username</label>
					<input type="text" name="username" id="username" placeholder="Enter your username" required>
				</div>
				
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="pw" id="password" placeholder="Enter your password" required>
				</div>
				
				<button type="submit" name="login" class="login-btn">Sign In</button>
			</form>
			
			<div class="credentials-info">
				<h4>Demo Credentials:</h4>
				<ul>
					<li><strong>Student:</strong> student / 240825</li>
					<li><strong>Admin:</strong> admin / 240825</li>
				</ul>
			</div>
			
			<div style="text-align: center; margin-top: 20px;">
				<a href="index.php" style="color: #2c5aa0; text-decoration: none; font-weight: 600; font-size: 14px;">
					← Back to Home
				</a>
			</div>
		</div>
</html>