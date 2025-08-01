# Archive Management Module – Database Schema

> **Note**: All table and column names follow Laravel default conventions (snake_case, plural). Foreign keys use `unsignedBigInteger` with proper constraints.

## Entity-Relationship Diagram (Textual)
```
categories 1───* classifications *───1 archives
     │                         │
     └─────────────────────────┘  (each archive also belongs to category)

users 1───* archives (creator, updater)
users 1───* audit_logs
roles & permissions ← (Spatie Laravel-Permission std tables)
```

For visual ERD please refer to the `mermaid` diagram below:
```mermaid
erDiagram
    categories ||--o{ classifications : contains
    categories ||--o{ archives : "has"
    classifications ||--o{ archives : "classifies"
    users ||--o{ audit_logs : "creates"
    users ||--o{ archives : "creates/updates"
```

---

## 1. `categories`
| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | BIGINT | PK, auto-increment | |
| code | VARCHAR(50) | UNIQUE | Official JRA Category code |
| name | VARCHAR(255) | | Human readable name |
| description | TEXT | NULLABLE | Longer explanation |
| retention_active | INT | default 0 | Years before archive turns *Inactive* |
| retention_inactive | INT | default 0 | Years before archive turns *Permanent* or *Destroyed* |
| created_at / updated_at | TIMESTAMP | | |

## 2. `classifications`
| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | BIGINT | PK | |
| category_id | BIGINT | FK → categories.id | Mandatory |
| parent_id | BIGINT | FK → self.id NULLABLE | Enables unlimited hierarchy |
| code | VARCHAR(100) | UNIQUE | Dot-separated numeric path e.g. `01.02.03` |
| name | VARCHAR(255) | | Title |
| description | TEXT | NULLABLE | |
| retention_active | INT | default 0 | Overrides category; cached to archives |
| retention_inactive | INT | default 0 | Overrides category; cached to archives |
| created_at / updated_at | TIMESTAMP | | |

**Indexes**
* (`category_id`)
* (`parent_id`)
* (`code` unique)

## 3. `archives`
| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | BIGINT | PK |
| category_id | BIGINT | FK → categories.id |
| classification_id | BIGINT | FK → classifications.id |
| index_number | VARCHAR(50) | NULLABLE, UNIQUE | Manual or generated index/lampiran |
| description | TEXT | | Narrative / uraian |
| kurun_waktu_start | DATE | | Beginning period |
| kurun_waktu_end | DATE | NULLABLE | Ending period |
| jumlah_berkas | INT | | File count |
| retention_active | INT | | Copied from classification at creation |
| retention_inactive | INT | | Copied from classification |
| transition_active_due | DATE | | Calculated: start + retention_active |
| transition_inactive_due | DATE | | transition_active_due + retention_inactive |
| status | ENUM('aktif','inaktif','inaktif_permanen','musnah') | default 'aktif' | Current lifecycle stage |
| created_by | BIGINT | FK → users.id |
| updated_by | BIGINT | FK → users.id NULLABLE |
| created_at / updated_at | TIMESTAMP | |

**Indexes**
* (`classification_id`)
* (`status`, `transition_active_due`)

## 4. `audit_logs`
Using *Spatie Activitylog* default structure (simplified):
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT PK |
| log_name | VARCHAR(255) | Context label |
| description | VARCHAR(255) | Event name (created/updated/deleted) |
| subject_type | VARCHAR(255) | Fully-qualified model class |
| subject_id | BIGINT | PK of subject |
| causer_type | VARCHAR(255) | Usually `App\\Models\\User` |
| causer_id | BIGINT | User id |
| properties | JSON | { old: {}, attributes: {} } |
| created_at | TIMESTAMP |

## 5. Role & Permission Tables
Provided by *Spatie Laravel-Permission*:
* `roles` (id, name, guard_name)
* `permissions` (id, name, guard_name)
* `model_has_roles`, `model_has_permissions`, `role_has_permissions`

---

## Derived & Check Constraints
1. `retention_active >= 0` and `retention_inactive >= 0` (database CHECK).
2. `transition_active_due` = `kurun_waktu_start` + INTERVAL `retention_active` YEAR (enforced in application & DB trigger optional).
3. Composite unique: (`classification_id`, `description`, `kurun_waktu_start`) optional to prevent duplicates.

## Sample Data Walk-through
1. **Category** `02` “Kepegawaian” – retention 2 / 5.
2. **Classification** `02.01` “Surat Masuk” – retention 1 / 4.
3. New **Archive** created 2022-01-10:
   * transition_active_due = 2023-01-10
   * transition_inactive_due = 2027-01-10
   * status initially **Aktif**
4. On 2023-01-11 scheduler marks **Inactive**; on 2027-01-11 marks **Permanent Inactive**.

## Migration Ordering
1. Create categories, classifications, archives.
2. Add users (Laravel default) then roles & permissions tables.
3. Install Activitylog tables.

---
This document defines the authoritative data contract for the Archive Management Module. Any schema changes must be reflected here and in corresponding migrations & seeders. 