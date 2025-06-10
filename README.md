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