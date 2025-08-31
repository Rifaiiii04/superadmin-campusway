# Super Admin Dashboard Setup Guide

## Overview

This is a responsive Super Admin dashboard built with Laravel + React (Inertia.js) + Tailwind CSS for managing an educational system with schools, students, questions, and monitoring capabilities.

## Features

-   **Dashboard**: Overview with statistics and quick actions
-   **School Management**: CRUD operations for schools with NPSN and password management
-   **Question Bank**: Manage questions (multiple choice and essay) with media support
-   **Global Monitoring**: National statistics and cross-school performance data
-   **Reports**: Download comprehensive reports in Excel format
-   **Responsive Design**: Works on desktop, tablet, and mobile devices

## Database Structure

The system includes the following tables:

-   `admins` - Super admin accounts
-   `schools` - School information (NPSN, name, password)
-   `students` - Student data (NISN, identity, school association)
-   `questions` - Question bank (text, image, audio support)
-   `question_options` - Multiple choice options
-   `answers` - Student answers
-   `results` - Student scores per subject
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
DB_DATABASE=superadmin_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
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

-   **Username**: superadmin
-   **Password**: password123

## Usage

### Accessing the Dashboard

Navigate to `/super-admin` after logging in with super admin credentials.

### School Management

-   Add new schools with NPSN, name, and default password
-   Edit existing school information
-   View school statistics and student counts

### Question Bank

-   Create questions with support for text, images, and audio
-   Support for multiple choice and essay questions
-   Organize questions by subject

### Monitoring

-   View national statistics across all schools
-   Compare school performance
-   Analyze subject performance trends

### Reports

-   Download comprehensive reports in Excel format
-   Filter data by date range
-   Multiple report types available

## File Structure

```
resources/js/Pages/SuperAdmin/
├── Dashboard.jsx          # Main dashboard
├── Schools.jsx            # School management
├── Questions.jsx          # Question bank management
├── Monitoring.jsx         # Global monitoring
└── Reports.jsx            # Report generation

resources/js/Layouts/
└── SuperAdminLayout.jsx   # Navigation layout

app/Http/Controllers/
└── SuperAdminController.php # Backend logic

app/Models/                 # Eloquent models
database/migrations/        # Database migrations
```

## Responsive Design Features

-   **Mobile-first approach** with Tailwind CSS
-   **Collapsible sidebar** for mobile devices
-   **Grid layouts** that adapt to screen size
-   **Touch-friendly** interface elements
-   **Optimized tables** for small screens

## Customization

-   Modify colors and styling in Tailwind config
-   Add new features by extending the controller and components
-   Customize database schema through migrations
-   Add new report types in the Reports component

## Security Features

-   Authentication required for all routes
-   Password hashing for admin and school accounts
-   Input validation and sanitization
-   CSRF protection enabled

## Future Enhancements

-   Excel import functionality for bulk data
-   Advanced analytics and charts
-   Real-time notifications
-   API endpoints for external integrations
-   Advanced search and filtering

## Troubleshooting

### Common Issues

1. **Database Connection**: Ensure SQL Server is running and credentials are correct
2. **Asset Building**: Run `npm run build` if styles aren't loading
3. **Permissions**: Ensure storage and cache directories are writable

### Support

For issues or questions, check the Laravel logs in `storage/logs/laravel.log`

## License

This project is part of the TKAWEB educational system.
