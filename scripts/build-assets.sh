#!/bin/bash

# Production Asset Build Script for cPanel Deployment

echo "Starting production asset build..."

# Install production dependencies
echo "Installing production npm dependencies..."
npm ci --production

# Build optimized assets
echo "Building optimized assets..."
npm run build

# Create versioned assets for cache busting
echo "Creating versioned assets..."
timestamp=$(date +%s)

# Rename built assets with timestamp
if [ -f "public/build/assets/app.css" ]; then
    cp "public/build/assets/app.css" "public/build/assets/app-${timestamp}.css"
fi

if [ -f "public/build/assets/app.js" ]; then
    cp "public/build/assets/app.js" "public/build/assets/app-${timestamp}.js"
fi

# Create asset manifest with versions
echo "Creating asset manifest..."
cat > public/build/manifest.json << EOF
{
    "resources/css/app.css": "/build/assets/app-${timestamp}.css",
    "resources/js/app.js": "/build/assets/app-${timestamp}.js"
}
EOF

# Optimize images if any exist
echo "Optimizing images..."
find public/images -name "*.jpg" -o -name "*.png" -o -name "*.gif" | while read file; do
    if command -v optipng &> /dev/null; then
        optipng -o2 "$file" 2>/dev/null || true
    fi
    if command -v jpegoptim &> /dev/null; then
        jpegoptim --max=80 "$file" 2>/dev/null || true
    fi
done

echo "Asset build completed successfully!"
echo "Assets built with timestamp: ${timestamp}"