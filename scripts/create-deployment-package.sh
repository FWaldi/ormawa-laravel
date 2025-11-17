#!/bin/bash

# Deployment Package Creator for cPanel

echo "Creating deployment package for cPanel..."

# Set variables
DEPLOY_DIR="deployment-package"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PACKAGE_NAME="ormawa-unp-deploy-${TIMESTAMP}"

# Clean up previous deployment directory
if [ -d "$DEPLOY_DIR" ]; then
    rm -rf "$DEPLOY_DIR"
fi

# Create deployment directory
mkdir -p "$DEPLOY_DIR/$PACKAGE_NAME"

echo "Copying application files..."

# Copy essential application files
cp -r app/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r bootstrap/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r config/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r database/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r public/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r resources/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r routes/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r storage/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r docs/ "$DEPLOY_DIR/$PACKAGE_NAME/"
cp -r scripts/ "$DEPLOY_DIR/$PACKAGE_NAME/"

# Copy essential files
cp artisan "$DEPLOY_DIR/$PACKAGE_NAME/"
cp composer.json "$DEPLOY_DIR/$PACKAGE_NAME/"
cp composer.lock "$DEPLOY_DIR/$PACKAGE_NAME/"
cp .env.example "$DEPLOY_DIR/$PACKAGE_NAME/"
cp vite.config.js "$DEPLOY_DIR/$PACKAGE_NAME/"
cp package.json "$DEPLOY_DIR/$PACKAGE_NAME/"
cp .htaccess "$DEPLOY_DIR/$PACKAGE_NAME/public/" 2>/dev/null || true

echo "Removing development files..."

# Remove development files and directories
cd "$DEPLOY_DIR/$PACKAGE_NAME"

# Remove tests
rm -rf tests/
rm -f phpunit.xml

# Remove development configuration
rm -f .gitignore
rm -f .editorconfig
rm -f .styleci.yml
rm -f README.md
rm -f CHANGELOG.md
rm -f postcss.config.js

# Remove node_modules if it exists
rm -rf node_modules/

# Remove git directory
rm -rf .git/

# Remove any development scripts
rm -f webpack.mix.js 2>/dev/null || true

# Remove any log files
find storage/logs/ -name "*.log" -delete 2>/dev/null || true

# Remove cache files
rm -rf bootstrap/cache/*.php 2>/dev/null || true

# Remove compiled views
rm -rf storage/framework/views/*.php 2>/dev/null || true

cd ../..

echo "Creating deployment documentation..."

# Create deployment checklist
cat > "$DEPLOY_DIR/$PACKAGE_NAME/DEPLOYMENT_CHECKLIST.txt" << EOF
=== ORMawa UNP Deployment Checklist ===

Pre-Deployment:
[ ] Database created in cPanel
[ ] Database user created with privileges
[ ] Domain pointed to public_html directory
[ ] SSL certificate installed (recommended)

File Upload:
[ ] Upload all files to public_html directory
[ ] Set file permissions (755 for directories, 644 for files)
[ ] Set storage and bootstrap/cache to 755
[ ] Set .env file to 600
[ ] Make artisan executable (755)

Configuration:
[ ] Copy .env.example to .env
[ ] Update database credentials in .env
[ ] Update APP_URL to your domain
[ ] Generate application key: php artisan key:generate
[ ] Update mail configuration

Database:
[ ] Import phpmyadmin_migration.sql via phpMyAdmin
[ ] Import phpmyadmin_seeding.sql (optional)
[ ] Verify database connection

Final Steps:
[ ] Clear caches: php artisan cache:clear
[ ] Optimize: php artisan config:cache, route:cache, view:cache
[ ] Test application in browser
[ ] Change default passwords
[ ] Set up regular backups

Post-Deployment:
[ ] Monitor error logs
[ ] Test all features
[ ] Verify file uploads work
[ ] Check email functionality
EOF

# Create .gitignore for production
cat > "$DEPLOY_DIR/$PACKAGE_NAME/.gitignore" << EOF
.env
.env.backup
.env.*.local
storage/logs/*.log
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
bootstrap/cache/*.php
node_modules/
.DS_Store
Thumbs.db
EOF

echo "Creating archive..."

# Create zip archive
cd "$DEPLOY_DIR"
zip -r "${PACKAGE_NAME}.zip" "$PACKAGE_NAME/"

echo "Cleaning up..."

# Remove the uncompressed directory
rm -rf "$PACKAGE_NAME/"

cd ..

echo "Deployment package created successfully!"
echo "Package location: $DEPLOY_DIR/${PACKAGE_NAME}.zip"
echo ""
echo "Next steps:"
echo "1. Upload the zip file to your cPanel"
echo "2. Extract it in your public_html directory"
echo "3. Follow the DEPLOYMENT_CHECKLIST.txt instructions"
echo ""
echo "Package contents:"
echo "- Application files (optimized for production)"
echo "- Database migration scripts"
echo "- Deployment documentation"
echo "- Configuration templates"