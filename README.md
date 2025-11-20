# MyProjek - Article Management System

![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.3-orange)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-blue)
![License](https://img.shields.io/badge/License-MIT-green)

Sistem manajemen artikel berbasis web yang dibangun dengan CodeIgniter 4. Aplikasi ini menyediakan fitur lengkap untuk mengelola artikel, user authentication, feedback system, dan admin panel yang modern.

## ✨ Fitur Utama

### Frontend
- 📰 **Article Listing** - Tampilan artikel dengan pagination modern
- 🔍 **Search Functionality** - Pencarian artikel dengan keyword highlighting
- 📱 **Responsive Design** - Optimal di semua device (mobile, tablet, desktop)
- 💬 **Contact Form** - Form kontak dengan feedback system
- 🎨 **Modern UI/UX** - Interface yang clean dan user-friendly

### Admin Panel
- 🔐 **Authentication System** - Login/Register dengan session management
- 📝 **Article Management** - CRUD operations untuk artikel
- 📊 **Dashboard** - Overview statistik dan aktivitas
- 💬 **Feedback Management** - Kelola feedback dari user
- 👤 **Profile Management** - Edit profile, change password, upload avatar
- 🔍 **Advanced Search** - Pencarian artikel dengan excerpt highlighting
- 📄 **Pagination** - Custom pagination berdasarkan tutorial Petani Kode

## 🛠 Tech Stack

### Backend
- **Framework**: CodeIgniter 4.6.3
- **Language**: PHP 8.2
- **Database**: MySQL 8.0
- **ORM**: CodeIgniter Query Builder
- **Authentication**: Session-based Auth

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling dengan Flexbox & Grid
- **JavaScript (ES6+)** - Vanilla JS untuk interactivity
- **Font Awesome** - Icon library
- **SweetAlert2** - Beautiful alerts & modals

### Development Tools
- **Composer** - PHP dependency manager
- **Git** - Version control
- **XAMPP/WAMP** - Local development server

## 📦 Persyaratan Sistem

- PHP >= 8.1
- MySQL >= 5.7 atau MariaDB >= 10.3
- Apache/Nginx Web Server
- Composer
- PHP Extensions:
  - intl
  - mbstring
  - json
  - mysqlnd
  - curl

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/my-project.git
cd my-project
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp env .env
```

Edit file `.env`:

```env
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = myprojek
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306

encryption.key = your-encryption-key-here
```

### 4. Generate Encryption Key

```bash
php spark key:generate
```

## 💾 Database Setup

### 1. Buat Database

```sql
CREATE DATABASE myprojek CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Jalankan Migrations

```bash
php spark migrate
```

### 3. Seed Data Dummy

```bash
# Seed users (admin & demo)
php spark db:seed UserSeeder

# Seed articles (8 artikel)
php spark db:seed ArticleSeeder

# Seed feedbacks (12 feedback)
php spark db:seed FeedbackSeeder

# Atau jalankan semua seeder sekaligus
php spark db:seed DatabaseSeeder
```

## 📁 Struktur Project

```
my-project/
├── app/
│   ├── Config/
│   │   ├── Pager.php              # Pagination config
│   │   └── Routes.php             # Route definitions
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── Dashboard.php      # Admin dashboard
│   │   │   ├── Post.php           # Article management
│   │   │   ├── Feedback.php       # Feedback management
│   │   │   └── Setting.php        # Profile settings
│   │   ├── Article.php            # Frontend articles
│   │   ├── Auth.php               # Authentication
│   │   ├── Page.php               # Static pages
│   │   └── Search.php             # Search functionality
│   ├── Database/
│   │   ├── Migrations/            # Database migrations
│   │   └── Seeds/                 # Database seeders
│   ├── Filters/
│   │   └── AuthFilter.php         # Authentication filter
│   ├── Helpers/
│   │   ├── avatar_helper.php      # Avatar utilities
│   │   └── pagination_helper.php  # Pagination utilities
│   ├── Models/
│   │   ├── ArticleModel.php       # Article model
│   │   ├── AuthModel.php          # User model
│   │   └── ProfileModel.php       # Profile model
│   └── Views/
│       ├── admin/                 # Admin views
│       ├── articles/              # Article views
│       ├── Pager/                 # Custom pagination
│       └── partials/              # Reusable components
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css           # Main styles
│   │   │   ├── admin.css          # Admin styles
│   │   │   └── petanikode-pagination.css
│   │   └── js/
│   │       └── sidebar-avatar.js  # Avatar management
│   └── uploads/
│       └── avatars/               # User avatars
├── writable/
│   ├── cache/                     # Cache files
│   ├── logs/                      # Application logs
│   └── session/                   # Session files
├── .env                           # Environment config
├── composer.json                  # PHP dependencies
└── README.md                      # This file
```

## 🎯 Fitur Detail

### 1. Article Management

**Frontend Features:**
- List artikel dengan pagination (5 per halaman)
- Search artikel dengan keyword highlighting
- Article detail view dengan full content
- Responsive article grid layout

**Admin Features:**
- Create, Read, Update, Delete artikel
- Draft/Publish status management
- Rich text content editor
- Slug auto-generation
- Search dengan excerpt preview
- Pagination (10 per halaman)

### 2. Authentication System

- **Login** - Session-based authentication
- **Register** - User registration dengan validation
- **Logout** - Secure session destruction
- **Auth Filter** - Protected admin routes
- **Session Management** - Auto-redirect untuk unauthorized access

### 3. Profile Management

- **Edit Profile** - Update name & email
- **Change Password** - Secure password update
- **Avatar Upload** - Image upload dengan validation
- **Avatar Remove** - Reset to default avatar
- **Real-time Avatar Update** - Sidebar avatar sync

### 4. Pagination System

Implementasi berdasarkan tutorial [Petani Kode](https://www.petanikode.com/codeigniter-pagination/):

- Custom pagination template
- Bootstrap-like styling
- Pagination info display
- First/Previous/Next/Last navigation
- Responsive design
- Search integration

### 5. Search Functionality

- **Keyword Search** - Search di title dan content
- **Highlight Results** - Keyword highlighting
- **Excerpt Display** - Context preview
- **No Results Handling** - User-friendly empty state

## 🧪 Testing

### Manual Testing

```bash
# Test artikel frontend
http://localhost/my-project/articles

# Test search
http://localhost/my-project/articles?keyword=php

# Test admin login
http://localhost/my-project/login

# Test admin dashboard
http://localhost/my-project/admin/dashboard
```

## 🚢 Deployment

### Production Checklist

1. **Environment**
   ```env
   CI_ENVIRONMENT = production
   ```

2. **Security**
   - Generate new encryption key
   - Update database credentials
   - Enable HTTPS
   - Set secure session cookies

3. **Performance**
   - Enable caching
   - Optimize database queries
   - Minify CSS/JS
   - Enable gzip compression

4. **Database**
   - Run migrations
   - Backup database
   - Set proper user permissions

## 📝 License

This project is licensed under the MIT License.

## 🙏 Acknowledgments

- [CodeIgniter 4](https://codeigniter.com/) - The PHP framework
- [Petani Kode](https://www.petanikode.com/) - Pagination tutorial
- [SweetAlert2](https://sweetalert2.github.io/) - Beautiful alerts
- [Font Awesome](https://fontawesome.com/) - Icon library
- [UI Avatars](https://ui-avatars.com/) - Default avatar generator

---

**Happy Coding! 🚀**
