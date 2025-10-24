<?php
require_once '../includes/config.php';

// Check if editing (from GET or POST)
$is_editing = (isset($_GET['edit']) && $_GET['edit'] == 1) || (isset($_POST['edit']) && $_POST['edit'] == 1);
$edit_student_id = $_SESSION['edit_student_id'] ?? '';
$existing_data = array();

// Clear session data if creating a new form (not editing)
if (!$is_editing) {
    $_SESSION['form_data'] = array();
    unset($_SESSION['edit_student_id']);
} else {
    // Initialize session data if not set
    if (!isset($_SESSION['form_data'])) {
        $_SESSION['form_data'] = array();
    }
}

if ($is_editing && !empty($edit_student_id)) {
    $filename = DATA_FILE;
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $in_target_app = false;
        
        for ($i = 0; $i < count($lines); $i++) {
            $trimmed = trim($lines[$i]);
            if (strpos($trimmed, 'Student Id: ' . $edit_student_id) !== false) {
                $in_target_app = true;
            }
            
            if ($in_target_app) {
                $colon_pos = strpos($trimmed, ':');
                if ($colon_pos !== false) {
                    $key = substr($trimmed, 0, $colon_pos);
                    $value = trim(substr($trimmed, $colon_pos + 1));
                    
                    if ($key === 'Student Name') {
                        $existing_data['student_name'] = $value;
                    } elseif ($key === 'Student Id') {
                        $existing_data['student_id'] = $value;
                    } elseif ($key === 'Course Of Study') {
                        $existing_data['course_of_study'] = $value;
                    } elseif ($key === 'Course Code') {
                        $existing_data['course_code'] = $value;
                    } elseif ($key === 'Major') {
                        $existing_data['major'] = $value;
                    } elseif ($key === 'Minor') {
                        $existing_data['minor'] = $value;
                    } elseif ($key === 'Email') {
                        $existing_data['email'] = $value;
                    } elseif ($key === 'Phone') {
                        $existing_data['phone'] = $value;
                    } elseif ($key === 'Academic Year') {
                        $existing_data['academic_year'] = $value;
                    } elseif ($key === 'Semester') {
                        $existing_data['semester'] = $value;
                    } elseif ($key === 'Campus') {
                        $existing_data['campus'] = $value;
                } elseif (strpos($lines[$i], 'MODULE INFORMATION:') !== false) {
                    break;
                }
                }
            }
        }
        
        // Populate form_data with loaded data for editing
        if (!empty($existing_data)) {
            $_SESSION['form_data']['student_info'] = $existing_data;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();
    
    // Validate required fields
    if (empty($_POST['student_name'])) {
        $errors[] = "Student's Name is required";
    }
    if (empty($_POST['student_id'])) {
        $errors[] = "Student ID is required";
    }
    if (empty($_POST['course_of_study'])) {
        $errors[] = "Course of Study is required";
    }
    if (empty($_POST['course_code'])) {
        $errors[] = "Course Code is required";
    }
    if (empty($_POST['email'])) {
        $errors[] = "Email Address is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($_POST['phone'])) {
        $errors[] = "Phone Number is required";
    }
    if (empty($_POST['academic_year'])) {
        $errors[] = "Academic Year is required";
    }
    if (empty($_POST['semester'])) {
        $errors[] = "Semester is required";
    }
    if (empty($_POST['campus'])) {
        $errors[] = "Campus is required";
    }
    
    // If no errors, save data and redirect
    if (empty($errors)) {
        $_SESSION['form_data']['student_info'] = array(
            'student_name' => htmlspecialchars($_POST['student_name']),
            'student_id' => htmlspecialchars($_POST['student_id']),
            'course_of_study' => htmlspecialchars($_POST['course_of_study']),
            'course_code' => htmlspecialchars($_POST['course_code']),
            'major' => htmlspecialchars($_POST['major']),
            'minor' => htmlspecialchars($_POST['minor']),
            'email' => htmlspecialchars($_POST['email']),
            'phone' => htmlspecialchars($_POST['phone']),
            'academic_year' => htmlspecialchars($_POST['academic_year']),
            'semester' => htmlspecialchars($_POST['semester']),
            'campus' => htmlspecialchars($_POST['campus'])
        );
        
        $redirect = 'step2.php';
        if ($is_editing) {
            $redirect .= '?edit=1';
            // Keep edit_student_id in session
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
    <title>Step 1: Student Information</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>APPLICATION FORM FOR GRADE FORGIVENESS</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Step 1 of 3: Student Information <?php echo $is_editing ? '(Editing)' : ''; ?></p>
        
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
        
        <form method="POST" action="step1.php">
            <div class="form-section">
                <h2>STUDENT INFORMATION</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="student_name">Student's Name:</label>
                        <input type="text" id="student_name" name="student_name" 
                               value="<?php echo isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : (isset($existing_data['student_name']) ? htmlspecialchars($existing_data['student_name']) : ''); ?>" 
                               placeholder="Last Name, First Name, Middle Initial" required>
                    </div>
                    <div class="form-group">
                        <label for="student_id">ID #:</label>
                        <input type="text" id="student_id" name="student_id" 
                               value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : (isset($existing_data['student_id']) ? htmlspecialchars($existing_data['student_id']) : ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="course_of_study">Course of Study:</label>
                        <input type="text" id="course_of_study" name="course_of_study" 
                               value="<?php echo isset($_POST['course_of_study']) ? htmlspecialchars($_POST['course_of_study']) : (isset($existing_data['course_of_study']) ? htmlspecialchars($existing_data['course_of_study']) : ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="course_code">Course Code:</label>
                        <input type="text" id="course_code" name="course_code" 
                               value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : (isset($existing_data['course_code']) ? htmlspecialchars($existing_data['course_code']) : ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="major">Major:</label>
                        <input type="text" id="major" name="major" 
                               value="<?php echo isset($_POST['major']) ? htmlspecialchars($_POST['major']) : (isset($existing_data['major']) ? htmlspecialchars($existing_data['major']) : ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="minor">Minor:</label>
                        <input type="text" id="minor" name="minor" 
                               value="<?php echo isset($_POST['minor']) ? htmlspecialchars($_POST['minor']) : (isset($existing_data['minor']) ? htmlspecialchars($existing_data['minor']) : ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($existing_data['email']) ? htmlspecialchars($existing_data['email']) : ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone #:</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : (isset($existing_data['phone']) ? htmlspecialchars($existing_data['phone']) : ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="academic_year">Academic Year:</label>
                        <select id="academic_year" name="academic_year" required>
                            <option value="">Select Year</option>
                            <option value="2023-2024" <?php $val = isset($_POST['academic_year']) ? $_POST['academic_year'] : (isset($existing_data['academic_year']) ? $existing_data['academic_year'] : ''); echo ($val == '2023-2024') ? 'selected' : ''; ?>>2023-2024</option>
                            <option value="2024-2025" <?php echo ($val == '2024-2025') ? 'selected' : ''; ?>>2024-2025</option>
                            <option value="2025-2026" <?php echo ($val == '2025-2026') ? 'selected' : ''; ?>>2025-2026</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester:</label>
                        <select id="semester" name="semester" required>
                            <option value="">Select Semester</option>
                            <option value="Fall" <?php $sem = isset($_POST['semester']) ? $_POST['semester'] : (isset($existing_data['semester']) ? $existing_data['semester'] : ''); echo ($sem == 'Fall') ? 'selected' : ''; ?>>Fall</option>
                            <option value="Spring" <?php echo ($sem == 'Spring') ? 'selected' : ''; ?>>Spring</option>
                            <option value="Summer" <?php echo ($sem == 'Summer') ? 'selected' : ''; ?>>Summer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="campus">Campus:</label>
                        <select id="campus" name="campus" required>
                            <option value="">Select Campus</option>
                            <option value="Main Campus" <?php $camp = isset($_POST['campus']) ? $_POST['campus'] : (isset($existing_data['campus']) ? $existing_data['campus'] : ''); echo ($camp == 'Main Campus') ? 'selected' : ''; ?>>Main Campus</option>
                            <option value="Downtown Campus" <?php echo ($camp == 'Downtown Campus') ? 'selected' : ''; ?>>Downtown Campus</option>
                            <option value="Online" <?php echo ($camp == 'Online') ? 'selected' : ''; ?>>Online</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="btn-container">
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
                <button type="submit" class="btn btn-primary">Next: Module Information</button>
                <?php if ($is_editing): ?>
                    <input type="hidden" name="edit" value="1">
                <?php endif; ?>
            </div>
        </form>
    </div>
    <script src="../assets/script.js"></script>
</body>
</html>
