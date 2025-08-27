# 📚 ArsipIn - Sistem Manajemen Arsip Digital

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**ArsipIn** adalah sistem manajemen arsip digital yang dirancang untuk mengelola dokumen dan arsip dengan fitur role-based access control, retensi otomatis, dan integrasi Telegram bot.

## 🚀 Fitur Utama

### 👥 Role-Based Access Control
- **Administrator**: Akses penuh ke semua fitur sistem
- **Staff (Pegawai TU)**: Manajemen arsip dan laporan
- **Intern (Mahasiswa)**: Input dan view arsip terbatas

### 📋 Manajemen Arsip
- ✅ Input, edit, dan hapus arsip
- ✅ Kategorisasi dan klasifikasi dokumen
- ✅ Sistem retensi otomatis (Aktif → Inaktif → Final)
- ✅ Pencarian multi-kriteria (deskripsi, kategori, tahun, kata kunci)
- ✅ Export data ke Excel dan PDF

### 🏢 Manajemen Storage
- ✅ Konfigurasi rak, baris, dan box penyimpanan
- ✅ Penempatan arsip otomatis
- ✅ Label dan barcode generation
- ✅ Operasi bulk (massal)

### 📊 Dashboard & Analytics
- ✅ Dashboard khusus per role
- ✅ Statistik arsip real-time
- ✅ Progress tracking personal
- ✅ Grafik performa

### 🤖 Integrasi Telegram Bot
- ✅ Notifikasi retensi otomatis
- ✅ Pencarian arsip via bot
- ✅ Laporan status storage
- ✅ Alert untuk arsip mendekati retensi

### 🔒 Keamanan
- ✅ Authentication & Authorization
- ✅ CSRF Protection
- ✅ Rate Limiting
- ✅ Input Sanitization
- ✅ Role-based permissions

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 10.x** - PHP Framework
- **MySQL/PostgreSQL** - Database
- **Spatie Laravel-Permission** - Role Management
- **Laravel Sanctum** - API Authentication

### Frontend
- **Tailwind CSS 3.x** - Utility-first CSS Framework
- **Alpine.js** - Lightweight JavaScript Framework
- **Font Awesome** - Icon Library
- **Chart.js** - Data Visualization

### Integrasi
- **Telegram Bot API** - Notifikasi & Pencarian
- **Maatwebsite Laravel-Excel** - Export Excel
- **Barryvdh DomPDF** - Generate PDF
- **Carbon** - Date & Time Manipulation

## 📋 Requirements

### System Requirements
- **PHP**: 8.1 atau lebih tinggi
- **Composer**: 2.0 atau lebih tinggi
- **Node.js**: 16.0 atau lebih tinggi
- **Database**: MySQL 8.0+ atau PostgreSQL 13+

### PHP Extensions
```bash
php-bcmath
php-curl
php-dom
php-fileinfo
php-gd
php-mbstring
php-mysql
php-xml
php-zip
```

## 🚀 Installation

### 1. Clone Repository
```bash
git clone https://github.com/yourusername/arsipin.git
cd arsipin
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (optional)
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
```bash
# Edit .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arsipin
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Configure Telegram Bot (Optional)
```bash
# Edit .env file
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_TEST_CHAT_ID=your_chat_id
```

### 6. Run Migrations & Seeders
```bash
# Run database migrations
php artisan migrate

IMPORT CATEGORY.SQL
IMPORT KLASIFIKASI.SQL

# Seed initial data (Lainnya Category & Clasification)
php artisan db:seed --class=LainnyaCategorySeeder
```

### 7. Setup Storage & Cache
```bash
# Create storage links
php artisan storage:link

# Clear and cache config
php artisan config:clear
php artisan config:cache
```

### 8. Start Development Server
```bash
# Start Laravel development server
php artisan serve

# Or use Laravel Sail (Docker)
./vendor/bin/sail up
```

## 🔧 Configuration

