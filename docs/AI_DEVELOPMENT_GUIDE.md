# 🤖 AI DEVELOPMENT GUIDE
## Sistem Arsip Digital - Quick Reference for AI Assistants

---

## 📋 **SYSTEM OVERVIEW**

### **Project Type**: Government Archive Management System
### **Framework**: Laravel 10.x (PHP 8.1+)
### **Database**: PostgreSQL (Production), SQLite (Development)
### **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
### **Authentication**: Laravel Breeze + Spatie Laravel Permission

---

## 🏗️ **ARCHITECTURE PATTERNS**

### **User Roles & Permissions**
```php
// Three main roles with hierarchical permissions
Admin (Super User)     → Full system access
Staff (Pegawai TU)    → Archive + Storage management
Intern (Mahasiswa)    → Basic archive operations
```

### **File Structure**
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

### **Database Schema**
```sql
-- Core tables
archives              # Archive records
categories            # Archive categories
classifications       # Archive classifications
storage_racks         # Storage racks
storage_boxes         # Storage boxes
storage_rows          # Storage rows
users                 # User accounts
permissions           # System permissions
roles                 # User roles
```

---

## 🔧 **DEVELOPMENT PATTERNS**

### **1. Controller Organization**
```php
// Role-based controllers
Admin/ArchiveController.php      # Admin archive management
Staff/ArchiveController.php      # Staff archive management
Intern/ArchiveController.php     # Intern archive management

// Shared controllers
ArchiveController.php            # Common archive logic
StorageManagementController.php  # Storage management
BulkOperationController.php     # Bulk operations
```

### **2. Model Relationships**
```php
// Archive Model
class Archive extends Model
{
    public function category(): BelongsTo
    public function classification(): BelongsTo
    public function createdByUser(): BelongsTo
    public function storageBox(): BelongsTo
    
    // Accessors
    public function getFormattedIndexNumberAttribute()
    public static function getNextFileNumber($boxNumber)
}
```

### **3. Route Organization**
```php
// Role-based route groups
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('archives', Admin\ArchiveController::class);
    Route::resource('storage-management', StorageManagementController::class);
});

Route::prefix('staff')->middleware(['auth', 'role:staff'])->group(function () {
    Route::resource('archives', Staff\ArchiveController::class);
    Route::resource('storage-management', StorageManagementController::class);
});
```

---

## 🎨 **FRONTEND PATTERNS**

### **1. Theme Colors by Role**
```css
/* Admin Theme */
.admin-theme { @apply bg-blue-600 text-white; }
.admin-button { @apply bg-blue-600 hover:bg-blue-700; }

/* Staff Theme */
.staff-theme { @apply bg-teal-600 text-white; }
.staff-button { @apply bg-teal-600 hover:bg-teal-700; }

/* Intern Theme */
.intern-theme { @apply bg-orange-600 text-white; }
.intern-button { @apply bg-orange-600 hover:bg-orange-700; }
```

### **2. JavaScript Patterns**
```javascript
// SweetAlert2 for notifications
Swal.fire({
    title: 'Success',
    text: 'Operation completed successfully',
    icon: 'success',
    confirmButtonText: 'OK'
});

// AJAX with error handling
fetch('/api/endpoint')
    .then(response => response.json())
    .then(data => {
        // Handle success
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Something went wrong', 'error');
    });
```

### **3. Form Validation**
```php
// Request validation
class StoreArchiveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'classification_id' => 'required|exists:classifications,id',
            'status' => 'required|in:Aktif,Inaktif,Dinilai Kembali',
        ];
    }
}
```

---

## 🔐 **SECURITY PATTERNS**

### **1. Role-based Access Control**
```php
// Middleware
class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->user()->hasRole($role)) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }
        return $next($request);
    }
}

// Controller level
if (auth()->user()->role_type !== 'admin' && $archive->created_by !== auth()->id()) {
    return redirect()->back()->with('error', 'Tidak memiliki izin');
}
```

### **2. Data Filtering**
```php
// Role-based data filtering
if (auth()->user()->role_type === 'staff') {
    $query->whereIn('created_by', [auth()->id()] + User::role('intern')->pluck('id')->toArray());
}
```

---

## 📊 **BUSINESS LOGIC PATTERNS**

### **1. Archive Number Generation**
```php
// Accessor for formatted archive number
public function getFormattedIndexNumberAttribute()
{
    if ($this->status == 'Dinilai Kembali') {
        return $this->index_number;
    }
    if ($this->classification && $this->classification->code == 'LAINNYA') {
        return $this->index_number; // Manual input
    }
    if ($this->classification && $this->kurun_waktu_start) {
        return $this->classification->code . '/' . $this->index_number . '/' . $this->kurun_waktu_start->format('Y');
    }
    return $this->index_number;
}
```

