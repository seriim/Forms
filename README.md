# Grade Forgiveness Application System

## Project Overview
This is a multi-page web application that allows students to apply for grade forgiveness through a structured form process. The system includes student access for submitting applications and admin access for reviewing and managing submissions.

## Group Members / Authors
- **Serena Morris** 
- **Jahzeal Simms** 
- **Joshane Beecher** 
- **Abigayle Higgins** 

## Features

### Student Access
- **Add New Grade Forgiveness Document** - Students can submit new applications with a unique primary key (StudentID_RecordNumber)
- **Edit Grade Forgiveness Document** - Students can modify their existing applications
- **Delete Grade Forgiveness** - Students can delete their applications with ID confirmation

### Admin Access
- **View All Records** - Administrators can view all submitted grade forgiveness records
- **View One Record** - Administrators can search and view specific student records
- **Add Signatures** - Administrators can add signatures from PD, HOS, Academic Advisor, and Faculty Admin with dates
- **Delete Text Files** - System administrators can delete text files (restricted to admin only)

## System Requirements

### Login Credentials
**Student Access:**
- Username: `student`
- Password: `240825`

**Admin Access:**
- Username: `admin`
- Password: `240825`

## Technical Stack
- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Data Storage:** Text file (grade_forgiveness_records.txt)
- **Session Management:** PHP Sessions with security cookies
- **Validation:** Server-side validation with double scripting protection



## Application Workflow

### Student Workflow
1. Login with student credentials
2. Select "Add New Grade Forgiveness Document"
3. Complete Step 1: Student Information
4. Complete Step 2: Module Information (up to 4 modules)
5. Complete Step 3: Signatures & Approval
6. Submit application and receive confirmation

### Admin Workflow
1. Login with admin credentials
2. View all records or search for specific records
3. Add signatures from various staff members
4. Manage and delete records as needed

## Security Features
- Session-based authentication
- HTTPOnly cookies for session security
- Input sanitization with htmlspecialchars()
- Server-side form validation
- CSRF protection with session tokens
- Secure password handling


## Installation & Setup

1. Place all files in the web server root directory
2. Ensure write permissions on the directory for creating `grade_forgiveness_records.txt`
3. Access the application at `http://localhost/MultipageGradeForgivenessForm/`
4. Login with provided credentials


