#!/bin/bash

echo "🧹 Starting SAFE Production Cleanup for Laravel..."

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This doesn't appear to be a Laravel project (artisan not found)"
    exit 1
fi

echo "✅ Laravel project detected"

# Remove documentation folder
if [ -d "docs" ]; then
    echo "📁 Removing docs folder..."
    rm -rf docs/
else
    echo "📁 Docs folder not found, skipping..."
fi

# Remove development files (only if they exist)
echo "🗑️ Removing development files..."
[ -f ".DS_Store" ] && rm -f .DS_Store
[ -f "package-lock.json" ] && rm -f package-lock.json
[ -f "vite.config.js" ] && rm -f vite.config.js
[ -f "postcss.config.js" ] && rm -f postcss.config.js
[ -f "tailwind.config.js" ] && rm -f tailwind.config.js

# Remove Node.js dependencies (only if not using Vite/Tailwind)
if [ -d "node_modules" ] && [ ! -f "vite.config.js" ]; then
    echo "📦 Removing Node.js files..."
    rm -rf node_modules/
    rm -f package.json
else
    echo "📦 Keeping Node.js files (Vite/Tailwind detected)"
fi

# Remove test files (optional - comment out if you want to keep tests)
if [ -d "tests" ]; then
    echo "🧪 Removing test files..."
    rm -rf tests/
else
    echo "🧪 Tests folder not found, skipping..."
fi

# Remove empty scripts folder
if [ -d "scripts" ] && [ -z "$(ls -A scripts)" ]; then
    echo "📜 Removing empty scripts folder..."
    rm -rf scripts/
else
    echo "📜 Scripts folder not empty or not found, skipping..."
fi

# Clean storage logs (keep structure)
echo "📝 Cleaning storage logs..."
find storage/logs -name "*.log" -delete 2>/dev/null || true
find storage/logs -name "*.txt" -delete 2>/dev/null || true

# Clean cache (safe for Laravel)
echo "🗂️ Cleaning Laravel cache..."
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Create .gitkeep files to maintain structure
echo "📁 Creating .gitkeep files..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views
touch storage/logs/.gitkeep
touch storage/framework/cache/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep

# Show space saved
echo "✅ Production cleanup completed!"
echo "📊 Current project size: $(du -sh . | cut -f1)"

echo ""
echo "🚀 Your Laravel application is now ready for production deployment!"
echo ""
echo "💡 Next steps:"
echo "   1. Update .env for production:"
echo "      - APP_ENV=production"
echo "      - APP_DEBUG=false"
echo "      - APP_URL=https://your-domain.com"
echo ""
echo "   2. Set proper file permissions:"
echo "      - chmod 600 .env"
echo "      - chmod -R 755 storage bootstrap/cache"
echo ""
echo "   3. Enable HTTPS and secure cookies"
echo "   4. Setup monitoring and logging"
echo "   5. Test all functionality before go-live"
