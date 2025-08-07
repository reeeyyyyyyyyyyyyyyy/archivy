# 📋 DEPLOYMENT & DEVELOPMENT GUIDELINES
## Sistem Arsip Digital - Instansi Pemerintah

---

## 🏗️ **SYSTEM ARCHITECTURE OVERVIEW**

### **Tech Stack**
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Database**: PostgreSQL (Production), SQLite (Development)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Authentication**: Laravel Breeze + Spatie Laravel Permission
- **File Storage**: Local storage (no file uploads required)
- **Caching**: Redis (recommended) / File cache
- **Queue**: Database queue driver

### **User Roles & Permissions**
```
├── Admin (Super User)
│   ├── Full system access
│   ├── User management
│   ├── System configuration
│   └── Audit logs access
├── Staff (Pegawai TU)
│   ├── Archive management
│   ├── Storage management
│   ├── Reports generation
│   └── Limited user management
└── Intern (Mahasiswa Magang)
    ├── Archive creation
    ├── Basic search
    └── Own archive management
```

---

## 🔐 **SECURITY GUIDELINES**

### **Environment Configuration**
```bash
# .env.production
APP_NAME="Sistem Arsip Digital"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://arsip.domain.go.id

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=arsip_production
DB_USERNAME=arsip_user
DB_PASSWORD=secure_password_here

# Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### **File Permissions (Linux Server)**
```bash
# Critical files
chmod 600 .env
chmod 644 storage/logs/
chmod 755 storage/framework/
chmod 755 bootstrap/cache/

# Application directories
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### **Security Best Practices**
1. **Environment Variables**: Never commit `.env` files
2. **Database**: Use strong passwords, limit connections
3. **Sessions**: Secure cookie settings
4. **CSRF**: Always enabled for forms
5. **XSS Protection**: Input sanitization
6. **SQL Injection**: Use Eloquent ORM only
7. **File Upload**: Disabled (not required)

---

## 🚀 **DEPLOYMENT CHECKLIST**

### **Pre-Deployment**
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_KEY` generated and secure
- [ ] Database migrations tested
- [ ] All routes tested
- [ ] Error pages configured
- [ ] Logging configured
- [ ] Cache configured

### **Server Requirements**
- **OS**: Ubuntu 20.04+ / CentOS 8+
- **PHP**: 8.1+ with extensions
- **PostgreSQL**: 13+
- **Web Server**: Nginx/Apache
- **Storage**: 20GB minimum
- **RAM**: 2GB+ recommended

### **Deployment Steps**
```bash
# 1. Server preparation
sudo apt update && sudo apt upgrade
sudo apt install nginx postgresql php8.1-fpm

# 2. Application deployment
git clone [repository]
cd archivy
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Database setup
php artisan migrate --force
php artisan db:seed --force

# 4. File permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
```

---

## 🔧 **DEVELOPMENT STANDARDS**

### **Code Organization**
```
app/
├── Http/Controllers/
│   ├── Admin/          # Admin-specific controllers
│   ├── Staff/          # Staff-specific controllers
│   └── Intern/         # Intern-specific controllers
├── Models/             # Eloquent models
├── Services/           # Business logic
├── Jobs/              # Background jobs
└── Exports/           # Excel/PDF exports
```

### **Naming Conventions**
- **Controllers**: `ArchiveController`, `StorageManagementController`
- **Models**: `Archive`, `StorageRack`, `User`
- **Routes**: `admin.archives.index`, `staff.storage.create`
- **Views**: `admin.archives.index`, `staff.storage.create`

### **Database Conventions**
- **Tables**: `archives`, `storage_racks`, `storage_boxes`
- **Foreign Keys**: `archive_id`, `rack_id`, `user_id`
- **Timestamps**: `created_at`, `updated_at`
- **Soft Deletes**: `deleted_at` where applicable

---

## 📊 **PERFORMANCE GUIDELINES**

### **Caching Strategy**
```php
// Cache frequently accessed data
Cache::remember('archives_count', 3600, function () {
    return Archive::count();
});

// Cache user permissions
Cache::remember('user_permissions_' . $userId, 1800, function () {
    return $user->getAllPermissions();
});
```

### **Database Optimization**
- **Indexes**: Add indexes on frequently queried columns
- **Eager Loading**: Use `with()` to prevent N+1 queries
- **Pagination**: Always paginate large datasets
- **Query Optimization**: Use database query logs in development

### **Monitoring Setup**
```bash
# Log monitoring
tail -f storage/logs/laravel.log

# Database monitoring
pg_stat_statements (PostgreSQL)

