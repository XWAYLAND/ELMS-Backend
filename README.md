<div align="center">

# 📚 E-Library Management System — Backend

<p>
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PostgreSQL-15+-336791?style=for-the-badge&logo=postgresql&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Sanctum-4.x-FF6D00?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/Flutter-Ready-02569B?style=for-the-badge&logo=flutter&logoColor=white"/>
</p>

**REST API backend untuk sistem peminjaman buku sekolah secara online**  
Dibangun dengan Laravel 12 + PostgreSQL, dikonsumsi oleh aplikasi Flutter mobile.

</div>

---

## 📖 Tentang Proyek

E-Library Management System adalah sistem manajemen perpustakaan digital yang memungkinkan siswa meminjam buku secara online. Sistem ini terdiri dari:

- 🖥️ **Backend** (repo ini): REST API Laravel 12 + PostgreSQL
- 📱 **Frontend**: Aplikasi Flutter (repo terpisah)

### Fitur Utama
- ✅ **Manajemen Katalog Buku** — CRUD buku beserta jenis, penulis, dan penerbit
- ✅ **Manajemen Anggota** — Pendaftaran dan pengelolaan data siswa
- ✅ **Sistem Peminjaman** — Transaksi peminjaman dengan tracking status real-time
- ✅ **Pencarian & Filter** — Cari buku berdasarkan judul, penulis, penerbit, jenis, bahasa
- ✅ **Notifikasi Jatuh Tempo** — Endpoint untuk trigger notifikasi push ke Flutter
- ✅ **Autentikasi Pegawai** — Token-based auth via Laravel Sanctum

---

## 🏗️ Arsitektur & ERD

```
JENIS ──────┐
PENULIS ────┼──▶ BUKU ◀── PEMINJAMAN ◀── ANGGOTA
PENERBIT ───┘                  │
                               └──────────── PEGAWAI
```

### Entitas Database

| Tabel | Primary Key | Deskripsi |
|---|---|---|
| `jenis` | `id_jenis` (string) | Jenis/genre buku |
| `penulis` | `id_penulis` (string) | Data penulis |
| `penerbit` | `id_penerbit` (string) | Data penerbit |
| `buku` | `isbn` (string) | Katalog buku |
| `anggota` | `nis` (string) | Data siswa/anggota |
| `pegawai` | `id_pegawai` (string) | Akun pegawai/pustakawan |
| `peminjaman` | `id_transaksi` (string) | Transaksi peminjaman |

---

## 🛠️ Tech Stack

| Teknologi | Versi | Kegunaan |
|---|---|---|
| PHP | 8.2+ | Runtime |
| Laravel | 12.x | Framework |
| PostgreSQL | 15+ | Database |
| Laravel Sanctum | 4.x | API Authentication |

---

## ⚡ Instalasi & Setup

### Prasyarat
- PHP 8.2+
- Composer
- PostgreSQL 15+
- Git

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/XWAYLAND/ELMS-Backend.git
cd ELMS-Backend

# 2. Install dependencies
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
#    DB_CONNECTION=pgsql
#    DB_HOST=127.0.0.1
#    DB_PORT=5432
#    DB_DATABASE=elibrary_lms
#    DB_USERNAME=postgres
#    DB_PASSWORD=your_password

# 6. Buat database PostgreSQL
# Di psql: CREATE DATABASE elibrary_lms;

# 7. Jalankan migrasi dan seeder
php artisan migrate --seed

