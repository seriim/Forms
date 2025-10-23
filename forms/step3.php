<?php
require_once '../includes/config.php';

// Check if user came from step 2
if (!isset($_SESSION['form_data']['modules'])) {
    header('Location: step2.php');
    exit();
}

// Check if editing and load existing signatures
$is_editing = isset($_GET['edit']) && $_GET['edit'] == 1;
$existing_signature = '';
$existing_date = '';

if ($is_editing && !empty($_SESSION['edit_student_id'])) {
    $filename = DATA_FILE;
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $in_target_app = false;
        $in_signatures = false;
        
        for ($i = 0; $i < count($lines); $i++) {
            $trimmed = trim($lines[$i]);
            if (strpos($trimmed, 'Student Id: ' . $_SESSION['edit_student_id']) !== false) {
                $in_target_app = true;
            }
            
            if ($in_target_app) {
                if (strpos($lines[$i], 'SIGNATURES & APPROVAL:') !== false) {
                    $in_signatures = true;
                    continue;
                }
                
                if ($in_signatures) {
                    if (strpos($lines[$i], 'END OF APPLICATION') !== false) {
                        break;
                    }
                    
                    $trimmed_line = trim($lines[$i]);
                    
                    if (strpos($trimmed_line, 'Student Signature:') === 0) {
                        $existing_signature = trim(substr($trimmed_line, 18));
                    } elseif (strpos($trimmed_line, 'Student Date:') === 0) {
                        $existing_date = trim(substr($trimmed_line, 13));
                    }
                }
            }
        }
    }
    
    // Populate form_data with loaded signatures for editing
    if (!empty($existing_signature) || !empty($existing_date)) {
        $_SESSION['form_data']['signatures'] = array(
            'student_signature' => $existing_signature,
            'student_date' => $existing_date
        );
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();
    
    // Validate required fields
    if (empty($_POST['student_signature'])) {
        $errors[] = "Student's Name & Signature is required";
    }
    if (empty($_POST['student_date'])) {
        $errors[] = "Student's Date is required";
    }
    
    // If no errors, save data and redirect to report
    if (empty($errors)) {
        $_SESSION['form_data']['signatures'] = array(
            'student_signature' => htmlspecialchars($_POST['student_signature']),
            'student_date' => htmlspecialchars($_POST['student_date'])
        );
        $redirect = 'report.php';
        if ($is_editing) {
            $redirect .= '?edit=1';
        }
        header('Location: ' . $redirect);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 3: Signatures & Approval</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>APPLICATION FORM FOR GRADE FORGIVENESS</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Step 3 of 3: Signatures & Approval <?php echo $is_editing ? '(Editing)' : ''; ?></p>
        
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <strong>Please correct the following errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="note">
            <strong>Note:</strong> Please provide your signature and date. Other signatures (advisor, program director, administrator) will be completed by staff during the review process.
        </div>
        
        <form method="POST" action="">
            <div class="form-section">
                <h2>SIGNATURES & APPROVAL</h2>
                
                <div class="signature-row">
                    <div class="signature-group">
                        <label for="student_signature">Student's Name & Signature:</label>
                        <input type="text" id="student_signature" name="student_signature" 
                               value="<?php echo isset($_POST['student_signature']) ? htmlspecialchars($_POST['student_signature']) : (isset($existing_signature) && !empty($existing_signature) ? htmlspecialchars($existing_signature) : ''); ?>" 
                               placeholder="Enter full name and signature" required>
                    </div>
                    <div class="date-group">
                        <label for="student_date">Date:</label>
                        <input type="date" id="student_date" name="student_date" 
                               value="<?php echo isset($_POST['student_date']) ? htmlspecialchars($_POST['student_date']) : (isset($existing_date) && !empty($existing_date) ? htmlspecialchars($existing_date) : ''); ?>" required>
                    </div>
                </div>
                
            </div>
            
            <div class="btn-container">
                <a href="step2.php<?php echo $is_editing ? '?edit=1' : ''; ?>" class="btn btn-secondary">Back: Module Information</a>
                <button type="submit" class="btn btn-primary">Submit Application</button>
                <?php if ($is_editing): ?>
                    <input type="hidden" name="edit" value="1">
                <?php endif; ?>
            </div>
        </form>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
