<?php
require_once '../includes/config.php';

// Check if editing mode (from GET or POST)
$is_editing = (isset($_GET['edit']) && $_GET['edit'] == 1) || (isset($_POST['edit']) && $_POST['edit'] == 1);

// Check if user came from step 2 (allow if editing)
if (!isset($_SESSION['form_data']['modules']) && !$is_editing) {
    header('Location: step2.php');
    exit();
}

// Load all missing data from file if editing
if ($is_editing && !empty($_SESSION['edit_student_id'])) {
    $filename = DATA_FILE;
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $in_target_app = false;
        $in_student = false;
        $in_modules = false;
        $in_signatures = false;
        $student_info = array();
        $modules = array();
        $current_module = array();
        $signatures = array();
        
        for ($i = 0; $i < count($lines); $i++) {
            $trimmed = trim($lines[$i]);
            
            if (strpos($trimmed, 'Student Id: ' . $_SESSION['edit_student_id']) !== false) {
                $in_target_app = true;
            }
            
            if ($in_target_app) {
                if (strpos($lines[$i], 'STUDENT INFORMATION:') !== false) {
                    $in_student = true;
                    $in_modules = false;
                    $in_signatures = false;
                    continue;
                } elseif (strpos($lines[$i], 'MODULE INFORMATION:') !== false) {
                    $in_student = false;
                    $in_modules = true;
                    $in_signatures = false;
                    continue;
                } elseif (strpos($lines[$i], 'SIGNATURES & APPROVAL:') !== false) {
                    if (!empty($current_module)) {
                        $modules[] = $current_module;
                    }
                    $in_student = false;
                    $in_modules = false;
                    $in_signatures = true;
                    continue;
                } elseif (strpos($lines[$i], 'END OF APPLICATION') !== false) {
                    break;
                }
                
                if ($in_student) {
                    $colon_pos = strpos($trimmed, ':');
                    if ($colon_pos !== false) {
                        $key = substr($trimmed, 0, $colon_pos);
                        $value = trim(substr($trimmed, $colon_pos + 1));
                        
                        if ($key === 'Student Name') {
                            $student_info['student_name'] = $value;
                        } elseif ($key === 'Student Id') {
                            $student_info['student_id'] = $value;
                        } elseif ($key === 'Course Of Study') {
                            $student_info['course_of_study'] = $value;
                        } elseif ($key === 'Course Code') {
                            $student_info['course_code'] = $value;
                        } elseif ($key === 'Major') {
                            $student_info['major'] = $value;
                        } elseif ($key === 'Minor') {
                            $student_info['minor'] = $value;
                        } elseif ($key === 'Email') {
                            $student_info['email'] = $value;
                        } elseif ($key === 'Phone') {
                            $student_info['phone'] = $value;
                        } elseif ($key === 'Academic Year') {
                            $student_info['academic_year'] = $value;
                        } elseif ($key === 'Semester') {
                            $student_info['semester'] = $value;
                        } elseif ($key === 'Campus') {
                            $student_info['campus'] = $value;
                        }
                    }
                } elseif ($in_modules) {
                    $colon_pos = strpos($trimmed, ':');
                    if ($colon_pos !== false) {
                        $key = substr($trimmed, 0, $colon_pos);
                        $value = trim(substr($trimmed, $colon_pos + 1));
                        
                        if ($key === 'Module Name') {
                            if (!empty($current_module)) {
                                $modules[] = $current_module;
                            }
                            $current_module = array('module_name' => $value);
                        } elseif ($key === 'Module Code') {
                            $current_module['module_code'] = $value;
                        } elseif ($key === 'Academic Year') {
                            $current_module['academic_year'] = $value;
                        } elseif ($key === 'Academic Session') {
                            $current_module['academic_session'] = $value;
                        } elseif ($key === 'Initial Grade') {
                            $current_module['initial_grade'] = $value;
                        }
                    }
                } elseif ($in_signatures) {
                    $colon_pos = strpos($trimmed, ':');
                    if ($colon_pos !== false) {
                        $key = substr($trimmed, 0, $colon_pos);
                        $value = trim(substr($trimmed, $colon_pos + 1));
                        
                        if ($key === 'Student Signature') {
                            $signatures['student_signature'] = $value;
                        } elseif ($key === 'Student Date') {
                            $signatures['student_date'] = $value;
                        }
                    }
                }
            }
        }
        
        // Make sure to add the last module if it exists
        if (!empty($current_module)) {
            $modules[] = $current_module;
        }
        
        // Populate session with all loaded data
        if (!empty($student_info)) {
            $_SESSION['form_data']['student_info'] = $student_info;
        }
        if (!empty($modules)) {
            $_SESSION['form_data']['modules'] = $modules;
        }
        if (!empty($signatures)) {
            $_SESSION['form_data']['signatures'] = $signatures;
        }
    }
}

// Load existing signatures if editing
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
                               value="<?php echo isset($_POST['student_signature']) ? htmlspecialchars($_POST['student_signature']) : (isset($existing_signature) && !empty($existing_signature) ? htmlspecialchars($existing_signature) : (isset($_SESSION['form_data']['signatures']['student_signature']) ? htmlspecialchars($_SESSION['form_data']['signatures']['student_signature']) : '')); ?>" 
                               placeholder="Enter full name and signature" required>
                    </div>
                    <div class="date-group">
                        <label for="student_date">Date:</label>
                        <input type="date" id="student_date" name="student_date" 
                               value="<?php echo isset($_POST['student_date']) ? htmlspecialchars($_POST['student_date']) : (isset($existing_date) && !empty($existing_date) ? htmlspecialchars($existing_date) : (isset($_SESSION['form_data']['signatures']['student_date']) ? htmlspecialchars($_SESSION['form_data']['signatures']['student_date']) : '')); ?>" required>
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
