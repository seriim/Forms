<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in as student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit;
}
?>
<html>
	<head>
		<title>Grade Forgiveness: Student Page</title>
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
			}
			
			.header {
				text-align: center;
				margin-bottom: 30px;
			}
			
			.header h2 {
				color: #333;
				font-size: 24px;
				font-weight: bold;
				margin-bottom: 10px;
			}
			
			.header p {
				color: #666;
				font-size: 14px;
			}
			
			.menu-options {
				display: flex;
				flex-direction: column;
				gap: 10px;
				margin-bottom: 20px;
			}
			
			.menu-btn {
				width: 100%;
				padding: 12px 15px;
				background: #333;
				color: white;
				border: none;
				border-radius: 3px;
				font-size: 14px;
				font-weight: bold;
				cursor: pointer;
				text-align: left;
			}
			
			.menu-btn:hover {
				background: #555;
			}
			
			.menu-btn.logout {
				background: #cc0000;
				margin-top: 10px;
			}
			
			.menu-btn.logout:hover {
				background: #990000;
			}
			
			.user-info {
				background: #f9f9f9;
				border: 1px solid #ccc;
				border-radius: 3px;
				padding: 15px;
				margin-bottom: 20px;
				font-size: 14px;
				color: #333;
			}
			
			.user-info strong {
				display: block;
				margin-bottom: 5px;
			}
		</style>
	</head>
	
	<body>
		<div class="container">
			<div class="header">
				<h2>Student Dashboard</h2>
				<p>Manage your grade forgiveness applications</p>
			</div>
			
			<?php
			// Check if user is logged in as student
			if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'student')
			{
				header('Location: ../login.php');
				exit;
			}
			?>
			
			<div class="user-info">
				<strong>Logged in as:</strong> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Student'); ?>
			</div>
			
			<form method="POST" action="">
				<div class="menu-options">
					<button type="submit" name="add" class="menu-btn">
						Add New Grade Forgiveness Document
					</button>
					
					<button type="submit" name="edit" class="menu-btn">
						Edit Grade Forgiveness Document
					</button>
					
					<button type="submit" name="delete" class="menu-btn">
						Delete Grade Forgiveness
					</button>
					
					<button type="submit" name="logout" class="menu-btn logout">
						Logout
					</button>
				</div>
			</form>
		</div>
		
		<?php
			if(isset($_POST['add']))
			{
				header('Location: ../forms/step1.php');
				exit;
			}
		
			if(isset($_POST['edit']))
			{
				header('Location: confirmID.php');
				exit;				
			}
			
			if(isset($_POST['delete']))
			{
				header('Location: delete_confirmation.php');
				exit;
			}
			
			if(isset($_POST['logout']))
			{
				session_destroy();
				header('Location: ../login.php');
				exit;
			}
		?>
		
	</body>
</html>