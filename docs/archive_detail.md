# Archive Management Module – Detailed Rules & Automation

## 1. Retention & Status Automation

| Status | Condition | Next Status | Trigger |
|--------|-----------|-------------|---------|
| **Active** | Created date < **transition_active_due** | Inactive | Daily job (`UpdateArchiveStatusJob`) when `today ≥ transition_active_due` |
| **Inactive** | Created date < **transition_inactive_due** | Permanent Inactive *or* Destroyed | Daily job when `today ≥ transition_inactive_due` |
| **Permanent Inactive** | — | Destroyed (manual policy) | Optional manual bulk action after appraisal |
| **Destroyed** | Final | — | No further transitions |

Algorithm (simplified):
```php
public function handle() {
    // 1. Promote Active → Inactive
    Archive::active()
        ->whereDate('transition_active_due', '<=', today())
        ->update(['status' => 'inaktif']);

    // 2. Promote Inactive → Permanent / Destroyed
    Archive::inaktif()
        ->whereDate('transition_inactive_due', '<=', today())
        ->each(function ($archive) {
            $archive->status = $archive->retention_inactive === 0
                ? 'musnah'
                : 'inaktif_permanen';
            $archive->save();
        });
}
```

*Job scheduling* is configured in `app/Console/Kernel.php`:
```php
$schedule->job(new UpdateArchiveStatusJob)->dailyAt('00:30');
```

## 2. Audit Logging Strategy
* **Package**: [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog)
* **Events Tracked**: `created`, `updated`, `deleted` for `Category`, `Classification`, `Archive`.
* **What is Logged**: complete *before* & *after* JSON snapshot in `properties` column.
* **Security**: Only Admin role can view logs via CLI; not exposed in UI.

Example JSON:
```json
{
  "old": {
    "status": "active",
    "updated_at": "2024-01-08 21:00:01"
  },
  "attributes": {
    "status": "inaktif",
    "updated_at": "2024-01-09 00:30:05"
  }
}
```

## 3. Role & Permission Matrix
| Permission | Admin | Staff | Intern |
|------------|-------|-------|--------|
| view archives | ✔ | ✔ | ✔ |
| create archives | ✔ | ✔ | ✖ |
| edit archives | ✔ | ✔ | ✖ |
| delete archives | ✔ | ✖ | ✖ |
| export archives | ✔ | ✔ | ✔ |
| manage categories | ✔ | ✖ | ✖ |
| manage classifications | ✔ | ✖ | ✖ |
| manage roles | ✔ | ✖ | ✖ |

Permissions are seeded in `DatabaseSeeder` and attached to roles using Spatie helpers.

## 4. Select2 Dependent Dropdown Behaviour
1. **Category First**
   * User selects Category.
   * AJAX endpoint `/classifications?category_id=...` returns filtered list.
   * Classification dropdown re-populates.
2. **Classification First**
   * User searches Classification (global list, grouped by Category).
   * Upon selection, front-end sets hidden Category field automatically.
3. On Classification selection the form auto-fills:
   ```js
   $('#classification').on('select2:select', e => {
       const data = e.params.data;
       $('#retention_active').val(data.retention_active);
       $('#retention_inactive').val(data.retention_inactive);
   });
   ```

## 5. Excel Export Rules
* Library: **Maatwebsite/Laravel-Excel**.
* Export button takes current DataTable query (including filters, search term, pagination) and streams as `.xlsx`.
* Sheet columns: `Index Number`, `Category`, `Classification`, `Description`, `Kurun Waktu`, `Jumlah Berkas`, `Status`, `Retention Active`, `Retention Inactive`.
* For archive lists grouped by year: server receives GET `?year=YYYY` and applies `whereYear(kurun_waktu_start, YYYY)`.

## 6. Data Import (Future Work)
* Bulk import of Category & Classification via CSV planned; each row must contain `code`, `name`, `retention_active`, `retention_inactive`, `category_code` (for classifications).
* On import duplicates are skipped unless `--force` flag provided.

## 7. Security & Validation
* All form inputs validated using *FormRequest* classes.
* Soft deletes not used; destroyed archives remain for audit until database archival.
* CSRF tokens active on all POST/PUT/DELETE.
* File uploads disabled (no physical document storage).

## 8. Performance Considerations
* Pagination default 25 rows; DataTables server-side to reduce payload.
* Composite index `(status, transition_active_due)` accelerates scheduler queries.
* Cache Category & Classification lists in Redis for dropdown population.

## 9. Deployment Checklist
1. Run composer / npm install.
2. Execute migrations & seeders.
3. Schedule cron: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`.
4. Queue worker optional (jobs are synchronous for now).
5. Verify time-zone settings on server to prevent premature transitions.

---
This document captures operational rules and tech notes crucial for maintaining and extending the Archive Management Module. 