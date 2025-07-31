# ARSIPIN - Sistem Arsip Pintar DPMPTSP Provinsi Jawa Timur

Sistem manajemen arsip digital yang sesuai dengan JRA Pergub 1 & 30 Jawa Timur dengan fitur automasi status dan role-based access control.

## 🎯 Features

### Core Features
- ✅ **Manajemen Arsip Lengkap** - CRUD arsip dengan 5 status berbeda
- ✅ **Automasi Status** - Transisi status otomatis berdasarkan retensi
- ✅ **Master Data** - Kategori dan Klasifikasi arsip
- ✅ **Export Excel** - Export data arsip ke Excel
- ✅ **Analytics Dashboard** - Visualisasi data dan statistik
- ✅ **Role-Based Access Control** - 3 level user dengan permission berbeda

### Role-Based System
| Role | Description | Permissions |
|------|-------------|-------------|
| **Admin** | Administrator penuh | Full CRUD, Analytics, Master Data, Bulk Operations |
| **Pegawai TU** | Staff Tata Usaha | CRUD Arsip, Analytics, Export Excel |
| **Mahasiswa Magang** | Intern | CRUD Arsip, Export Excel (no Analytics) |

## 🚀 Quick Start

### Installation
```bash
git clone <repository>
cd archivy
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### Demo Users
| Email | Password | Role |
|-------|----------|------|
| admin@arsipin.id | password | Administrator |
| staff@arsipin.id | password | Pegawai TU |
| intern@arsipin.id | password | Mahasiswa Magang |

### Run Application
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## 🎨 User Interfaces

### Admin Dashboard
- **URL**: `http://127.0.0.1:8001/admin/dashboard`
- **Theme**: Blue/Purple gradient
- **Features**: Full system access, analytics, master data management

### Staff Dashboard (Pegawai TU)
- **URL**: `http://127.0.0.1:8001/staff/dashboard`
- **Theme**: Green/Teal gradient
- **Features**: Archive management, analytics dashboard, export Excel

### Intern Dashboard (Mahasiswa Magang)
- **URL**: `http://127.0.0.1:8001/intern/dashboard`
- **Theme**: Orange/Pink gradient
- **Features**: Basic archive CRUD, export Excel, learning progress

## 📁 Archive Status Flow

```
Aktif → Inaktif → [Permanen/Musnah]
```

Status berubah otomatis berdasarkan:
- **Retensi Aktif**: Tahun sebelum menjadi Inaktif
- **Retensi Inaktif**: Tahun sebelum menjadi Permanen/Musnah
- **Nasib Akhir**: Ditentukan oleh kategori arsip

## 🔐 Security

### Authentication
- Login berbasis email/password
- Session management dengan Laravel Sanctum
- CSRF protection pada semua form

### Authorization  
- Role-based permissions menggunakan Spatie Laravel-Permission
- Route protection dengan middleware
- Policy-based access control

## 🛠 Technical Stack

- **Framework**: Laravel 11
- **Database**: PostgreSQL
- **Frontend**: Tailwind CSS + Alpine.js
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel-Permission
- **PDF Generation**: Barryvdh DomPDF
- **Excel Export**: Maatwebsite Laravel-Excel

## 📊 Analytics Features

### Admin & Staff Analytics
- Status distribution charts
- Monthly archive trends
- Retention alerts
- PDF export reports

### Performance Metrics
- Real-time archive counts
- User contribution tracking
- System health monitoring

## 🔄 Automation

### Daily Status Updates
```bash
# Manual execution
php artisan archive:update-status

# Scheduled (runs daily at 00:30)
php artisan schedule:run
```

### Test Data Generation
```bash
# Create test archive from specific year
php artisan archive:create-test 2020
```

## 📱 Navigation

### Role-Aware Sidebar
- **Dynamic menus** based on user role
- **Color-coded themes** per role type
- **Smart submenu persistence** with localStorage
- **Mobile-responsive** navigation

### URL Structure
```
/admin/*     - Administrator routes
/staff/*     - Pegawai TU routes  
/intern/*    - Mahasiswa routes
/archives/*  - Shared archive routes
/categories/* - Shared master data (read-only for non-admin)
```

## 🎓 Learning Mode (Mahasiswa)

### Progress Tracking
- Daily/weekly contribution counters
- Learning goals and targets
- Performance visualization
- Mentorship features

### Restricted Access
- No analytics dashboard
- No master data management
- Read-only permissions for sensitive areas
- Guided learning interface

## 📄 Documentation

- `docs/archive_feature_overview.md` - Feature overview
- `docs/archive_detail.md` - Technical details
- `docs/archive_database.md` - Database schema
- `docs/SISTEM_ARSIP_FINAL.md` - Final system documentation

## 🎯 Development Roadmap

### Phase 1: Core System ✅
- [x] RBAC implementation
- [x] Role-specific dashboards
- [x] Navigation system
- [x] Permission management

### Phase 2: Enhancements 🔄
- [ ] Mobile responsiveness
- [ ] Export Excel per role
- [ ] Advanced search features
- [ ] Bulk operations for staff

### Phase 3: Advanced Features 📋
- [ ] Document management
- [ ] Notification system
- [ ] API development
- [ ] PWA capabilities

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/new-feature`)
3. Commit changes (`git commit -am 'Add new feature'`)
4. Push to branch (`git push origin feature/new-feature`)
5. Create Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🏢 About

**ARSIPIN** dikembangkan untuk DPMPTSP Provinsi Jawa Timur sebagai solusi digitalisasi manajemen arsip yang sesuai dengan peraturan JRA Pergub 1 & 30.

---

**🎉 Role-Based System is now LIVE and ready for production use!**
