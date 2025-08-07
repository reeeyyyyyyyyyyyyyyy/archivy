# API Documentation - Archive Management System

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
All API endpoints (except login) require authentication using Bearer token.

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

## Endpoints

### 1. Authentication

#### Login
```http
POST /auth/login-test
```

**Request Body:**
```json
{
    "email": "admin@arsipin.id",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Administrator",
            "email": "admin@arsipin.id",
            "role": "admin"
        },
        "token": "1|9bhDqBBRy798RKWnxZ6zUMRKse1i1OnvCegLRwmC21c8aa09"
    }
}
```

#### Logout
```http
POST /logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

### 2. Archives

#### Get All Archives
```http
GET /archives
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 215,
                "index_number": "100.1/024/ABC/2010",
                "description": "TESTING TELEGRAM",
                "status": "Permanen",
                "category": {
                    "id": 2,
                    "nama_kategori": "Keuangan dan Aset"
                },
                "classification": {
                    "id": 3,
                    "nama_klasifikasi": "Laporan Keuangan"
                },
                "created_by_user": {
                    "id": 1,
                    "name": "Administrator",
                    "role_type": "admin"
                }
            }
        ],
        "total": 167,
        "per_page": 10
    }
}
```

#### Get Archive by ID
```http
GET /archives/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 215,
        "index_number": "100.1/024/ABC/2010",
        "description": "TESTING TELEGRAM",
        "status": "Permanen",
        "category": {
            "id": 2,
            "nama_kategori": "Keuangan dan Aset"
        },
        "classification": {
            "id": 3,
            "nama_klasifikasi": "Laporan Keuangan"
        },
        "created_by_user": {
            "id": 1,
            "name": "Administrator",
            "role_type": "admin"
        }
    }
}
```

#### Get Archives by Status
```http
GET /archives/status/{status}
```

**Status Options:**
- `Aktif`
- `Inaktif`
- `Permanen`
- `Musnah`
- `Dinilai Kembali`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 212,
                "index_number": "TEST-EDIT-1754452633",
                "description": "Test archive for edit location",
                "status": "Aktif",
                "category": {
                    "id": 1,
                    "nama_kategori": "Ketatausahaan dan Kerumahtanggaan"
                },
                "classification": {
                    "id": 1,
                    "nama_klasifikasi": "Surat Masuk"
                }
            }
        ],
        "total": 54,
        "per_page": 10
    }
}
```

### 3. Storage Management

#### Get All Racks
```http
GET /storage/racks
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Rak 1",
            "description": "Rak utama untuk arsip aktif",
            "total_rows": 7,
            "total_boxes": 28,
            "capacity_per_box": 50,
            "status": "active",
            "boxes": [
                {
                    "id": 5,
                    "box_number": 5,
                    "archive_count": 0,
                    "capacity": 50,
                    "status": "partially_full"
                }
            ],
            "rows": [
                {
                    "id": 1,
                    "row_number": 1,
                    "total_boxes": 4,
                    "available_boxes": 4,
                    "status": "available"
                }
            ]
        }
    ]
}
```

#### Get Rack by ID
```http
GET /storage/racks/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Rak 1",
        "description": "Rak utama untuk arsip aktif",
        "boxes": [...],
        "rows": [...]
    }
}
```

#### Get All Boxes
```http
GET /storage/boxes
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 5,
            "rack_id": 1,
            "row_id": 2,
            "box_number": 5,
            "archive_count": 0,
            "capacity": 50,
            "status": "partially_full",
            "rack": {
                "id": 1,
                "name": "Rak 1"
            },
            "row": {
                "id": 2,
                "row_number": 2
            }
        }
    ]
}
```

#### Get Box by ID
```http
GET /storage/boxes/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "box_number": 5,
        "capacity": 50,
        "archive_count": 0,
        "status": "partially_full",
        "rack": {...},
        "row": {...},
        "archives": [...]
    }
}
```

### 4. Reports

#### Summary Report
```http
GET /reports/summary
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "archives": {
            "total": 167,
            "aktif": 54,
            "inaktif": 60,
            "permanen": 20,
            "musnah": 26
        },
        "storage": {
            "total_racks": 9,
            "total_boxes": 180,
            "full_boxes": 0,
            "available_boxes": 0
        }
    }
}
```

#### Retention Report
```http
GET /reports/retention
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 212,
            "index_number": "TEST-EDIT-1754452633",
            "description": "Test archive for edit location",
            "category": "Ketatausahaan dan Kerumahtanggaan",
            "classification": "Surat Masuk",
            "retention_period": 2,
            "retention_date": "2027-08-05",
            "days_until_retention": 730,
            "is_overdue": false
        }
    ]
}
```

#### Storage Utilization Report
```http
GET /reports/storage-utilization
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Rak 1",
            "total_capacity": 1400,
            "total_used": 40,
            "utilization_percentage": 2.86,
            "boxes": [
                {
                    "id": 5,
                    "box_number": 5,
                    "capacity": 50,
                    "archive_count": 0,
                    "status": "partially_full"
                }
            ]
        }
    ]
}
```

## Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Archive not found"
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

## Role-Based Access Control

### Admin
- Full access to all endpoints
- Can view all archives regardless of creator

### Staff
- Can view archives created by themselves and interns
- Limited access to certain reports

### Intern
- Can only view archives created by themselves
- Limited access to reports

## Pagination

Most list endpoints support pagination with the following parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10)

**Response includes:**
- `current_page`: Current page number
- `last_page`: Last page number
- `per_page`: Items per page
- `total`: Total number of items
- `from`: Starting item number
- `to`: Ending item number
- `links`: Navigation links

## Testing

### Using cURL

#### Login
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login-test \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@arsipin.id","password":"password"}'
```

#### Get Archives (with token)
```bash
curl -X GET http://127.0.0.1:8000/api/archives \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

#### Logout
```bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

### Using Postman

1. **Login Request:**
   - Method: `POST`
   - URL: `http://127.0.0.1:8000/api/auth/login-test`
   - Headers: `Content-Type: application/json`
   - Body: `{"email":"admin@arsipin.id","password":"password"}`

2. **Authenticated Requests:**
   - Add header: `Authorization: Bearer {token}`
   - Replace `{token}` with the token from login response

## Notes

- All timestamps are in ISO 8601 format
- Archive status transitions are automatic based on retention periods
- Storage utilization is calculated in real-time
- Token expires when user logs out or token is manually revoked
- API supports CORS for frontend integration 
