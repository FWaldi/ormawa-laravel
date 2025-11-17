# cPanel File Permissions Guide

## Required Permissions for cPanel Deployment

### Directory Permissions
- **755** for most directories
- **755** for `storage/` and subdirectories
- **755** for `bootstrap/cache/`
- **755** for `public/`

### File Permissions
- **644** for most PHP files
- **600** for sensitive configuration files (`.env`, database configs)
- **755** for executable files (`artisan`)

### Critical Directories
```
storage/                 # 755 (writable by web server)
├── app/                # 755
├── framework/          # 755
│   ├── cache/         # 755
│   ├── sessions/      # 755
│   ├── views/         # 755
│   └── testing/       # 755
└── logs/              # 755

bootstrap/cache/        # 755 (writable by web server)
public/                 # 755 (web root)
```

### Sensitive Files (600 permissions)
- `.env`
- `config/database.php`
- `config/mail.php`
- `config/services.php`

### Commands to Run on cPanel
```bash
# Set ownership (replace with your cPanel username)
chown -R your_username:your_username /home/your_username/public_html/

# Set directory permissions
find /home/your_username/public_html -type d -exec chmod 755 {} \;

# Set file permissions
find /home/your_username/public_html -type f -exec chmod 644 {} \;

# Special permissions for writable directories
chmod -R 755 /home/your_username/public_html/storage/
chmod -R 755 /home/your_username/public_html/bootstrap/cache/

# Secure sensitive files
chmod 600 /home/your_username/public_html/.env
chmod 600 /home/your_username/public_html/config/database.php

# Make artisan executable
chmod 755 /home/your_username/public_html/artisan
```

### cPanel File Manager Method
1. Log into cPanel
2. Open File Manager
3. Navigate to your application directory
4. Select all files and folders
5. Right-click → Change Permissions
6. Set directories to 755, files to 644
7. Manually set sensitive files to 600
8. Set storage and bootstrap/cache to 755