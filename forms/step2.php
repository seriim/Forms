<?php
require_once '../includes/config.php';

// Check if editing mode (from GET or POST)
$is_editing = (isset($_GET['edit']) && $_GET['edit'] == 1) || (isset($_POST['edit']) && $_POST['edit'] == 1);

// Check if user came from step 1 (allow if editing)
if (!isset($_SESSION['form_data']['student_info']) && !$is_editing) {
    header('Location: step1.php');
    exit();
}

// Load student info from file if editing and session is empty
if ($is_editing && !empty($_SESSION['edit_student_id']) && empty($_SESSION['form_data']['student_info'])) {
    $filename = DATA_FILE;
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $in_target_app = false;
        $student_info = array();
        
        for ($i = 0; $i < count($lines); $i++) {
            $trimmed = trim($lines[$i]);
            if (strpos($trimmed, 'Student Id: ' . $_SESSION['edit_student_id']) !== false) {
                $in_target_app = true;
            }
            
            if ($in_target_app) {
                if (strpos($trimmed, 'Student Name:') === 0) {
                    $student_info['student_name'] = trim(substr($trimmed, 13));
                } elseif (strpos($trimmed, 'Student Id:') === 0) {
                    $student_info['student_id'] = trim(substr($trimmed, 11));
                } elseif (strpos($trimmed, 'Course Of Study:') === 0) {
                    $student_info['course_of_study'] = trim(substr($trimmed, 16));
                } elseif (strpos($trimmed, 'Course Code:') === 0) {
                    $student_info['course_code'] = trim(substr($trimmed, 12));
                } elseif (strpos($trimmed, 'Major:') === 0) {
                    $student_info['major'] = trim(substr($trimmed, 6));
                } elseif (strpos($trimmed, 'Minor:') === 0) {
                    $student_info['minor'] = trim(substr($trimmed, 6));
                } elseif (strpos($trimmed, 'Email:') === 0) {
                    $student_info['email'] = trim(substr($trimmed, 6));
                } elseif (strpos($trimmed, 'Phone:') === 0) {
                    $student_info['phone'] = trim(substr($trimmed, 6));
                } elseif (strpos($trimmed, 'Academic Year:') === 0) {
                    $student_info['academic_year'] = trim(substr($trimmed, 14));
                } elseif (strpos($trimmed, 'Semester:') === 0) {
                    $student_info['semester'] = trim(substr($trimmed, 9));
                } elseif (strpos($trimmed, 'Campus:') === 0) {
                    $student_info['campus'] = trim(substr($trimmed, 7));
                } elseif (strpos($lines[$i], 'MODULE INFORMATION:') !== false) {
                    break;
                }
            }
        }
        
        if (!empty($student_info)) {
            $_SESSION['form_data']['student_info'] = $student_info;
        }
    }
}

// Get current semester from step 1 (or from loaded data if editing)
$current_academic_year = $_SESSION['form_data']['student_info']['academic_year'] ?? '';
$current_semester = $_SESSION['form_data']['student_info']['semester'] ?? '';

// Load existing modules if editing
$existing_modules = array();

