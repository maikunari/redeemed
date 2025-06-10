# Redeemed - Digital Content Download Code System

## Overview
A secure and user-friendly system for managing downloadable content through unique redemption codes. Built with Laravel 11 and Vue.js 3, this system enables administrators to upload files, generate download codes, and manage digital content distribution while providing users with a simple interface to redeem their codes and download files.

## Features
- **Secure File Management**
  - Secure storage and delivery of downloadable files
  - Protection against unauthorized access
  - File type and size validation
  - Secure upload/download handling

- **Code Management**
  - Unique, readable download codes (XXXXXX format)
  - Usage tracking and limits
  - Code expiration support
  - CSV export functionality
  - QR code generation

- **Professional Business Card Export**
  - **Avery 5371 format** - 10 cards per sheet (2×5 grid, 3.5" × 2")
  - **Double-sided printing support** - Front and back sides on separate pages
  - **QR codes** with download links and branding
  - **Professional design** with customizable colors and fonts
  - **Print shop ready** - Includes comprehensive printing instructions
  - **Perfect alignment** for professional printing services

- **User Interface**
  - Modern, responsive design
  - Google Authenticator-style code input
  - Drag-and-drop file uploads
  - Toast notifications
  - Visual file previews
  - Dashboard with metrics

## Tech Stack
- **Backend**: Laravel 11.x (PHP 8.1+)
- **Frontend**: Vue.js 3 with Inertia.js
- **Database**: MySQL
- **Styling**: Tailwind CSS
- **Key Packages**:
  - Laravel Vite
  - Spatie Laravel Media Library
  - SimpleSoftwareIO Simple QRCode
  - League CSV

## Requirements
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Node.js 16+ and NPM
- Composer

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/maikunari/redeemed.git
   cd redeemed
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Set up database:
   ```bash
   php artisan migrate
   ```

6. Link storage:
   ```bash
   php artisan storage:link
   ```

7. Build assets:
   ```bash
   npm run build
   ```

## Development
- Run development server:
  ```bash
  php artisan serve
  ```
- Watch for asset changes:
  ```bash
  npm run dev
  ```

## Production Deployment

### Preparing for Production

1. **Build production assets**:
   ```bash
   npm run build
   ```

2. **Optimize Composer dependencies**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Create production .env file**:
   ```bash
   cp .env .env.production
   # Edit .env.production with production settings
   ```

4. **Clear and cache configuration**:
   ```bash
   php artisan config:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

### Shared Hosting Deployment (cPanel/Site5/etc.)

**Recommended Structure:**
```
public_html/              (web root)
├── index.php            (Laravel's public/index.php)
├── .htaccess            (Laravel's public/.htaccess)  
├── build/               (Laravel's public/build/)
└── storage/             (symlink to ../laravel/storage/app/public)

laravel/                 (outside web root - SECURE)
├── app/
├── config/
├── database/
├── routes/
├── storage/
├── vendor/
├── .env
└── (all other Laravel files)
```

**Deployment Steps:**
1. **Upload Laravel files** (except `public/`) to `laravel/` directory
2. **Upload `public/` contents** to `public_html/`
3. **Edit `public_html/index.php`**:
   ```php
   require __DIR__.'/../laravel/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel/bootstrap/app.php';
   ```
4. **Terminal commands** (via cPanel Terminal):
   ```bash
   cd laravel
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   chmod -R 755 storage/ bootstrap/cache/
   ```

### VPS/Dedicated Server Deployment

**Using traditional web server:**
1. Set document root to `public/` folder
2. Configure web server (Apache/Nginx) 
3. Set proper file permissions
4. Configure SSL certificate
5. Set up process manager (if needed)

**Using Laravel Forge/Vapor:**
- Follow platform-specific deployment guides
- Configure environment variables
- Set up database connections
- Configure queues and cron jobs

### Environment Variables

**Key production settings:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail (for contact form)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

### Post-Deployment Checklist

- [ ] Site loads correctly
- [ ] SSL certificate installed and working
- [ ] Database migrations completed
- [ ] File uploads working
- [ ] Business card PDF generation working
- [ ] Contact form sending emails
- [ ] Error logging configured
- [ ] Backups scheduled
- [ ] Performance optimization enabled

### Ongoing Updates

**For template/content changes:**
1. Upload changed files via FTP/File Manager
2. Clear caches: `php artisan view:clear && php artisan optimize`

**For application updates:**
```bash
# Upload new code
composer install --no-dev
php artisan migrate --force
php artisan optimize
```

**Detailed deployment guides available in `/docs/` directory.**

## Security
This project implements several security measures:
- Secure session management
- Protection against common web vulnerabilities
- Input validation and sanitization
- Secure file handling
- CSRF protection

## Contributing
1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author
@maikunari 

## Admin account setup

Registration is disabled by default in production. To create the first admin account:

1. Run migrations (if you haven't already):
   ```bash
   php artisan migrate --force
   ```
2. Register through the `/register` page **once**; the first user automatically gets `is_admin = true`.
   After the first user exists, you can disable the register routes or leave them—subsequent sign-ups won't be admin.

OR create admins via artisan:

```bash
php artisan tinker

// inside tinker
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('secret'),
    'email_verified_at' => now(),
    'is_admin' => true,
]);
```

Use the admin account to log in and manage the site. 