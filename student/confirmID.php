<?php
session_start();
require_once '../includes/config.php';
?>
<html>
	<head>
	<title>Confirm ID</title>
	
		<style>
			body {
				font-family: Arial, sans-serif;
				background: #f5f5f5;
				display: flex;
				justify-content: center;
				align-items: center;
				min-height: 100vh;
				padding: 20px;
			}
			
			form {
				background: white;
				padding: 30px;
				width: 100%;
				max-width: 400px;
			}
			
			p {
				font-size: 16px;
				font-weight: bold;
				margin-bottom: 20px;
				color: #333;
			}
			
			input[type="text"],
			input[type="email"],
			input[type="password"] {
				padding: 10px;
				margin: 8px 0;
				border-radius: 3px;
				width: 100%;
				border: 1px solid #ccc;
				font-size: 14px;
			}
			
			input[type="submit"] {
				width: 100%;
				padding: 10px;
				margin-top: 10px;
				border-radius: 3px;
				background-color: #333;
				color: white;
				font-weight: bold;
				cursor: pointer;
				border: none;
			}
			
			input[type="submit"]:hover {
				background-color: #555;
			}
		</style>
	</head>

	<body>
		<form method="POST" action="">
			<p>Please enter your ID number:</p>
			<input type="text" name="id" placeholder="Enter your ID" required>
			<input type="submit" name="stu_id" value="Submit">
			<input type="submit" name="cancel" value="Cancel">
		</form>
		
		<?php
			// Check if user is logged in
			if(!isset($_SESSION['role']))
			{
				header('Location: ../login.php');
				exit;
			}
			
			if(isset($_POST['stu_id']))
			{
				$found = 0;
				$student_id = trim($_POST['id']);
				
				// Check if data file exists
				if(file_exists("../grade_forgiveness_records.txt"))
				{
					$record = file("../grade_forgiveness_records.txt");
					
					// Search for the student ID in the file
					foreach($record as $student_record)
					{
						if (strpos($student_record, 'Student Id: ' . $student_id) !== false || strpos($student_record, 'Student ID: ' . $student_id) !== false)
						{
							$found = 1;
							// Store student ID in session for editing
							$_SESSION['edit_student_id'] = $student_id;
							header('Location: ../forms/step1.php?edit=1');
							exit;
						}
					}
				}
				
				if($found == 0)
				{
					echo '<p style="color: #cc0000; margin-top: 20px;">Student ID not found. Please re-enter your ID or first apply for a Grade Forgiveness.</p>';
				}	
			}
			
			if(isset($_POST['cancel']))
			{
				header('Location: studentAccess.php');
				exit;
			}
		?>
	</body>

</html>