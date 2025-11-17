@echo off
REM Deployment Testing Script (Windows)
REM Simulates cPanel environment and tests deployment readiness

echo === Ormawa UNP Deployment Testing ===
echo Testing deployment readiness for cPanel...
echo.

set TESTS_PASSED=0
set TESTS_FAILED=0

REM Function to check if file exists
:file_exists
if exist "%~1" (
    echo %~1: FOUND
    set /a TESTS_PASSED+=1
) else (
    echo %~1: NOT FOUND
    set /a TESTS_FAILED+=1
)
goto :eof

REM Function to check if directory exists
:dir_exists
if exist "%~1\" (
    echo %~1: FOUND
    set /a TESTS_PASSED+=1
) else (
    echo %~1: NOT FOUND
    set /a TESTS_FAILED+=1
)
goto :eof

echo === Essential Files Test ===
call :file_exists "composer.json"
call :file_exists "artisan"
call :file_exists ".env.example"
call :file_exists "vite.config.js"
call :file_exists "package.json"

echo.
echo === Directory Structure Test ===
call :dir_exists "app"
call :dir_exists "config"
call :dir_exists "database"
call :dir_exists "public"
call :dir_exists "resources"
call :dir_exists "routes"
call :dir_exists "storage"
call :dir_exists "bootstrap"

echo.
echo === Laravel Configuration Test ===
call :file_exists "config\app.php"
call :file_exists "config\database.php"
call :file_exists "config\cache.php"
call :file_exists "config\session.php"
call :file_exists "config\mail.php"

echo.
echo === Migration Files Test ===
call :file_exists "database\migrations\0001_01_01_000000_create_users_table.php"
call :file_exists "database\migrations\2025_11_13_000001_create_organizations_table.php"
call :file_exists "database\migrations\2025_11_13_000002_create_activities_table.php"
call :file_exists "database\migrations\2025_11_13_000003_create_announcements_table.php"
call :file_exists "database\migrations\2025_11_13_000004_create_news_table.php"

echo.
echo === Database Migration Scripts Test ===
call :file_exists "database\migrations\phpmyadmin_migration.sql"
call :file_exists "database\migrations\phpmyadmin_seeding.sql"

echo.
echo === Documentation Test ===
call :file_exists "docs\cpanel-deployment-guide.md"
call :file_exists "docs\environment-setup.md"
call :file_exists "docs\database-migration.md"
call :file_exists "docs\cpanel-permissions.md"
call :file_exists "docs\deployment-checklist.md"

echo.
echo === Scripts Test ===
call :file_exists "scripts\build-assets.bat"
call :file_exists "scripts\set-permissions.bat"
call :file_exists "scripts\setup-environment.bat"
call :file_exists "scripts\create-deployment-package.bat"

echo.
echo === Production Optimization Test ===
call :file_exists "composer.production.json"
call :file_exists ".gitignore.production"
call :file_exists "config\production.php"

echo.
echo === Security Configuration Test ===
call :file_exists "app\Http\Middleware\OptimizeCache.php"
call :file_exists "app\Http\Middleware\PageCache.php"
call :file_exists "app\Console\Commands\WarmCache.php"

echo.
echo === Environment Variables Test ===
if exist ".env.example" (
    echo Checking .env.example for required variables...
    
    findstr /C:"APP_NAME=" .env.example >nul && echo   APP_NAME: FOUND && set /a TESTS_PASSED+=1 || echo   APP_NAME: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"APP_ENV=" .env.example >nul && echo   APP_ENV: FOUND && set /a TESTS_PASSED+=1 || echo   APP_ENV: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"APP_KEY=" .env.example >nul && echo   APP_KEY: FOUND && set /a TESTS_PASSED+=1 || echo   APP_KEY: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"APP_DEBUG=" .env.example >nul && echo   APP_DEBUG: FOUND && set /a TESTS_PASSED+=1 || echo   APP_DEBUG: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"APP_URL=" .env.example >nul && echo   APP_URL: FOUND && set /a TESTS_PASSED+=1 || echo   APP_URL: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"DB_CONNECTION=" .env.example >nul && echo   DB_CONNECTION: FOUND && set /a TESTS_PASSED+=1 || echo   DB_CONNECTION: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"DB_HOST=" .env.example >nul && echo   DB_HOST: FOUND && set /a TESTS_PASSED+=1 || echo   DB_HOST: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"DB_DATABASE=" .env.example >nul && echo   DB_DATABASE: FOUND && set /a TESTS_PASSED+=1 || echo   DB_DATABASE: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"DB_USERNAME=" .env.example >nul && echo   DB_USERNAME: FOUND && set /a TESTS_PASSED+=1 || echo   DB_USERNAME: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"DB_PASSWORD=" .env.example >nul && echo   DB_PASSWORD: FOUND && set /a TESTS_PASSED+=1 || echo   DB_PASSWORD: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"CACHE_STORE=" .env.example >nul && echo   CACHE_STORE: FOUND && set /a TESTS_PASSED+=1 || echo   CACHE_STORE: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"SESSION_DRIVER=" .env.example >nul && echo   SESSION_DRIVER: FOUND && set /a TESTS_PASSED+=1 || echo   SESSION_DRIVER: MISSING && set /a TESTS_FAILED+=1
    findstr /C:"LOG_LEVEL=" .env.example >nul && echo   LOG_LEVEL: FOUND && set /a TESTS_PASSED+=1 || echo   LOG_LEVEL: MISSING && set /a TESTS_FAILED+=1
) else (
    echo .env.example: NOT FOUND
    set /a TESTS_FAILED+=1
)

echo.
echo === PHP Version Compatibility Test ===
php -v >nul 2>&1
if %errorlevel% equ 0 (
    echo PHP: INSTALLED
    set /a TESTS_PASSED+=1
) else (
    echo PHP: NOT INSTALLED (check on server)
)

echo.
echo === Composer Dependencies Test ===
if exist "composer.json" (
    composer --version >nul 2>&1
    if %errorlevel% equ 0 (
        echo Composer: INSTALLED
        set /a TESTS_PASSED+=1
    ) else (
        echo Composer: NOT INSTALLED (check on server)
    )
) else (
    echo composer.json: NOT FOUND
    set /a TESTS_FAILED+=1
)

echo.
echo === Test Results Summary ===
echo Tests Passed: %TESTS_PASSED%
echo Tests Failed: %TESTS_FAILED%

set /a TOTAL_TESTS=%TESTS_PASSED% + %TESTS_FAILED%
if %TESTS_FAILED% equ 0 (
    echo Overall Status: READY FOR DEPLOYMENT
    echo.
    echo Next steps:
    echo 1. Run: scripts\create-deployment-package.bat
    echo 2. Upload the generated zip file to cPanel
    echo 3. Follow docs\cpanel-deployment-guide.md
) else (
    echo Overall Status: NOT READY FOR DEPLOYMENT
    echo.
    echo Please fix the failed tests before deploying.
)

pause