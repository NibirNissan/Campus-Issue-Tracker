# Campus Issue Tracker

A web-based complaint management system for university students and administrators. Students can submit complaints about campus issues (lab, classroom, hostel, library, etc.) and track their resolution. Admins can manage, update, and resolve complaints efficiently.

## Technology Stack

| Layer              | Technology              |
|--------------------|-------------------------|
| Frontend           | HTML, CSS, Bootstrap 5  |
| Backend            | PHP (Core/Pure)         |
| Database           | MySQL (via XAMPP)       |
| Server Environment | XAMPP (Apache + MySQL)  |

## Features

### Student
- Register and login
- Submit complaints with title, department, description, and optional image
- View all submitted complaints with filters (status, department)
- View complaint details and timeline/history

### Admin
- Login (default: `admin@campus.com` / `admin123`)
- Dashboard with statistics (total, pending, in progress, done, by department)
- View all complaints with search and filter options
- Update complaint status (Pending → In Progress → Done) with remarks
- Full complaint timeline/history for transparency

## Setup Instructions (XAMPP)

### 1. Install XAMPP
Download and install [XAMPP](https://www.apachefriends.org/) for your operating system.

### 2. Clone/Copy Project
Copy the project folder to your XAMPP's `htdocs` directory:
```
C:\xampp\htdocs\campus-issue-tracker\
```

### 3. Create Database
1. Start Apache and MySQL from XAMPP Control Panel
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import the SQL file: `database/campus_issue_tracker.sql`
   - Click "Import" tab → Choose file → Select `campus_issue_tracker.sql` → Click "Go"

### 4. Configure Database (if needed)
Edit `config/database.php` if your MySQL credentials differ from defaults:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'campus_issue_tracker');
```

### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/campus-issue-tracker/
```

### 6. Default Admin Login
- **Email:** admin@campus.com
- **Password:** admin123

## Project Structure

```
campus-issue-tracker/
├── config/
│   ├── database.php          # Database connection
│   └── session.php           # Session management & helpers
├── css/
│   └── style.css             # Custom styles
├── database/
│   └── campus_issue_tracker.sql  # Database schema
├── includes/
│   ├── header.php            # HTML head & header
│   ├── footer.php            # Footer & scripts
│   └── navbar.php            # Navigation bar
├── pages/
│   ├── admin/
│   │   ├── complaints.php    # All complaints (search/filter)
│   │   ├── dashboard.php     # Admin dashboard
│   │   ├── update_status.php # Update complaint status
│   │   └── view_complaint.php # View complaint detail
│   └── student/
│       ├── dashboard.php     # Student dashboard
│       ├── my_complaints.php # Student's complaints list
│       ├── submit_complaint.php # Submit new complaint
│       └── view_complaint.php # View complaint detail
├── uploads/                  # Uploaded images
├── .htaccess                 # Apache configuration
├── index.php                 # Landing page
├── login.php                 # Login page
├── logout.php                # Logout handler
├── register.php              # Registration page
└── README.md
```

## Database Schema

### Users Table
Stores student and admin accounts with hashed passwords.

### Complaints Table
Stores all complaints with title, department, description, optional image, and status.

### Complaint Timeline Table
Tracks every status change with timestamps, user who made the change, and optional remarks.
