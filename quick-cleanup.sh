#!/bin/bash

echo "🧹 Quick Cleanup for Production..."

# Remove docs folder (development documentation)
if [ -d "docs" ]; then
    echo "📁 Removing docs folder..."
    rm -rf docs/
fi

# Remove macOS system file
if [ -f ".DS_Store" ]; then
    echo "🍎 Removing .DS_Store..."
    rm -f .DS_Store
fi

# Remove empty scripts folder
if [ -d "scripts" ] && [ -z "$(ls -A scripts)" ]; then
    echo "📜 Removing empty scripts folder..."
    rm -rf scripts/
fi

# Clean Laravel cache
echo "🗂️ Cleaning Laravel cache..."
php artisan cache:clear 2>/dev/null || true

echo "✅ Quick cleanup completed!"
echo "📊 Current project size: $(du -sh . | cut -f1)"
