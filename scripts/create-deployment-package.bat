@echo off
REM Deployment Package Creator for cPanel (Windows)

echo Creating deployment package for cPanel...

REM Set variables
set DEPLOY_DIR=deployment-package
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set TIMESTAMP=%dt:~0,8%_%dt:~8,6%
set PACKAGE_NAME=ormawa-unp-deploy-%TIMESTAMP%

REM Clean up previous deployment directory
if exist "%DEPLOY_DIR%" (
    rmdir /s /q "%DEPLOY_DIR%"
)

REM Create deployment directory
mkdir "%DEPLOY_DIR%\%PACKAGE_NAME%"

echo Copying application files...

REM Copy essential application files
xcopy /E /I /H app "%DEPLOY_DIR%\%PACKAGE_NAME%\app"
xcopy /E /I /H bootstrap "%DEPLOY_DIR%\%PACKAGE_NAME%\bootstrap"
xcopy /E /I /H config "%DEPLOY_DIR%\%PACKAGE_NAME%\config"
xcopy /E /I /H database "%DEPLOY_DIR%\%PACKAGE_NAME%\database"
xcopy /E /I /H public "%DEPLOY_DIR%\%PACKAGE_NAME%\public"
xcopy /E /I /H resources "%DEPLOY_DIR%\%PACKAGE_NAME%\resources"
xcopy /E /I /H routes "%DEPLOY_DIR%\%PACKAGE_NAME%\routes"
xcopy /E /I /H storage "%DEPLOY_DIR%\%PACKAGE_NAME%\storage"
xcopy /E /I /H docs "%DEPLOY_DIR%\%PACKAGE_NAME%\docs"
xcopy /E /I /H scripts "%DEPLOY_DIR%\%PACKAGE_NAME%\scripts"

REM Copy essential files
copy artisan "%DEPLOY_DIR%\%PACKAGE_NAME%\"
copy composer.json "%DEPLOY_DIR%\%PACKAGE_NAME%\"
copy composer.lock "%DEPLOY_DIR%\%PACKAGE_NAME%\"
copy .env.example "%DEPLOY_DIR%\%PACKAGE_NAME%\"
copy vite.config.js "%DEPLOY_DIR%\%PACKAGE_NAME%\"
copy package.json "%DEPLOY_DIR%\%PACKAGE_NAME%\"
if exist .htaccess copy .htaccess "%DEPLOY_DIR%\%PACKAGE_NAME%\public\"

echo Removing development files...

cd "%DEPLOY_DIR%\%PACKAGE_NAME%"

REM Remove tests
if exist tests rmdir /s /q tests
if exist phpunit.xml del phpunit.xml

REM Remove development configuration
if exist .gitignore del .gitignore
if exist .editorconfig del .editorconfig
if exist .styleci.yml del .styleci.yml
if exist README.md del README.md
if exist CHANGELOG.md del CHANGELOG.md
if exist postcss.config.js del postcss.config.js

REM Remove node_modules if it exists
if exist node_modules rmdir /s /q node_modules

REM Remove git directory
if exist .git rmdir /s /q .git

REM Remove any log files
for /r storage\logs %%f in (*.log) do del "%%f" 2>nul

REM Remove cache files
for %%f in (bootstrap\cache\*.php) do del "%%f" 2>nul

REM Remove compiled views
for /r storage\framework\views %%f in (*.php) do del "%%f" 2>nul

cd ..\..

echo Creating deployment documentation...

REM Create deployment checklist
echo === ORMawa UNP Deployment Checklist === > "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo. >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo Pre-Deployment: >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Database created in cPanel >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Database user created with privileges >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Domain pointed to public_html directory >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] SSL certificate installed (recommended) >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo. >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo File Upload: >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Upload all files to public_html directory >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Set file permissions (755 for directories, 644 for files) >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Set storage and bootstrap/cache to 755 >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Set .env file to 600 >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Make artisan executable (755) >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo. >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo Configuration: >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Copy .env.example to .env >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Update database credentials in .env >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Update APP_URL to your domain >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Generate application key: php artisan key:generate >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Update mail configuration >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo. >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo Database: >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Import phpmyadmin_migration.sql via phpMyAdmin >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Import phpmyadmin_seeding.sql (optional) >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Verify database connection >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo. >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo Final Steps: >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Clear caches: php artisan cache:clear >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Optimize: php artisan config:cache, route:cache, view:cache >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Test application in browser >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Change default passwords >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"
echo [ ] Set up regular backups >> "%DEPLOY_DIR%\%PACKAGE_NAME%\DEPLOYMENT_CHECKLIST.txt"

echo Creating archive...

cd "%DEPLOY_DIR%"

REM Create zip archive (requires PowerShell or external zip tool)
powershell -command "Compress-Archive -Path '%PACKAGE_NAME%' -DestinationPath '%PACKAGE_NAME%.zip' -Force"

echo Cleaning up...

REM Remove uncompressed directory
rmdir /s /q "%PACKAGE_NAME%"

cd ..

echo Deployment package created successfully!
echo Package location: %DEPLOY_DIR%\%PACKAGE_NAME%.zip
echo.
echo Next steps:
echo 1. Upload zip file to your cPanel
echo 2. Extract it in your public_html directory
echo 3. Follow DEPLOYMENT_CHECKLIST.txt instructions
echo.
echo Package contents:
echo - Application files (optimized for production)
echo - Database migration scripts
echo - Deployment documentation
echo - Configuration templates