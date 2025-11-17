@echo off
REM cPanel File Permissions Setup Script (Windows)

echo Setting up cPanel file permissions...

REM Set directory permissions
echo Setting directory permissions...
for /d %%i in (*) do (
    echo Processing directory: %%i
    attrib +R "%%i" /S /D
)

REM Set file permissions
echo Setting file permissions...
for %%i in (*) do (
    if not "%%i"=="set-permissions.bat" (
        echo Processing file: %%i
        attrib +R "%%i"
    )
)

REM Special permissions for storage directory
echo Setting storage directory permissions...
if exist "storage" (
    attrib +R "storage\*" /S /D
)

REM Special permissions for public directory
echo Setting public directory permissions...
if exist "public" (
    attrib +R "public\*" /S /D
)

REM Make artisan executable (Unix-style)
echo Setting artisan permissions...
if exist "artisan" (
    echo Artisan file found - permissions will be set on server
)

echo File permissions setup completed!
echo NOTE: Full permissions will be applied after FTP upload to cPanel