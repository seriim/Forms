<?php
// Grade Forgiveness Application System - Configuration File

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    
    // Session configuration
    session_set_cookie_params([
        'lifetime' => 3600, // 1 hour
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true for HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    session_start();
}

// Application settings
define('DATA_FILE', dirname(__DIR__) . '/grade_forgiveness_records.txt');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('America/Jamaica');

// Helper function to get next record number for a student
function get_next_record_number($student_id) {
    $filename = DATA_FILE;
    if (!file_exists($filename)) {
        return 1;
    }
    
    $content = file_get_contents($filename);
    $pattern = '/Student Id: ' . preg_quote($student_id, '/') . '/';
    preg_match_all($pattern, $content, $matches);
    
    return count($matches[0]) + 1;
}

// Helper function to generate primary key
function generate_primary_key($student_id) {
    $record_number = get_next_record_number($student_id);
    return $student_id . '_' . $record_number;
}

// File operations using fopen and fwrite
function save_application_data($data) {
    $filename = DATA_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $student_id = $data['student_info']['student_id'];
    $primary_key = generate_primary_key($student_id);
    
    // Try to create the file if it doesn't exist
    if (!file_exists($filename)) {
        touch($filename);
        chmod($filename, 0777);
    }
    
    // Open file for writing (append mode)
    $file = fopen($filename, 'a');
    
    if ($file === false) {
        return false;
    }
    
    // Write application header
    fwrite($file, "=== GRADE FORGIVENESS APPLICATION ===\n");
    fwrite($file, "Primary Key: " . $primary_key . "\n");
    fwrite($file, "Timestamp: " . $timestamp . "\n");
    fwrite($file, "Student Id: " . $student_id . "\n");
    fwrite($file, "Status: Pending\n");
    fwrite($file, "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n");
    fwrite($file, "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n\n");
    
    // Write Student Information
    fwrite($file, "STUDENT INFORMATION:\n");
    fwrite($file, "===================\n");
    foreach ($data['student_info'] as $key => $value) {
        $formatted_key = ucwords(str_replace('_', ' ', $key));
        fwrite($file, $formatted_key . ": " . $value . "\n");
    }
    fwrite($file, "\n");
    
    // Write Module Information
    fwrite($file, "MODULE INFORMATION:\n");
    fwrite($file, "==================\n");
    foreach ($data['modules'] as $index => $module) {
        fwrite($file, "Module " . ($index + 1) . ":\n");
        fwrite($file, "--------\n");
        foreach ($module as $key => $value) {
            $formatted_key = ucwords(str_replace('_', ' ', $key));
            fwrite($file, "  " . $formatted_key . ": " . $value . "\n");
        }
        fwrite($file, "\n");
    }
    
    // Write Signatures and Approval
    fwrite($file, "SIGNATURES & APPROVAL:\n");
    fwrite($file, "=====================\n");
    foreach ($data['signatures'] as $key => $value) {
        $formatted_key = ucwords(str_replace('_', ' ', $key));
        fwrite($file, $formatted_key . ": " . $value . "\n");
    }
    fwrite($file, "\n");
    
    // Write footer
    fwrite($file, str_repeat("=", 60) . "\n");
    fwrite($file, "END OF APPLICATION\n");
    fwrite($file, str_repeat("=", 60) . "\n\n");
    
    // Close file
    fclose($file);
    
    return true;
}


// Include this file in all PHP pages
?>
