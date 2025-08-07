<?php

/**
 * Script untuk menghapus commands development sebelum deploy
 * Jalankan: php scripts/cleanup-development-commands.php
 */

echo "🧹 Cleaning up development commands...\n";

$commandsToRemove = [
    'app/Console/Commands/TelegramSearchCommand.php',
    'app/Console/Commands/TelegramWebhookCommand.php',
    'app/Console/Commands/TestTelegramSearchCommand.php',
    'app/Console/Commands/SetupTelegramWebhookCommand.php',
    'app/Console/Commands/TestTelegramWebhookCommand.php'
];

$removedCount = 0;

foreach ($commandsToRemove as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✅ Removed: {$file}\n";
        $removedCount++;
    } else {
        echo "⚠️  File not found: {$file}\n";
    }
}

echo "\n📊 Summary:\n";
echo "Removed {$removedCount} development command files\n";
echo "Commands that will remain:\n";
echo "- php artisan telegram:test\n";
echo "- php artisan telegram:retention-alerts\n";
echo "- php artisan telegram:maintenance-notification\n";
echo "- php artisan telegram:test-status-transition\n";

echo "\n🎯 Next steps:\n";
echo "1. Commit the changes\n";
echo "2. Deploy to production\n";
echo "3. Setup webhook URL in production\n";
echo "4. Test bot functionality\n";

echo "\n✅ Cleanup completed!\n";
