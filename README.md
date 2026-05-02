# Campus Issue Tracker

This project is built using **PHP (Core)**, **MySQL**, and **Bootstrap 5**, designed for a local **XAMPP** server environment.

## Project Structure
- `index.php`: Login page and entry point.
- `register.php`: Student registration.
- `dashboard.php`: Student dashboard to view/manage their complaints.
- `submit_complaint.php`: Form for students to report issues.
- `admin_dashboard.php`: Admin panel to update complaint status.
- `view_complaint.php`: Detailed view and timeline for a specific issue.
- `includes/`: Contains reusable database connection, header, and footer.
- `database.sql`: MySQL schema for creating tables.

## How to setup in XAMPP:
1. Start XAMPP and turn on **Apache** and **MySQL**.
2. Go to **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Create a new database named `campus_issue_tracker`.
4. Import the provided `database.sql` file.
5. Copy all project files into your XAMPP `htdocs` folder (e.g., `C:\xampp\htdocs\campus-tracker\`).
6. Access the app in your browser at `http://localhost/campus-tracker/`.

## Default Credentials:
- **Admin**: `admin@campus.edu` / `admin123`
- **Student**: Create a new account via `register.php`.

## Note on AI Studio Preview:
This platform uses a Node.js/TypeScript environment for live previews. While I have generated the complete PHP source code as requested, the interactive preview window cannot execute PHP logic. Please download the files and run them in a XAMPP environment to see the full functionality.
