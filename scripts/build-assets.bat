@echo off
REM Production Asset Build Script for cPanel Deployment (Windows)

echo Starting production asset build...

REM Install production dependencies
echo Installing production npm dependencies...
npm ci --production

REM Build optimized assets
echo Building optimized assets...
npm run build

REM Create versioned assets for cache busting
echo Creating versioned assets...
for /f "delims=" %%i in ('powershell -Command "Get-Date -UFormat %%s"') do set timestamp=%%i

REM Rename built assets with timestamp
if exist "public\build\assets\app.css" (
    copy "public\build\assets\app.css" "public\build\assets\app-%timestamp%.css"
)

if exist "public\build\assets\app.js" (
    copy "public\build\assets\app.js" "public\build\assets\app-%timestamp%.js"
)

REM Create asset manifest with versions
echo Creating asset manifest...
echo { > public\build\manifest.json
echo     "resources/css/app.css": "/build/assets/app-%timestamp%.css", >> public\build\manifest.json
echo     "resources/js/app.js": "/build/assets/app-%timestamp%.js" >> public\build\manifest.json
echo } >> public\build\manifest.json

echo Asset build completed successfully!
echo Assets built with timestamp: %timestamp%