# 8. Jalankan server
php artisan serve
```

> **Default Admin Account** (dari seeder):  
> Email: `admin@elibrary.com`  
> Password: `password123`  
> ⚠️ **Ganti password ini sebelum deploy ke production!**

---

## 📡 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Format Response

**Success:**
```json
{
  "success": true,
  "message": "Data berhasil diambil.",
  "data": { ... },
  "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 75 }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": { "field": ["error message"] }
}
```

---

### 🔐 Auth

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| `POST` | `/api/auth/login` | Login pegawai | ❌ |
| `POST` | `/api/auth/logout` | Logout (hapus token) | ✅ |
| `GET` | `/api/auth/me` | Data pegawai aktif | ✅ |

**Request Login:**
```json
{
  "email": "admin@elibrary.com",
  "password": "password123"
}
```

**Response Login:**
```json
{
  "success": true,
  "data": {
    "pegawai": { "id_pegawai": "PGW001", "nama": "Admin", "email": "admin@elibrary.com" },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

> Gunakan token di header: `Authorization: Bearer {token}`

---

### 📚 Buku

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| `GET` | `/api/buku` | Daftar buku (search & filter) | ❌ |
| `GET` | `/api/buku/{isbn}` | Detail buku | ❌ |
| `POST` | `/api/buku` | Tambah buku baru | ✅ |
| `PUT` | `/api/buku/{isbn}` | Update buku | ✅ |
| `DELETE` | `/api/buku/{isbn}` | Hapus buku | ✅ |

**Query Parameters GET /api/buku:**

| Parameter | Tipe | Deskripsi |
|---|---|---|
| `search` | string | Cari di judul, penulis, penerbit, jenis |
| `id_jenis` | string | Filter berdasarkan ID jenis |
| `id_penulis` | string | Filter berdasarkan ID penulis |
| `id_penerbit` | string | Filter berdasarkan ID penerbit |
| `bahasa` | string | Filter berdasarkan bahasa |
| `per_page` | integer | Item per halaman (default: 15) |

**Contoh:** `GET /api/buku?search=laskar&id_jenis=JNS001&per_page=10`

---

### 👥 Jenis / Penulis / Penerbit

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| `GET` | `/api/jenis` | Daftar jenis buku | ❌ |
| `POST` | `/api/jenis` | Tambah jenis | ✅ |
| `PUT` | `/api/jenis/{id}` | Update jenis | ✅ |
| `DELETE` | `/api/jenis/{id}` | Hapus jenis | ✅ |

_(Endpoint yang sama berlaku untuk `/api/penulis` dan `/api/penerbit`)_

---

### 🎓 Anggota

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| `GET` | `/api/anggota` | Daftar anggota | ✅ |
| `POST` | `/api/anggota` | Tambah anggota | ✅ |
| `GET` | `/api/anggota/{nis}` | Detail anggota | ✅ |
| `PUT` | `/api/anggota/{nis}` | Update anggota | ✅ |
| `DELETE` | `/api/anggota/{nis}` | Hapus anggota | ✅ |
| `PUT` | `/api/anggota/{nis}/fcm-token` | Update FCM token Flutter | ❌ |

**Query Parameters GET /api/anggota:**

| Parameter | Tipe | Deskripsi |
|---|---|---|
| `search` | string | Cari berdasarkan nama atau NIS |
| `kelas` | string | Filter berdasarkan kelas (e.g., `X-A`) |

---

### 📋 Peminjaman

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| `GET` | `/api/peminjaman` | Daftar peminjaman | ✅ |
| `POST` | `/api/peminjaman` | Buat transaksi baru | ✅ |
| `GET` | `/api/peminjaman/{id}` | Detail peminjaman | ✅ |
| `PUT` | `/api/peminjaman/{id}` | Update status | ✅ |
| `DELETE` | `/api/peminjaman/{id}` | Hapus transaksi | ✅ |
| `GET` | `/api/peminjaman/jatuh-tempo` | Peminjaman mendekati deadline | ✅ |

**Query Parameters GET /api/peminjaman:**

| Parameter | Tipe | Deskripsi |
|---|---|---|
| `status` | string | `pending` \| `dipinjam` \| `dikembalikan` \| `terlambat` |
| `nis` | string | Filter berdasarkan NIS anggota |
| `isbn` | string | Filter berdasarkan ISBN buku |

**Query Parameters GET /api/peminjaman/jatuh-tempo:**

| Parameter | Tipe | Deskripsi |
|---|---|---|
| `hari` | integer | Jangkauan hari (default: 3) |

---

### 👔 Pegawai

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| `GET` | `/api/pegawai` | Daftar pegawai | ✅ |
| `POST` | `/api/pegawai` | Tambah pegawai | ✅ |
| `GET` | `/api/pegawai/{id}` | Detail pegawai | ✅ |
| `PUT` | `/api/pegawai/{id}` | Update pegawai | ✅ |
| `DELETE` | `/api/pegawai/{id}` | Hapus pegawai | ✅ |

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Test dengan coverage
php artisan test --coverage
```

---

## 📁 Struktur Project

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/           # AuthController, BukuController, dll.
│   ├── Requests/          # Form validation (Store*, Update*)
│   └── Resources/         # API response transformers
├── Models/                # Eloquent models + relations + scopes
database/
├── migrations/            # 7 migration files (ordered by dependency)
└── seeders/               # Sample data seeders
routes/
└── api.php                # Semua API routes
```

---

## 🤝 Kontribusi

1. Fork repository ini
2. Buat branch fitur: `git checkout -b feat/nama-fitur`
3. Commit perubahan: `git commit -m "feat: deskripsi singkat"`
4. Push ke branch: `git push origin feat/nama-fitur`
5. Buat Pull Request

### Konvensi Commit

| Prefix | Kegunaan |
|---|---|
| `feat:` | Fitur baru |
| `fix:` | Bug fix |
| `refactor:` | Refactoring kode |
| `docs:` | Update dokumentasi |
| `test:` | Tambah/update test |

---

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](LICENSE).

---

<div align="center">
  Dibuat dengan ❤️ menggunakan Laravel & PostgreSQL
</div>