if ($is_editing && !empty($_SESSION['edit_student_id'])) {
    $filename = DATA_FILE;
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $in_target_app = false;
        $in_modules = false;
        $current_module = array();
        
        for ($i = 0; $i < count($lines); $i++) {
            $trimmed = trim($lines[$i]);
            if (strpos($trimmed, 'Student Id: ' . $_SESSION['edit_student_id']) !== false) {
                $in_target_app = true;
            }
            
            if ($in_target_app) {
                if (strpos($lines[$i], 'MODULE INFORMATION:') !== false) {
                    $in_modules = true;
                    continue;
                }
                
                if ($in_modules) {
                    if (strpos($lines[$i], 'SIGNATURES & APPROVAL:') !== false) {
                        if (!empty($current_module)) {
                            $existing_modules[] = $current_module;
                        }
                        break;
                    }
                    
                    $trimmed_line = trim($lines[$i]);
                    $colon_pos = strpos($trimmed_line, ':');
                    
                    if ($colon_pos !== false) {
                        $key = substr($trimmed_line, 0, $colon_pos);
                        $value = trim(substr($trimmed_line, $colon_pos + 1));
                        
                        if ($key === 'Module Name') {
                            if (!empty($current_module)) {
                                $existing_modules[] = $current_module;
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
                    
                    // If we hit an empty line after having module data, save it
                    if (empty($trimmed_line) && !empty($current_module)) {
                        $existing_modules[] = $current_module;
                        $current_module = array();
                    }
                }
            }
        }
        
        // Make sure to add the last module if it exists
        if (!empty($current_module)) {
            $existing_modules[] = $current_module;
        }
    }
    
    // Populate form_data with loaded modules for editing
    if (!empty($existing_modules)) {
        $_SESSION['form_data']['modules'] = $existing_modules;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();
    $modules = array();
    
    // Process module data
    for ($i = 0; $i < 4; $i++) {
        // Check if this module has any data entered
        $has_data = !empty($_POST['module_name'][$i]) || 
                   !empty($_POST['module_code'][$i]) || 
                   !empty($_POST['academic_year'][$i]) || 
                   !empty($_POST['academic_session'][$i]) || 
                   !empty($_POST['initial_grade'][$i]);
        
        if ($has_data) {
            $module_errors = array();
            
            // Validate required fields for this module
            if (empty($_POST['module_name'][$i])) {
                $module_errors[] = "Module Name is required for module " . ($i + 1);
            }
            if (empty($_POST['module_code'][$i])) {
                $module_errors[] = "Module Code is required for module " . ($i + 1);
            }
            if (empty($_POST['academic_year'][$i])) {
                $module_errors[] = "Academic Year is required for module " . ($i + 1);
            }
            if (empty($_POST['academic_session'][$i])) {
                $module_errors[] = "Academic Session is required for module " . ($i + 1);
            }
            if (empty($_POST['initial_grade'][$i])) {
                $module_errors[] = "Initial Grade is required for module " . ($i + 1);
            }
            
            // Add module errors to main errors array
            $errors = array_merge($errors, $module_errors);
            
            // If no errors for this module, add to array
            if (empty($module_errors)) {
                $modules[] = array(
                    'module_name' => htmlspecialchars($_POST['module_name'][$i]),
                    'module_code' => htmlspecialchars($_POST['module_code'][$i]),
                    'academic_year' => htmlspecialchars($_POST['academic_year'][$i]),
                    'academic_session' => htmlspecialchars($_POST['academic_session'][$i]),
                    'initial_grade' => htmlspecialchars($_POST['initial_grade'][$i])
                );
            }
        }
    }
    
    // Check if at least one module is entered
    if (empty($modules)) {
        $errors[] = "At least one module must be entered";
    }
    
    // Validate that no module is from current semester
    if (!empty($current_academic_year) && !empty($current_semester)) {
        foreach ($modules as $module) {
            if ($module['academic_year'] === $current_academic_year && $module['academic_session'] === $current_semester) {
                $errors[] = "You cannot apply for grade forgiveness for modules in your current semester (" . htmlspecialchars($current_semester) . " " . htmlspecialchars($current_academic_year) . ")";
                break;
            }
        }
    }
    
    // If no errors, save data and redirect
    if (empty($errors)) {
        $_SESSION['form_data']['modules'] = $modules;
        $redirect = 'step3.php';
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
    <title>Step 2: Module Information</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>APPLICATION FORM FOR GRADE FORGIVENESS</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Step 2 of 3: Module Information <?php echo $is_editing ? '(Editing)' : ''; ?></p>
        
        <?php if (!empty($current_academic_year) && !empty($current_semester)): ?>
            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 3px; margin-bottom: 20px; color: #856404;">
                <strong>Note:</strong> You cannot apply for grade forgiveness for modules taken in your current semester (<strong><?php echo htmlspecialchars($current_semester) . ' ' . htmlspecialchars($current_academic_year); ?></strong>). Please select modules from previous semesters only.
            </div>
        <?php endif; ?>
        
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
            <strong>Note:</strong> Please fill in the details for the modules you wish to apply for grade forgiveness. You must fill at least one module, and can fill up to 4 modules total. Decision and comments will be filled by administrators during the review process.
        </div>
        
        <form method="POST" action="">
            <div class="form-section">
                <h2>MODULE INFORMATION</h2>
                
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
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <tr>
                            <td>
                                <input type="text" name="module_name[]" 
                                       value="<?php echo isset($_POST['module_name'][$i]) ? htmlspecialchars($_POST['module_name'][$i]) : (isset($existing_modules[$i]['module_name']) ? htmlspecialchars($existing_modules[$i]['module_name']) : (isset($_SESSION['form_data']['modules'][$i]['module_name']) ? htmlspecialchars($_SESSION['form_data']['modules'][$i]['module_name']) : '')); ?>"
                                       placeholder="Enter module name">
                            </td>
                            <td>
                                <input type="text" name="module_code[]" 
                                       value="<?php echo isset($_POST['module_code'][$i]) ? htmlspecialchars($_POST['module_code'][$i]) : (isset($existing_modules[$i]['module_code']) ? htmlspecialchars($existing_modules[$i]['module_code']) : (isset($_SESSION['form_data']['modules'][$i]['module_code']) ? htmlspecialchars($_SESSION['form_data']['modules'][$i]['module_code']) : '')); ?>"
                                       placeholder="e.g., CS101">
                            </td>
                            <td>
                                <select name="academic_year[]">
                                    <option value="">Select Year</option>
                                    <option value="2022-2023" <?php $yr = isset($_POST['academic_year'][$i]) ? $_POST['academic_year'][$i] : (isset($existing_modules[$i]['academic_year']) ? $existing_modules[$i]['academic_year'] : (isset($_SESSION['form_data']['modules'][$i]['academic_year']) ? $_SESSION['form_data']['modules'][$i]['academic_year'] : '')); echo ($yr == '2022-2023') ? 'selected' : ''; ?>>2022-2023</option>
                                    <option value="2023-2024" <?php echo ($yr == '2023-2024') ? 'selected' : ''; ?>>2023-2024</option>
                                    <option value="2024-2025" <?php echo ($yr == '2024-2025') ? 'selected' : ''; ?>>2024-2025</option>
                                </select>
                            </td>
                            <td>
                                <select name="academic_session[]">
                                    <option value="">Select Session</option>
                                    <option value="Fall" <?php $sess = isset($_POST['academic_session'][$i]) ? $_POST['academic_session'][$i] : (isset($existing_modules[$i]['academic_session']) ? $existing_modules[$i]['academic_session'] : (isset($_SESSION['form_data']['modules'][$i]['academic_session']) ? $_SESSION['form_data']['modules'][$i]['academic_session'] : '')); echo ($sess == 'Fall') ? 'selected' : ''; ?>>Fall</option>
                                    <option value="Spring" <?php echo ($sess == 'Spring') ? 'selected' : ''; ?>>Spring</option>
                                    <option value="Summer" <?php echo ($sess == 'Summer') ? 'selected' : ''; ?>>Summer</option>
                                </select>
                            </td>
                            <td>
                                <select name="initial_grade[]">
                                    <option value="">Select Grade</option>
                                    <option value="F" <?php $gr = isset($_POST['initial_grade'][$i]) ? $_POST['initial_grade'][$i] : (isset($existing_modules[$i]['initial_grade']) ? $existing_modules[$i]['initial_grade'] : (isset($_SESSION['form_data']['modules'][$i]['initial_grade']) ? $_SESSION['form_data']['modules'][$i]['initial_grade'] : '')); echo ($gr == 'F') ? 'selected' : ''; ?>>F</option>
                                    <option value="D" <?php echo ($gr == 'D') ? 'selected' : ''; ?>>D</option>
                                    <option value="D+" <?php echo ($gr == 'D+') ? 'selected' : ''; ?>>D+</option>
                                    <option value="C-" <?php echo ($gr == 'C-') ? 'selected' : ''; ?>>C-</option>
                                </select>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="btn-container">
                <a href="step1.php<?php echo $is_editing ? '?edit=1' : ''; ?>" class="btn btn-secondary">Back: Student Information</a>
                <button type="submit" class="btn btn-primary">Next: Signatures & Approval</button>
                <?php if ($is_editing): ?>
                    <input type="hidden" name="edit" value="1">
                <?php endif; ?>
            </div>
        </form>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