### **2. Storage Management**
```php
// Box status calculation
public function getBoxStatusAttribute()
{
    $capacity = $this->capacity;
    $count = $this->archive_count;
    
    if ($count == 0) return 'Kosong';
    if ($count < $capacity / 2) return 'Tersedia';
    if ($count < $capacity) return 'Sebagian';
    return 'Penuh';
}
```

### **3. File Number Generation**
```php
// Gap-filling file number logic
public static function getNextFileNumber($boxNumber)
{
    $existingFileNumbers = static::where('box_number', $boxNumber)
        ->pluck('file_number')->sort()->values();
    
    if ($existingFileNumbers->isEmpty()) return 1;
    
    $expectedFileNumber = 1;
    foreach ($existingFileNumbers as $existingFileNumber) {
        if ($existingFileNumber > $expectedFileNumber) {
            return $expectedFileNumber;
        }
        $expectedFileNumber = $existingFileNumber + 1;
    }
    return $existingFileNumbers->max() + 1;
}
```

---

## 🧪 **TESTING PATTERNS**

### **1. Feature Tests**
```php
class ArchiveTest extends TestCase
{
    public function test_staff_can_create_archive()
    {
        $user = User::factory()->create(['role_type' => 'staff']);
        
        $response = $this->actingAs($user)
            ->post('/staff/archives', [
                'description' => 'Test Archive',
                'category_id' => 1,
                'classification_id' => 1,
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('archives', [
            'description' => 'Test Archive',
            'created_by' => $user->id
        ]);
    }
}
```

### **2. Database Factories**
```php
class ArchiveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => fake()->sentence(),
            'index_number' => fake()->unique()->numberBetween(1, 9999),
            'status' => fake()->randomElement(['Aktif', 'Inaktif', 'Dinilai Kembali']),
            'category_id' => Category::factory(),
            'classification_id' => Classification::factory(),
            'created_by' => User::factory(),
        ];
    }
}
```

---

## 🚀 **DEPLOYMENT PATTERNS**

### **1. Environment Configuration**
```bash
# Production settings
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### **2. File Permissions**
```bash
chmod 600 .env
chmod 755 storage/
chown -R www-data:www-data storage/
```

### **3. Cache Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔄 **COMMON ISSUES & SOLUTIONS**

### **1. Route Parameter Errors**
```php
// ❌ Wrong
route('admin.storage-management.show', $rack)

// ✅ Correct
route('admin.storage-management.show', $rack->id)
```

### **2. JavaScript Array vs Collection**
```php
// ❌ JavaScript can't handle Laravel Collections
$racks = StorageRack::all();

// ✅ Convert to array for JavaScript
$racksArray = array_values($racks->toArray());
```

### **3. Form Validation Errors**
```php
// ❌ Missing input field
<div>{{ $file_number }}</div>

// ✅ Hidden input for form submission
<input type="hidden" name="file_number" value="{{ $file_number }}">
<div>{{ $file_number }}</div>
```

---

## 📚 **DOCUMENTATION PATTERNS**

### **1. Code Comments**
```php
/**
 * Get formatted archive number based on status and classification
 * 
 * @return string
 * 
 * Rules:
 * - Dinilai Kembali: index_number only
 * - LAINNYA: index_number only (manual input)
 * - Others: code/index_number/year
 */
public function getFormattedIndexNumberAttribute()
```

### **2. Commit Messages**
```bash
# Format: type(scope): description
feat(archive): add automatic file number generation
fix(storage): resolve box status calculation
docs(api): update endpoint documentation
```

---

## 🎯 **DEVELOPMENT WORKFLOW**

### **1. Feature Development**
1. Create feature branch: `git checkout -b feature/notification-system`
2. Implement feature with tests
3. Test locally: `php artisan test && php artisan serve`
4. Commit changes: `git commit -m "feat: implement notification system"`
5. Push and create PR

### **2. Bug Fixes**
1. Identify issue in specific role/feature
2. Check related files (controller, model, view, route)
3. Implement fix with proper error handling
4. Test across all roles
5. Update documentation if needed

### **3. Security Considerations**
- Always validate user permissions
- Sanitize all inputs
- Use prepared statements (Eloquent ORM)
- Implement proper error handling
- Never expose sensitive data in logs

---

## 📋 **QUICK REFERENCE**

### **Key Files**
- `app/Models/Archive.php` - Core archive logic
- `app/Http/Controllers/` - Role-based controllers
- `routes/web.php` - Route definitions
- `resources/views/` - Blade templates
- `database/migrations/` - Database schema

### **Key Commands**
```bash
php artisan migrate          # Run migrations
php artisan db:seed         # Seed database
php artisan test            # Run tests
php artisan route:list      # List all routes
php artisan tinker          # Interactive shell
```

### **Key URLs**
- Admin: `/admin/*`
- Staff: `/staff/*`
- Intern: `/intern/*`
- API: `/api/v1/*`

---

**📝 Note**: This guide should be updated as the system evolves. Follow these patterns to maintain consistency and security across the application. 
