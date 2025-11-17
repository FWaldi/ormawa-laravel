# Environment Configuration for cPanel Deployment

## Required Environment Variables

### Application Configuration
```bash
APP_NAME="Ormawa UNP"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### Database Configuration
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ormawa_unp
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password
```

### Cache Configuration (Optimized for cPanel)
```bash
CACHE_STORE=file
CACHE_PREFIX=ormawa_unp_
```

### Session Configuration (File-based for cPanel)
```bash
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=yourdomain.com
```

### Logging Configuration (Production-ready)
```bash
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
```

### Mail Configuration (cPanel Email)
```bash
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### File Upload Configuration
```bash
FILESYSTEM_DISK=local
```

### Queue Configuration (Database for cPanel)
```bash
QUEUE_CONNECTION=database
```

## cPanel-Specific Settings

### Performance Optimizations
```bash
# PHP Memory Limit
PHP_MEMORY_LIMIT=256M

# Execution Time
MAX_EXECUTION_TIME=300

# Upload Limits
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=10M
```

### Security Settings
```bash
# Bcrypt rounds for password hashing
BCRYPT_ROUNDS=12

# Maintenance mode
APP_MAINTENANCE_DRIVER=file
```

## Setup Instructions

### 1. Generate Application Key
```bash
php artisan key:generate --show
```
Copy the generated key to your APP_KEY environment variable.

### 2. Create Database in cPanel
1. Log into cPanel
2. Go to "MySQL Databases"
3. Create a new database: `ormawa_unp`
4. Create a database user
5. Add the user to the database with all privileges

### 3. Configure Email
1. Create an email account in cPanel
2. Use the email credentials for MAIL_USERNAME and MAIL_PASSWORD

### 4. Set Domain
Update APP_URL and SESSION_DOMAIN to match your actual domain.

## Environment File Security

### File Permissions
```bash
chmod 600 .env
```

### .gitignore (if applicable)
```
.env
.env.backup
.env.*.local
```

## Testing Environment Configuration

### Verify Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Verify Cache
```bash
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');
```

### Verify Sessions
```bash
php artisan session:table
php artisan migrate
```

## Common Issues and Solutions

### 1. Database Connection Failed
- Check database credentials
- Verify database exists
- Ensure user has proper permissions

### 2. Cache/Session Issues
- Ensure storage directories are writable (755)
- Check file permissions

### 3. Email Not Sending
- Verify SMTP settings
- Check email credentials
- Ensure firewall allows SMTP connections

### 4. File Upload Issues
- Check upload_max_filesize and post_max_size in php.ini
- Verify storage directory permissions