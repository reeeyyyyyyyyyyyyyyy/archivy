#!/bin/bash

echo "🧹 Starting Production Cleanup..."

# Remove documentation folder
echo "📁 Removing docs folder..."
rm -rf docs/

# Remove development files
echo "🗑️ Removing development files..."
rm -f .DS_Store
rm -f package-lock.json
rm -f vite.config.js
rm -f postcss.config.js
rm -f tailwind.config.js

# Remove Node.js dependencies (if not using)
echo "📦 Removing Node.js files..."
rm -rf node_modules/
rm -f package.json

# Remove test files (optional)
echo "🧪 Removing test files..."
rm -rf tests/

# Remove empty scripts folder
echo "📜 Removing empty scripts folder..."
rm -rf scripts/

# Remove IDE files
echo "💻 Removing IDE files..."
rm -f .editorconfig
rm -f .gitattributes

# Clean storage logs (keep structure)
echo "📝 Cleaning storage logs..."
find storage/logs -name "*.log" -delete
find storage/logs -name "*.txt" -delete

# Clean cache
echo "🗂️ Cleaning cache..."
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# Create .gitkeep files to maintain structure
echo "📁 Creating .gitkeep files..."
touch storage/logs/.gitkeep
touch storage/framework/cache/.gitkeep
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep

echo "✅ Production cleanup completed!"
echo "📊 Space saved: $(du -sh . | cut -f1)"
echo ""
echo "🚀 Your application is now ready for production deployment!"
echo "💡 Remember to:"
echo "   - Update .env for production"
echo "   - Set proper file permissions"
echo "   - Enable HTTPS"
echo "   - Setup monitoring"
