<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['manage'])) {
        header('Location: manage_applications.php');
        exit;
    }
    
    if (isset($_POST['view_all'])) {
        header('Location: view_all_records.php');
        exit;
    }
    
    if (isset($_POST['view_one'])) {
        header('Location: view_one_record.php');
        exit;
    }
    
    if (isset($_POST['add_signature'])) {
        header('Location: add_signature.php');
        exit;
    }
    
    if (isset($_POST['delete_files'])) {
        header('Location: delete_text_files.php');
        exit;
    }
    
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Grade Forgiveness System</title>
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
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .user-info {
            background: #f9f9f9;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 3px;
        }
        
        .user-info strong {
            color: #333;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .menu-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 3px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .menu-card:hover {
            background: #f0f0f0;
            border-color: #333;
        }
        
        .menu-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .menu-card p {
            color: #666;
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #333;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #555;
        }
        
        .btn-logout {
            background: #cc0000;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-logout:hover {
            background: #990000;
        }
        
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #333;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        
        .info-box strong {
            color: #333;
        }
        
        .info-box p {
            color: #555;
            font-size: 14px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <p>Grade Forgiveness Application Management System</p>
        </div>
        
        <div class="user-info">
            <strong>Logged in as:</strong> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Administrator'); ?>
        </div>
        
        <div class="info-box">
            <strong>Quick Guide:</strong>
            <p>Use the options below to manage grade forgiveness applications. You can view, approve, deny, and sign applications.</p>
        </div>
        
        <div class="menu-grid">
            <div class="menu-card">
                <h3>Manage Applications</h3>
                <p>Review, approve, or deny pending applications</p>
                <form method="POST">
                    <button type="submit" name="manage" class="btn">Go to Management</button>
                </form>
            </div>
            
            <div class="menu-card">
                <h3>View All Records</h3>
                <p>See all submitted grade forgiveness records</p>
                <form method="POST">
                    <button type="submit" name="view_all" class="btn">View Records</button>
                </form>
            </div>
            
            <div class="menu-card">
                <h3>View One Record</h3>
                <p>Search for a specific student application</p>
                <form method="POST">
                    <button type="submit" name="view_one" class="btn">Search Record</button>
                </form>
            </div>
            
            <div class="menu-card">
                <h3>Add Signatures</h3>
                <p>Add official signatures to applications</p>
                <form method="POST">
                    <button type="submit" name="add_signature" class="btn">Add Signature</button>
                </form>
            </div>
            
            <div class="menu-card">
                <h3>Delete Files</h3>
                <p>Remove text files from the system</p>
                <form method="POST">
                    <button type="submit" name="delete_files" class="btn">Delete Files</button>
                </form>
            </div>
        </div>
        
        <form method="POST">
            <button type="submit" name="logout" class="btn btn-logout">Logout</button>
        </form>
    </div>
</body>
</html>
