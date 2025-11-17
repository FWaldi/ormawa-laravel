# Ormawa UNP cPanel Deployment Guide

## Overview
This guide provides complete instructions for deploying the Ormawa UNP Laravel application to a cPanel shared hosting environment.

## Prerequisites

### Server Requirements
- cPanel hosting account
- PHP 8.2 or higher
- MySQL 8.0 or MariaDB 10.3+
- 256MB+ PHP memory limit
- 10MB+ upload limits
- SSL certificate (recommended)

### Required Tools
- FTP client (FileZilla, WinSCP, or cPanel File Manager)
- phpMyAdmin access
- Text editor for configuration

## Step 1: Database Setup

### 1.1 Create Database
1. Log into cPanel
2. Navigate to **MySQL Databases**
3. Under **Create New Database**, enter: `ormawa_unp`
4. Click **Create Database**

### 1.2 Create Database User
1. Under **Add New User**, enter:
   - Username: `ormawa_user`
   - Password: Generate strong password
2. Click **Create User**

### 1.3 Assign Privileges
1. Under **Add User to Database**:
   - Select user: `ormawa_user`
   - Select database: `ormawa_unp`
2. Check **ALL PRIVILEGES**
3. Click **Add**

### 1.4 Import Database Structure
1. Navigate to **phpMyAdmin**
2. Select `ormawa_unp` database
3. Click **Import** tab
4. Upload `database/migrations/phpmyadmin_migration.sql`
5. Click **Go**

### 1.5 Import Initial Data (Optional)
1. With same database selected
2. Click **Import** tab
3. Upload `database/migrations/phpmyadmin_seeding.sql`
4. Click **Go**

## Step 2: File Upload

### 2.1 Create Deployment Package
Run the deployment script locally:
```bash
# On Linux/Mac
./scripts/create-deployment-package.sh

# On Windows
scripts\create-deployment-package.bat
```

This creates `deployment-package/ormawa-unp-deploy-YYYYMMDD_HHMMSS.zip`

### 2.2 Upload Files
1. Log into cPanel File Manager or use FTP
2. Navigate to `public_html/`
3. Upload and extract the deployment package
4. Ensure files are in the correct structure:
   ```
   public_html/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── public/ (web root)
   ├── resources/
   ├── routes/
   ├── storage/
   ├── artisan
   ├── composer.json
   ├── .env.example
   └── ...
   ```

### 2.3 Set File Permissions
Using cPanel File Manager:
1. Select all files and folders
2. Right-click → **Change Permissions**
3. Set directories to **755**
4. Set files to **644**
5. Set special permissions:
   - `storage/` and subdirectories: **755**
   - `bootstrap/cache/`: **755**
   - `.env`: **600**
   - `artisan`: **755**

## Step 3: Configuration

### 3.1 Environment Setup
1. Copy `.env.example` to `.env`
2. Edit `.env` file with your settings:

```bash
# Application
APP_NAME="Ormawa UNP"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ormawa_unp
DB_USERNAME=ormawa_user
DB_PASSWORD=your_db_password

# Cache & Session
CACHE_STORE=file
SESSION_DRIVER=file

# Mail (configure with your cPanel email)
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### 3.2 Generate Application Key
In cPanel Terminal or via PHP script:
```bash
php artisan key:generate
```

### 3.3 Install Dependencies
If composer is available in cPanel:
```bash
composer install --no-dev --optimize-autoloader
```

Otherwise, upload the `vendor/` directory from your local build.

### 3.4 Build Assets
If Node.js is available:
```bash
npm install --production
npm run build
```

Otherwise, ensure `public/build/` directory exists with compiled assets.

## Step 4: Final Setup

### 4.1 Create Storage Directories
```bash
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/uploads
```

### 4.2 Set Directory Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 4.3 Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

### 4.4 Create Symbolic Link
```bash
php artisan storage:link
```

## Step 5: Testing

### 5.1 Basic Tests
1. Open your domain in browser
2. Verify homepage loads
3. Check for any error messages

### 5.2 Database Test
Create a test file `test-db.php` in public directory:
```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::connection()->getPdo();
    echo "Database connection: SUCCESS";
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage();
}
?>
```
Access `https://yourdomain.com/test-db.php` and remove after testing.

