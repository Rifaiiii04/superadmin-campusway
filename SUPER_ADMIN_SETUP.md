# Super Admin Dashboard Setup Guide

## Overview

This is a responsive Super Admin dashboard built with Laravel + React (Inertia.js) + Tailwind CSS for managing an educational system with schools, students, questions, and monitoring capabilities.

## Features

-   **Dashboard**: Overview with statistics and quick actions
-   **School Management**: CRUD operations for schools with NPSN and password management
-   **Question Bank**: Manage questions (multiple choice only) with media support
-   **Global Monitoring**: National statistics and cross-school performance data
-   **Reports**: Download comprehensive reports in CSV format with proper column separation
-   **Import Features**: Import questions and schools from CSV/Excel files
-   **Responsive Design**: Works on desktop, tablet, and mobile devices with collapsible sidebar

## Database Structure

The system includes the following tables:

-   `admins` - Super admin accounts
-   `schools` - School information (NPSN, name, password_hash)
-   `students` - Student data (NISN, name, school_id, kelas, email, phone, status)
-   `questions` - Question bank (text, image, audio support)
-   `question_options` - Multiple choice options
-   `test_results` - Student test results with scores and recommendations
-   `test_answers` - Individual student answers to test questions
-   `subjects` - Available test subjects (required and optional)
-   `results` - Individual subject results
-   `recommendations` - Major recommendations for students

## Installation & Setup

### 1. Prerequisites

-   PHP 8.1+
-   Composer
-   Node.js & npm
-   SQL Server Express (or Azure SQL Database)

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

Copy `.env.example` to `.env` and configure:

```env
DB_CONNECTION=sqlsrv
DB_HOST=127.0.0.1
DB_PORT=1433
DB_DATABASE=campusway_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=SubjectsSeeder
php artisan db:seed --class=SampleDataSeeder
```

### 5. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Development Server

```bash
php artisan serve
```

## Default Login Credentials

-   **Username**: `campusway_superadmin`
-   **Password**: `campusway321@`

## Usage

### Accessing the Dashboard

Navigate to `/super-admin` after logging in with super admin credentials.

### School Management

-   Add new schools with NPSN, name, and default password
-   Import schools from CSV/Excel files (NPSN, Nama Sekolah, Password only)
-   Edit existing school information
-   View school statistics and student counts

### Question Bank

-   Create multiple choice questions with support for text, images, and audio
-   Import questions from CSV/Excel files with specific column format
-   Organize questions by subject
-   Pagination, search, and sorting capabilities
-   Compact display of correct answers

### Monitoring

-   View national statistics across all schools
-   Compare school performance
-   Analyze subject performance trends
-   Real-time data from database (no dummy data)

### Reports

-   Download comprehensive reports in CSV format
-   Proper column separation (semicolon delimiter)
-   Filter data by date range
-   Multiple report types available (schools, students, results, questions)

## File Structure

```
resources/js/Pages/SuperAdmin/
├── Dashboard.jsx          # Main dashboard
├── Schools.jsx            # School management
├── Questions.jsx          # Question bank management
├── Monitoring.jsx         # Global monitoring
└── Reports.jsx            # Report generation

resources/js/Layouts/
└── SuperAdminLayout.jsx   # Navigation layout with responsive sidebar

resources/js/Pages/SuperAdmin/components/
├── ImportQuestionsModal.jsx    # Import questions modal
├── ImportSchoolsModal.jsx      # Import schools modal
├── QuestionTable.jsx           # Questions table with pagination
└── Pagination.jsx              # Pagination component

app/Http/Controllers/
└── SuperAdminController.php    # Backend logic

app/Models/                     # Eloquent models
database/migrations/            # Database migrations
database/seeders/               # Database seeders
```

## Responsive Design Features

-   **Mobile-first approach** with Tailwind CSS
-   **Collapsible sidebar** for mobile and tablet devices
-   **Desktop sidebar** preserved with full height and proper alignment
-   **Grid layouts** that adapt to screen size
-   **Touch-friendly** interface elements
-   **Optimized tables** for small screens
-   **Custom CSS classes** for desktop-specific styling

## Import Features

### Questions Import

-   **Required Columns**: Mata Pelajaran, Tipe Soal, Media, Opsi Jawaban
-   **Format**: CSV/Excel with semicolon delimiter
-   **Validation**: Automatic validation and error handling
-   **Loading Animation**: Visual feedback during import process

### Schools Import

-   **Required Columns**: NPSN, Nama Sekolah, Password
-   **Format**: CSV/Excel with semicolon delimiter
-   **Validation**: NPSN uniqueness and data format validation

## Customization

-   Modify colors and styling in Tailwind config
-   Add new features by extending the controller and components
-   Customize database schema through migrations
-   Add new report types in the Reports component
-   Adjust responsive breakpoints in `resources/css/superadmin.css`

## Security Features

-   Authentication required for all routes
-   Password hashing for admin and school accounts
-   Input validation and sanitization
-   CSRF protection enabled (with specific exclusions for import routes)
-   Custom middleware for admin authentication without CSRF

## Recent Fixes & Improvements

### 1. **Database Column Mapping**

-   Fixed mismatch between controller field names and database columns
-   Students table now uses `name`, `school_id`, `phone` instead of legacy names
-   Proper foreign key relationships established

### 2. **CSV Export Format**

-   Fixed CSV column separation issue (now uses semicolon delimiter)
-   Added UTF-8 BOM for proper Excel compatibility
-   Proper column headers and data formatting

### 3. **Import Functionality**

-   Resolved CSRF token issues for import routes
-   Added loading animations and error handling
-   Improved validation and user feedback

### 4. **Responsive Design**

-   Fixed desktop sidebar height and alignment issues
-   Maintained original desktop design while adding mobile responsiveness
-   Added custom CSS classes for desktop-specific styling

### 5. **Question Management**

-   Removed essay question support (multiple choice only)
-   Added pagination, search, and sorting capabilities
-   Optimized table display for better user experience

## Future Enhancements

-   Advanced analytics and charts
-   Real-time notifications
-   API endpoints for external integrations
-   Advanced search and filtering
-   Bulk operations for questions and schools
-   Export to additional formats (PDF, Excel)

## Troubleshooting

### Common Issues

1. **Database Connection**: Ensure SQL Server is running and credentials are correct
2. **Asset Building**: Run `npm run build` if styles aren't loading
3. **Permissions**: Ensure storage and cache directories are writable
4. **Import Failures**: Check CSV format and column headers
5. **CSRF Errors**: Verify import routes are properly excluded from CSRF verification

### Support

For issues or questions, check the Laravel logs in `storage/logs/laravel.log`

## License

This project is part of the TKAWEB educational system.