### Telegram Bot Setup
1. Set webhook: `php artisan telegram:set-webhook`
2. Test bot: `php artisan telegram:test`

### File Permissions
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Queue Configuration (Optional)
```bash
# For background jobs
php artisan queue:work
```

## 📁 Project Structure

```
arsipin/
├── app/
│   ├── Console/Commands/          # Artisan Commands
│   ├── Http/Controllers/          # Controllers
│   │   ├── Admin/                # Admin Controllers
│   │   ├── Auth/                 # Authentication Controllers
│   │   ├── Staff/                # Staff Controllers
│   │   └── Intern/               # Intern Controllers
│   ├── Models/                   # Eloquent Models
│   ├── Observers/                # Model Observers
│   ├── Services/                 # Business Logic Services
│   └── Providers/                # Service Providers
├── database/
│   ├── migrations/               # Database Migrations
│   └── seeders/                  # Database Seeders
├── resources/
│   ├── views/                    # Blade Templates
│   │   ├── admin/               # Admin Views
│   │   ├── staff/               # Staff Views
│   │   ├── intern/              # Intern Views
│   │   └── auth/                # Authentication Views
│   └── css/                     # Stylesheets
├── routes/                       # Route Definitions
├── storage/                      # File Storage
└── public/                       # Public Assets
```

## 🔐 Default Users

Setelah menjalankan seeder, sistem akan memiliki:

### Admin User
- **Email**: admin@arsipin.com
- **Password**: password
- **Role**: Administrator

### Roles Available
- **admin**: Akses penuh sistem
- **staff**: Manajemen arsip & laporan
- **intern**: Input & view arsip

## 📱 Telegram Bot Commands

| Command | Description |
|---------|-------------|
| `/start` | Memulai bot dan menampilkan menu utama |
| `/menu` | Menampilkan menu utama |
| `/retention` | Laporan retensi arsip |
| `/storage` | Status storage system |
| `/website` | Link website aplikasi |
| `/stop` | Menghentikan bot (harus start ulang) |

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Configure production database
- [ ] Set proper file permissions
- [ ] Configure web server (Nginx/Apache)
- [ ] Setup SSL certificate
- [ ] Configure backup strategy

### Server Requirements
- **Web Server**: Nginx/Apache
- **PHP**: 8.1+ dengan OPcache
- **Database**: MySQL 8.0+ atau PostgreSQL 13+
- **Memory**: Minimum 512MB RAM
- **Storage**: Minimum 10GB

## 🧪 Testing

```bash
# Run PHPUnit tests
php artisan test

# Run specific test
php artisan test --filter=UserTest

# Run with coverage
php artisan test --coverage
```

## 📊 Performance

### Optimization Tips
- ✅ Enable OPcache
- ✅ Use Redis for caching
- ✅ Optimize database queries
- ✅ Enable compression (gzip)
- ✅ Use CDN for static assets

### Monitoring
- Laravel Telescope (development)
- Laravel Horizon (queue monitoring)
- Database query logging
- Application performance monitoring

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 Changelog

### [1.0.0] - 2025-01-XX
- ✅ Initial release
- ✅ Role-based access control
- ✅ Archive management system
- ✅ Telegram bot integration
- ✅ Storage management
- ✅ Reporting system

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Telegram Bot API](https://core.telegram.org/bots/api)

### Issues & Questions
- 📧 Email: support@arsipin.com
- 🐛 [GitHub Issues](https://github.com/yourusername/arsipin/issues)
- 💬 [Discussions](https://github.com/yourusername/arsipin/discussions)

### Community
- 🌐 Website: [https://arsipin.com](https://arsipin.com)
- 📱 Telegram: [@ArsipInBot](https://t.me/ArsipInBot)
- 📧 Email: info@arsipin.com

---

<div align="center">

**Made with by ArsipIn Team**

[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/yourusername)
[![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/ArsipInBot)

</div>
