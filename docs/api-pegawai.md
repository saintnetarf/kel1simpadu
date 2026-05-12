# API Pegawai

Base routes (registered under `api.php`):

- GET /api/pegawai
  - Query params: `search` (optional), `per_page` (optional)
  - Permission: `view-pegawai`
  - Response (200):

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "current_page": 1,
    "data": [
      {"id":1,"employee_number":"P001","name":"Andi Setiawan","email":"andi@...","phone":"0812...","position":"Dosen","is_active":true}
    ],
    "last_page": 1
  }
}
```

- POST /api/pegawai
  - Body (application/json): `employee_number`, `name`, `email` (opt), `phone` (opt), `position` (opt), `is_active` (boolean)
  - Permission: `create-pegawai`
  - Response (201): created resource

- GET /api/pegawai/{id}
  - Permission: `view-pegawai`
  - Response (200): single pegawai object

- PUT /api/pegawai/{id}
  - Body similar to POST
  - Permission: `update-pegawai`

- DELETE /api/pegawai/{id}
  - Permission: `delete-pegawai`

Notes:
- API responses follow `{"success":bool,"message":string,"data":...}` pattern used across the project.
- Use the `search` param to filter by employee number, name or position.

Examples (curl):

List pegawai (first page):

```bash
curl -H "Authorization: Bearer <token>" "https://example.test/api/pegawai?per_page=10"
```

Create pegawai:

```bash
curl -X POST -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"employee_number":"P010","name":"Nama Baru","is_active":true}' \
  "https://example.test/api/pegawai"
```