### 5.3 Feature Tests
1. Test login with default credentials
2. Test file upload functionality
3. Test email functionality
4. Verify all pages load correctly

## Step 6: Security

### 6.1 Change Default Passwords
Login with admin account and change all default passwords:
- Admin: admin@ormawa-unp.com / admin123
- BEM: bem@ormawa-unp.com / admin123
- HIMTIF: himtif@ormawa-unp.com / admin123

### 6.2 Secure Sensitive Files
Ensure these files have 600 permissions:
- `.env`
- `config/database.php`
- `config/mail.php`
- `config/services.php`

### 6.3 Remove Test Files
Delete any test files created during deployment.

## Step 7: Maintenance

### 7.1 Regular Backups
Set up automatic backups in cPanel:
1. Navigate to **Backup**
2. Configure daily database backups
3. Set up weekly full backups

### 7.2 Log Monitoring
Regularly check:
- `storage/logs/laravel.log`
- cPanel error logs
- Database error logs

### 7.3 Updates
When updating:
1. Backup current version
2. Test updates in staging
3. Clear caches after updates
4. Verify all functionality

## Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check `.env` file permissions (600)
- Verify database credentials
- Check `storage/logs/laravel.log`
- Ensure all directories have correct permissions

#### Database Connection Failed
- Verify database exists
- Check user privileges
- Test credentials in phpMyAdmin
- Ensure MySQL server is running

#### File Upload Issues
- Check `storage/` directory permissions (755)
- Verify PHP upload limits
- Check disk space
- Ensure `storage:link` was created

#### Email Not Sending
- Verify SMTP settings
- Check email credentials
- Test with cPanel email account
- Check firewall settings

#### Performance Issues
- Enable OPcache in cPanel
- Optimize images
- Use file-based caching
- Monitor resource usage

### Error Messages

#### "Whoops, looks like something went wrong"
1. Set `APP_DEBUG=true` temporarily in `.env`
2. Check detailed error message
3. Fix the issue
4. Set `APP_DEBUG=false` again

#### "Storage directory not writable"
1. Check permissions: `chmod -R 755 storage/`
2. Verify ownership
3. Check disk space

#### "Database connection refused"
1. Verify database server is running
2. Check host and port settings
3. Test with phpMyAdmin

## Performance Optimization

### PHP Configuration
In cPanel **Select PHP Version** → **Options**:
- `memory_limit`: 256M
- `max_execution_time`: 300
- `upload_max_filesize`: 10M
- `post_max_size`: 10M
- `opcache.enable`: On

### Laravel Optimization
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Warm up application cache
php artisan cache:warm --url=https://yourdomain.com
```

### Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_activities_status_date ON activities(status, start_date);
CREATE INDEX idx_announcements_org ON announcements(organization_id);
CREATE INDEX idx_news_created_by ON news(created_by);

-- Optimize tables
OPTIMIZE TABLE users, organizations, activities, announcements, news;
```

## Support

### Documentation References
- [Environment Setup Guide](docs/environment-setup.md)
- [Database Migration Guide](docs/database-migration.md)
- [File Permissions Guide](docs/cpanel-permissions.md)

### Emergency Contacts
- System Administrator: [Contact Info]
- Database Administrator: [Contact Info]
- Hosting Provider: [Contact Info]

### Useful Commands
```bash
# Check Laravel version
php artisan --version

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Clear application cache
php artisan cache:clear

# View routes
php artisan route:list

# Check configuration
php artisan config:show
```

---

**Deployment completed successfully!** Your Ormawa UNP application should now be running on cPanel.