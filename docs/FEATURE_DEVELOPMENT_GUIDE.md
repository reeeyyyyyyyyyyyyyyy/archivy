# 🚀 FEATURE DEVELOPMENT GUIDE
## Sistem Arsip Digital - Development Roadmap

---

## 📋 **FEATURE ROADMAP**

### **Phase 1: Core System ✅ COMPLETED**
- [x] User Authentication & Authorization
- [x] Archive Management (CRUD)
- [x] Storage Management System
- [x] Role-based Access Control
- [x] Basic Reporting
- [x] Search & Filtering
- [x] Export Functionality

### **Phase 2: Advanced Features 🔄 IN PROGRESS**
- [ ] **Notification System** (Priority: HIGH)
- [ ] **API Development** (Priority: MEDIUM)
- [ ] **PWA Capabilities** (Priority: LOW)

---

## 🔔 **NOTIFICATION SYSTEM ARCHITECTURE**

### **System Overview**
```
Notification System
├── Database Notifications (Primary)
├── Real-time Notifications (WebSocket)
├── Email Notifications (Future)
└── Push Notifications (Future)
```

### **Notification Types & Triggers**

#### **1. Archive Management Notifications**
```php
// Triggers
- Archive created
- Archive updated
- Archive status changed
- Archive location set
- Archive deleted

// Recipients
- Archive creator
- Admin users
- Staff users (if relevant)
```

#### **2. Storage Management Notifications**
```php
// Triggers
- Storage rack full
- Storage box full
- Storage capacity warning
- New storage location added

// Recipients
- Admin users
- Staff users
- Storage managers
```

#### **3. Retention & Compliance Notifications**
```php
// Triggers
- Archive approaching retention period
- Archive ready for disposal
- Archive status change required
- Compliance deadline approaching

// Recipients
- Archive owners
- Admin users
- Compliance officers
```

#### **4. User Management Notifications**
```php
// Triggers
- New user registered
- User role changed
- User account activated/deactivated
- Password reset requested

// Recipients
- Admin users
- Affected user
```

### **Implementation Plan**

#### **Step 1: Database Structure**
```sql
-- notifications table
CREATE TABLE notifications (
    id UUID PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- notification_types table
CREATE TABLE notification_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **Step 2: Notification Classes**
```php
// app/Notifications/ArchiveCreated.php
class ArchiveCreated extends Notification
{
    use Queueable;

    public function __construct(public Archive $archive)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'archive_id' => $this->archive->id,
            'archive_number' => $this->archive->formatted_index_number,
            'description' => $this->archive->description,
            'creator_name' => $this->archive->createdByUser->name,
            'message' => "Arsip baru telah dibuat: {$this->archive->formatted_index_number}"
        ];
    }
}
```

#### **Step 3: Real-time Implementation**
```php
// config/broadcasting.php
'default' => env('BROADCAST_DRIVER', 'pusher'),

'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ],
    ],
],
```

#### **Step 4: Frontend Integration**
```javascript
// resources/js/notifications.js
class NotificationManager {
    constructor() {
        this.initializeEcho();
        this.setupEventListeners();
    }

    initializeEcho() {
        Echo.private(`user.${userId}`)
            .notification((notification) => {
                this.showNotification(notification);
                this.updateNotificationCount();
            });
    }

