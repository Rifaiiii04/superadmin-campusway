# SuperAdmin Campusway

A Laravel-based SuperAdmin dashboard application for managing campus and student data with TKA (Tes Kemampuan Akademik) functionality.

## 🚀 Features

- **SuperAdmin Dashboard** - Complete admin interface for managing schools, students, and academic data
- **Authentication System** - Secure login with admin guard
- **Student Management** - Manage student data, subjects, and academic choices
- **School Management** - Handle school information and class data
- **TKA Management** - Test scheduling and question management
- **Reports & Analytics** - Comprehensive reporting system
- **Modern UI** - Built with React.js and Inertia.js

## 🛠️ Tech Stack

- **Backend**: Laravel 11
- **Frontend**: React.js + Inertia.js
- **Database**: SQL Server / MySQL
- **Styling**: Tailwind CSS
- **Authentication**: Laravel Guards

## 📋 Requirements

- PHP 8.1+
- Composer
- Node.js & NPM
- SQL Server / MySQL
- Apache/Nginx

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/raihan-yasykur/superadmin-campusway.git
   cd superadmin-campusway
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Run the application**
   ```bash
   php artisan serve
   ```

## 🔐 Default Login

- **URL**: `/login`
- **Username**: `admin`
- **Password**: `admin123`

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # API and web controllers
│   ├── Models/              # Eloquent models
│   └── Http/Middleware/     # Custom middleware
├── resources/
│   ├── js/Pages/            # React components
│   │   └── SuperAdmin/      # SuperAdmin specific pages
│   └── css/                 # Stylesheets
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── database/
    ├── migrations/          # Database migrations
    └── seeders/            # Database seeders
```

## 🎯 Main Features

### SuperAdmin Dashboard
- School management and monitoring
- Student data management
- Academic subject management
- TKA test scheduling
- Reports and analytics

### Authentication
- Secure admin login system
- Session management
- CSRF protection

### Data Management
- Student enrollment
- School information
- Academic subjects
- Test questions and answers

## 🚀 Deployment

1. **Production setup**
   ```bash
   composer install --no-dev
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Web server configuration**
   - Point document root to `public/` directory
   - Configure URL rewriting for Laravel

## 📝 API Endpoints

- `GET /health` - Health check
- `GET /test` - System test
- `POST /login` - Admin login
- `GET /dashboard` - SuperAdmin dashboard
- `POST /logout` - Admin logout

## 🔧 Configuration

### Database
Configure your database connection in `.env`:
```env
DB_CONNECTION=sqlsrv
DB_HOST=your_host
DB_PORT=1433
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Session
Configure session settings in `config/session.php`:
```php
'path' => '/super-admin',
```

## 📊 Database Schema

- **admins** - SuperAdmin users
- **schools** - School information
- **students** - Student data
- **subjects** - Academic subjects
- **questions** - TKA questions
- **results** - Test results
- **major_recommendations** - Academic recommendations

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 👥 Authors

- **Raihan Yasykur** - *Initial work* - [raihan-yasykur](https://github.com/raihan-yasykur)

## 📞 Support

For support, email raihan@campusway.com or create an issue in the repository.

---

**SuperAdmin Campusway** - Managing educational data with modern technology.