# Application monitoring
Laravel Telescope (development only)
```

---

## 🔄 **INTEGRATION PATTERNS**

### **API Structure (Future)**
```php
// API Routes structure
Route::prefix('api/v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('archives', ArchiveApiController::class);
        Route::apiResource('storage', StorageApiController::class);
    });
});
```

### **External System Integration**
```php
// Service class for external integrations
class ExternalSystemService
{
    public function syncArchive($archive)
    {
        // Future integration logic
    }
}
```

---

## 📝 **API DOCUMENTATION STANDARDS**

### **API Response Format**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "index_number": "000.1/2023/001",
        "description": "Dokumen arsip",
        "status": "Aktif"
    },
    "message": "Archive retrieved successfully",
    "meta": {
        "pagination": {
            "current_page": 1,
            "total": 100
        }
    }
}
```

### **Error Response Format**
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Validation failed",
        "details": {
            "description": ["Description is required"]
        }
    }
}
```

---

## 📱 **PWA IMPLEMENTATION GUIDE**

### **Service Worker Structure**
```javascript
// public/sw.js
const CACHE_NAME = 'arsip-cache-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/offline.html'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});
```

### **Manifest Configuration**
```json
{
    "name": "Sistem Arsip Digital",
    "short_name": "Arsip",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#1f2937",
    "icons": [
        {
            "src": "/icons/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
        }
    ]
}
```

---

## 🔔 **NOTIFICATION SYSTEM ARCHITECTURE**

### **Notification Types**
```php
// Notification classes
class ArchiveStatusChanged extends Notification
class RetentionReminder extends Notification
class StorageFullAlert extends Notification
class UserRegistered extends Notification
```

### **Channels Configuration**
```php
// config/notification.php
'channels' => [
    'database' => [
        'driver' => 'database',
    ],
    'mail' => [
        'driver' => 'smtp',
        'host' => env('MAIL_HOST'),
        'port' => env('MAIL_PORT'),
    ],
    'broadcast' => [
        'driver' => 'pusher',
    ],
],
```

### **Real-time Notifications**
```javascript
// resources/js/app.js
Echo.private(`user.${userId}`)
    .notification((notification) => {
        // Handle real-time notifications
        showNotification(notification);
    });
```

---

## 📋 **MAINTENANCE PROCEDURES**

### **Backup Strategy**
```bash
# Automated backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/arsip"

# Database backup
pg_dump arsip_production > $BACKUP_DIR/db_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/app_$DATE.tar.gz /var/www/arsip

# Clean old backups (keep 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

### **Maintenance Mode**
```bash
# Enable maintenance mode
php artisan down --message="Sistem sedang dalam pemeliharaan" --retry=300

# Disable maintenance mode
php artisan up
```

### **Log Management**
```php
// config/logging.php
'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/arsip.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
],
```

---

## 🚨 **DISASTER RECOVERY PLAN**

### **Recovery Procedures**
1. **Server Down**: Restore from latest backup
2. **Database Corruption**: Restore from database backup
3. **Application Error**: Rollback to previous version
4. **Data Loss**: Restore from automated backup

### **Contact Information**
- **System Admin**: [Contact Info]
- **Database Admin**: [Contact Info]
- **Emergency Contact**: [Contact Info]

---

## 📚 **DOCUMENTATION REQUIREMENTS**

### **Required Documentation**
- [ ] API Documentation (Postman Collection)
- [ ] User Manual (PDF)
- [ ] Admin Guide (PDF)
- [ ] Technical Documentation (Markdown)
- [ ] Deployment Guide (Markdown)
- [ ] Troubleshooting Guide (Markdown)

### **Documentation Standards**
- Use clear, concise language
- Include screenshots for UI features
- Provide step-by-step instructions
- Include error handling procedures
- Regular updates with system changes

---

## ✅ **DEPLOYMENT READINESS CHECKLIST**

### **Security**
- [ ] `.env` file secured
- [ ] Database credentials secure
- [ ] SSL certificate installed
- [ ] File permissions correct
- [ ] Error reporting disabled

### **Performance**
- [ ] Caching configured
- [ ] Database optimized
- [ ] Images optimized
- [ ] CDN configured (if needed)
- [ ] Monitoring setup

### **Functionality**
- [ ] All features tested
- [ ] User roles working
- [ ] Archive management working
- [ ] Storage management working
- [ ] Reports generating

### **Backup & Recovery**
- [ ] Automated backup configured
- [ ] Recovery procedures documented
- [ ] Test restore performed
- [ ] Monitoring alerts configured

---

**📝 Note**: This document should be updated regularly as the system evolves. All team members should follow these guidelines to ensure consistency and security. 