    showNotification(notification) {
        // Show toast notification
        Swal.fire({
            title: 'Notifikasi Baru',
            text: notification.data.message,
            icon: 'info',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
}
```

---

## 🔌 **API DEVELOPMENT FRAMEWORK**

### **API Structure Overview**
```
API v1
├── Authentication
│   ├── POST /api/auth/login
│   ├── POST /api/auth/logout
│   └── GET /api/auth/user
├── Archives
│   ├── GET /api/archives
│   ├── POST /api/archives
│   ├── GET /api/archives/{id}
│   ├── PUT /api/archives/{id}
│   └── DELETE /api/archives/{id}
├── Storage
│   ├── GET /api/storage/racks
│   ├── GET /api/storage/boxes
│   └── POST /api/storage/locate
└── Reports
    ├── GET /api/reports/retention
    ├── GET /api/reports/statistics
    └── POST /api/reports/export
```

### **API Authentication Strategy**
```php
// Sanctum Configuration
// config/sanctum.php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),
    'guard' => ['web'],
    'expiration' => null,
    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

### **API Controllers Structure**
```php
// app/Http/Controllers/Api/V1/ArchiveController.php
class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Archive::with(['category', 'classification', 'createdByUser']);
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        // Role-based filtering
        if (auth()->user()->role_type === 'staff') {
            $query->whereIn('created_by', [auth()->id()] + User::role('intern')->pluck('id')->toArray());
        }
        
        return ArchiveResource::collection(
            $query->paginate($request->get('per_page', 15))
        );
    }

    public function store(StoreArchiveRequest $request)
    {
        $archive = DB::transaction(function () use ($request) {
            $archive = Archive::create($request->validated() + [
                'created_by' => auth()->id()
            ]);
            
            // Send notification
            auth()->user()->notify(new ArchiveCreated($archive));
            
            return $archive;
        });
        
        return new ArchiveResource($archive);
    }
}
```

### **API Resources & Transformers**
```php
// app/Http/Resources/ArchiveResource.php
class ArchiveResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'index_number' => $this->formatted_index_number,
            'description' => $this->description,
            'status' => $this->status,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->nama_kategori,
            ],
            'classification' => [
                'id' => $this->classification->id,
                'code' => $this->classification->code,
                'name' => $this->classification->name,
            ],
            'storage' => [
                'rack_number' => $this->rack_number,
                'row_number' => $this->row_number,
                'box_number' => $this->box_number,
                'file_number' => $this->file_number,
            ],
            'created_by' => [
                'id' => $this->createdByUser->id,
                'name' => $this->createdByUser->name,
                'role' => $this->createdByUser->role_type,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
```

### **API Documentation (Postman Collection)**
```json
{
    "info": {
        "name": "Sistem Arsip Digital API",
        "version": "1.0.0",
        "description": "API untuk sistem arsip digital instansi pemerintah"
    },
    "auth": {
        "type": "bearer",
        "bearer": [
            {
                "key": "token",
                "value": "{{auth_token}}",
                "type": "string"
            }
        ]
    },
    "variable": [
        {
            "key": "base_url",
            "value": "https://arsip.domain.go.id/api/v1"
        }
    ]
}
```

---

## 📱 **PWA (PROGRESSIVE WEB APP) IMPLEMENTATION**

### **PWA Features Overview**
```
PWA Capabilities
├── Offline Functionality
├── App-like Experience
├── Push Notifications
├── Background Sync
└── Install Prompt
```

### **Implementation Steps**

#### **Step 1: Web App Manifest**
```json
// public/manifest.json
{
    "name": "Sistem Arsip Digital",
    "short_name": "Arsip",
    "description": "Sistem arsip digital untuk instansi pemerintah",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#1f2937",
    "orientation": "portrait-primary",
    "scope": "/",
    "lang": "id",
    "icons": [
        {
            "src": "/icons/icon-72x72.png",
            "sizes": "72x72",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/icons/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "maskable any"
        },
        {
            "src": "/icons/icon-512x512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "maskable any"
        }
    ]
}
```

#### **Step 2: Service Worker**
```javascript
// public/sw.js
const CACHE_NAME = 'arsip-cache-v1';
const STATIC_CACHE = 'arsip-static-v1';
const DYNAMIC_CACHE = 'arsip-dynamic-v1';

const STATIC_ASSETS = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/offline.html',
    '/manifest.json'
];

// Install event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Fetch event
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }

                return fetch(event.request)
                    .then(response => {
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        const responseToCache = response.clone();
                        caches.open(DYNAMIC_CACHE)
                            .then(cache => {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    })
                    .catch(() => {
                        if (event.request.destination === 'document') {
                            return caches.match('/offline.html');
                        }
                    });
            })
    );
});

// Push notification
self.addEventListener('push', event => {
    const options = {
        body: event.data.text(),
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Lihat',
                icon: '/icons/checkmark.png'
            },
            {
                action: 'close',
                title: 'Tutup',
                icon: '/icons/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('Sistem Arsip Digital', options)
    );
});
```

#### **Step 3: Frontend Integration**
```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1f2937">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <title>Sistem Arsip Digital</title>
</head>
<body>
    <!-- Content -->
    
    <script>
        // PWA Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }

        // Install prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button
            const installButton = document.getElementById('install-button');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', () => {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                        }
                        deferredPrompt = null;
                    });
                });
            }
        });
    </script>
</body>
</html>
```

---

## 🧪 **TESTING STRATEGY**

### **Notification System Testing**
```php
// tests/Feature/NotificationTest.php
class NotificationTest extends TestCase
{
    public function test_archive_created_notification()
    {
        $user = User::factory()->create(['role_type' => 'staff']);
        
        $this->actingAs($user)->post('/staff/archives', [
            'description' => 'Test Archive',
            'category_id' => 1,
            'classification_id' => 1,
        ]);
        
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'type' => ArchiveCreated::class,
        ]);
    }
}
```

### **API Testing**
```php
// tests/Feature/Api/ArchiveApiTest.php
class ArchiveApiTest extends TestCase
{
    public function test_can_get_archives()
    {
        $user = User::factory()->create(['role_type' => 'admin']);
        $archives = Archive::factory()->count(5)->create();
        
        $response = $this->actingAs($user)
            ->getJson('/api/v1/archives');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'index_number',
                        'description',
                        'status'
                    ]
                ]
            ]);
    }
}
```

---

## 📊 **PERFORMANCE MONITORING**

### **Key Metrics to Track**
- **Response Time**: < 2 seconds for API calls
- **Database Queries**: < 10 queries per page
- **Memory Usage**: < 128MB per request
- **Cache Hit Rate**: > 80%
- **Error Rate**: < 1%

### **Monitoring Tools**
```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    // Query monitoring
    DB::listen(function ($query) {
        if ($query->time > 100) {
            Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'time' => $query->time,
                'bindings' => $query->bindings
            ]);
        }
    });
}
```

---

## 🔄 **DEPLOYMENT WORKFLOW**

### **Feature Branch Workflow**
```bash
# 1. Create feature branch
git checkout -b feature/notification-system

# 2. Develop feature
# ... coding ...

# 3. Test locally
php artisan test
php artisan serve

# 4. Commit changes
git add .
git commit -m "feat: implement notification system"

# 5. Push and create PR
git push origin feature/notification-system
# Create Pull Request on GitHub
```

### **Deployment Checklist**
- [ ] All tests passing
- [ ] Code review completed
- [ ] Database migrations tested
- [ ] Environment variables updated
- [ ] Documentation updated
- [ ] Performance tested
- [ ] Security review completed

---

## 📚 **DOCUMENTATION REQUIREMENTS**

### **For Each Feature**
- [ ] **Technical Documentation**: Implementation details
- [ ] **User Guide**: How to use the feature
- [ ] **API Documentation**: Endpoints and responses
- [ ] **Testing Guide**: How to test the feature
- [ ] **Troubleshooting**: Common issues and solutions

### **Documentation Standards**
- Use clear, concise language
- Include code examples
- Provide step-by-step instructions
- Include screenshots for UI features
- Regular updates with changes

---

**🎯 Next Steps:**
1. **Notification System**: Start with database notifications
2. **API Development**: Begin with authentication and basic CRUD
3. **PWA Implementation**: Start with manifest and service worker

**📝 Note**: This guide should be updated as features are implemented and requirements change. 
