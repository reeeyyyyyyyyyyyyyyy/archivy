# ✅ DEPLOYMENT CHECKLIST
## Sistem Arsip Digital - Government Institution

---

## 🔒 **SECURITY CHECKLIST**

### **Environment Configuration**
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_KEY` generated and secure
- [ ] `.env` file permissions set to 600
- [ ] Database credentials secure and strong
- [ ] SSL certificate installed and configured
- [ ] Session security configured
- [ ] CSRF protection enabled

### **File Permissions (Linux)**
```bash
chmod 600 .env
chmod 644 storage/logs/
chmod 755 storage/framework/
chmod 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

---

## 🚀 **DEPLOYMENT STEPS**

### **1. Server Preparation**
- [ ] Update system packages
- [ ] Install required software (PHP 8.1+, PostgreSQL 13+, Nginx)
- [ ] Configure firewall
- [ ] Set up SSL certificate

### **2. Application Deployment**
```bash
# Clone repository
git clone [repository-url]
cd archivy

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set file permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
```

### **3. Database Setup**
```bash
# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Create admin user
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@domain.go.id', 'password' => Hash::make('password'), 'role_type' => 'admin']);
```

### **4. Web Server Configuration**
```nginx
# Nginx configuration
server {
    listen 443 ssl;
    server_name arsip.domain.go.id;
    
    root /var/www/archivy/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📊 **PERFORMANCE OPTIMIZATION**

### **Caching Configuration**
- [ ] Redis installed and configured
- [ ] Cache driver set to redis
- [ ] Session driver set to redis
- [ ] Queue driver set to redis

### **Database Optimization**
- [ ] PostgreSQL optimized
- [ ] Indexes created on frequently queried columns
- [ ] Connection pooling configured
- [ ] Query logging enabled for monitoring

### **Application Optimization**
- [ ] OPcache enabled
- [ ] Composer autoloader optimized
- [ ] Laravel configuration cached
- [ ] Route and view caching enabled

---

## 🔄 **BACKUP STRATEGY**

### **Automated Backup Script**
```bash
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

### **Backup Schedule**
- [ ] Daily database backup
- [ ] Weekly application backup
- [ ] Monthly full system backup
- [ ] Backup retention policy (30 days)

---

## 📋 **MONITORING SETUP**

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

### **Monitoring Tools**
- [ ] Log rotation configured
- [ ] Error monitoring setup
- [ ] Performance monitoring enabled
- [ ] Alert system configured

---

## 🛠️ **MAINTENANCE PROCEDURES**

### **Maintenance Mode**
```bash
# Enable maintenance mode
php artisan down --message="Sistem sedang dalam pemeliharaan" --retry=300

# Disable maintenance mode
php artisan up
```

### **Update Procedures**
```bash
# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🚨 **DISASTER RECOVERY**

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

## ✅ **FINAL VERIFICATION**

### **Functionality Tests**
- [ ] User authentication working
- [ ] Archive management working
- [ ] Storage management working
- [ ] Reports generating correctly
- [ ] Export functionality working
- [ ] Search and filtering working

### **Performance Tests**
- [ ] Response time < 2 seconds
- [ ] Database queries optimized
- [ ] Memory usage within limits
- [ ] Cache working correctly

### **Security Tests**
- [ ] SSL certificate valid
- [ ] File permissions correct
- [ ] Environment variables secure
- [ ] Error reporting disabled

---

## 📚 **DOCUMENTATION DELIVERABLES**

### **Required Files**
- [ ] `.env.production` template
- [ ] Nginx configuration
- [ ] Backup scripts
- [ ] Monitoring setup guide
- [ ] User manual
- [ ] Admin guide
- [ ] Troubleshooting guide

### **Documentation Standards**
- Clear, concise language
- Step-by-step instructions
- Screenshots for UI features
- Error handling procedures
- Regular updates required

---

**📝 Note**: This checklist should be completed before going live. All items must be checked off to ensure a secure and stable deployment. 
