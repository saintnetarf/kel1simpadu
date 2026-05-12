# Auth Center (Laravel 13) — Auth Service

Ringkasan: service mikro untuk pusat autentikasi semua microservice Politeknik Negeri Banjarmasin.

ERD (sederhana):
- users (id, name, email, password, ...)
- roles (id, name, display_name, description)
- permissions (id, name, display_name, description)
- role_permissions (id, role_id, permission_id)
- user_roles (id, user_id, role_id)
- service_clients (id, name, slug, url, secret)
- menu_access (id, name, code, path, icon, parent_id, sort_order, is_active, description)
- service_registry (id, name, code, base_url, health_check_url, contact_email, status, description)
- api_tokens (id, user_id, name, token, abilities, last_used_at)
- activity_logs (id, user_id, action, payload, ip, user_agent)
- audit_logs (id, auditable_type, auditable_id, user_id, old_values, new_values, action)
- pegawai (id, employee_number, name, email, phone, position, is_active, timestamps)
- class_participants (id, academic_year_id, study_program_id, class_id, course_id, student_id, pegawai_id, participant_status, notes, timestamps)

Instalasi (singkat):

1. Copy repository ini ke server / development.
2. Buat file `.env` dan set `DB_*` ke MySQL.
3. Install dependencies:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

4. Jalankan dev server:

```bash
php artisan serve
```

5. Dokumentasi API dengan Laravel Scramble tersedia di:

```text
/docs/api
/docs/api.json
```

Route dokumentasi ini aktif otomatis dari package `dedoc/scramble` dan default-nya tersedia untuk environment lokal.

Endpoints (utama):
- POST `/api/login` {email,password}
- POST `/api/logout` (Bearer token)
- POST `/api/refresh-token` (alias login)
- GET `/api/me` (Bearer token)
- POST `/api/validate-token` {token}
- GET `/api/users` (list)
- POST `/api/users` (create)
- PUT `/api/users/{id}` (update)
- DELETE `/api/users/{id}` (delete)
- GET `/api/roles`
- GET `/api/permissions`
- GET `/api/menu-access`
- GET `/api/service-registry`
- GET `/api/pegawai` (list with search)
- POST `/api/pegawai` (create)
- GET `/api/pegawai/{id}` (detail)
- PUT `/api/pegawai/{id}` (update)
- DELETE `/api/pegawai/{id}` (delete)

Format response (contoh sukses login):

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "...",
    "user": { /* user */ },
    "roles": ["super_admin"]
  }
}
```

Postman example (quick):
1. POST `/api/login` body JSON: `{ "email": "admin@poliban.ac.id", "password": "password123" }`
2. Use returned `token` as `Authorization: Bearer {token}` for subsequent calls.

Catatan arsitektur:
- Auth Center mengeluarkan token yang disimpan di tabel `api_tokens`.
- Microservice lain harus memanggil endpoint `POST /api/validate-token` untuk memvalidasi token.
- Middleware `role` dan `permission` memeriksa akses berdasarkan token.

Matrix akses default:
- `super_admin`: full access ke semua master data dan manajemen client.
- `admin_akademik`: read-only ke `users`, `roles`, dan `permissions`.
- `service-clients` dan `api-clients`: hanya `super_admin`.

Permission matrix default:
- `view-users` = lihat daftar user
- `create-users` = buat user
- `update-users` = ubah user
- `delete-users` = hapus user
- `view-roles` = lihat role
- `view-permissions` = lihat permission
- `manage-roles` = CRUD role
- `manage-permissions` = CRUD permission
- `view-pegawai` = lihat pegawai
- `create-pegawai` = buat pegawai
- `update-pegawai` = ubah pegawai
- `delete-pegawai` = hapus pegawai
- `menu-access` dan `service-registry` dikelola penuh oleh `super_admin`

Postman scenarios tambahan:
- `admin_akademik` hanya bisa membaca `users`, `roles`, dan `permissions`.
- `super_admin` bisa CRUD `roles`, `permissions`, `menu-access`, dan `service-registry`.

## Master Data — Pegawai (Karyawan)

Fitur: kelola data pegawai dan relasi dengan peserta kelas untuk keperluan tracking dan manajemen kelas.

### Web Interface
- Halaman: `/master-data/pegawai`
- CRUD: full (super_admin), read-only (admin_akademik)
- Detail pegawai (show): `/master-data/pegawai/{id}` — dapat diakses oleh super_admin dan admin_akademik
- Link ke profil pegawai: tampil di tabel peserta kelas sebagai tautan interaktif
- Permission checks (via PegawaiPolicy):
  - `viewAny` — memerlukan permission `view-pegawai`
  - `view` — memerlukan permission `view-pegawai`
  - `create` — memerlukan permission `create-pegawai`
  - `update` — memerlukan permission `update-pegawai`
  - `delete` — memerlukan permission `delete-pegawai`

### API Resource
- Base: `/api/pegawai`
- Query: `search` (filter employee_number, name, position), `per_page` (default 15)
- Auth: Bearer token (permission-based middleware)
- Response format: `{"success":bool,"message":string,"data":...}`
- Dokumentasi lengkap: [docs/api-pegawai.md](docs/api-pegawai.md)

### Relasi & Seeders
- Pegawai dapat di-assign ke Class Participants (`class_participants.pegawai_id`)
- Seeder dummy: `PegawaiSeeder` — membuat 5 data pegawai default
- Seeder permission: `PegawaiPermissionSeeder` — setup permission untuk super_admin dan admin_akademik
- Seeder assign: `AssignPegawaiToParticipantsSeeder` — menautkan peserta kelas ke pegawai secara round-robin
- Jalankan seeder (sudah terintegrasi di DatabaseSeeder):

```bash
php artisan db:seed
```

### Testing
- Unit test policy: `tests/Unit/Policies/PegawaiPolicyTest.php` (test authorization logic)
- Feature test form: `tests/Feature/PegawaiInClassParticipantFormTest.php` (test UI dropdown)
- Jalankan semua test:

```bash
php artisan test
```

- Jalankan policy test saja:

```bash
php artisan test tests/Unit/Policies/PegawaiPolicyTest.php
```
