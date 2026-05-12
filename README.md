# Leave Management System API

**Postman API Documentation:** [Klik di sini untuk melihat Dokumentasi API](https://documenter.getpostman.com/view/39954677/2sBXqQEHJa)

## System Architecture & Data Flow

Sistem ini dibangun menggunakan pendekatan **Clean Architecture** dengan menerapkan pola **Service-Repository** dan **Data Transfer Objects (DTO)** untuk memastikan pemisahan tanggung jawab (_Separation of Concerns_) yang jelas, skalabilitas, dan kemudahan pengujian (_maintainability_).

### Arsitektur Layer:

1. **Routing & Middleware Layer**: Menangani _request_ masuk. Menggunakan Laravel Sanctum untuk autentikasi dan _Custom Middleware_ (`IsAdmin`) untuk validasi _Role-Based Access Control (RBAC)_.
2. **Controller Layer**: Bertanggung jawab murni sebagai pengatur lalu lintas HTTP. Menerima _request_, melakukan validasi dasar (Form Validation), dan merangkum _request_ menjadi **DTO**. Controller tidak berisi _business logic_ sama sekali.
3. **DTO (Data Transfer Object) Layer**: Objek _immutable_ yang digunakan untuk mentransfer data antar layer (dari Controller ke Service) secara _type-safe_, sehingga struktur data terjamin konsistensinya.
4. **Service Layer**: Pusat dari seluruh **Business Logic**. Layer ini mengeksekusi logika kompleks seperti perhitungan sisa kuota, pengecekan tanggal cuti, hingga implementasi **Database Transaction** (`DB::transaction`). Jika terjadi kegagalan di tengah proses persetujuan (misal: gagal memotong kuota), seluruh proses akan di-_rollback_ untuk menjaga integritas data.
5. **Repository Layer**: Menangani abstraksi _query database_ menggunakan Eloquent ORM. Service layer tidak berinteraksi langsung dengan Model, melainkan meminta data melalui fungsi-fungsi spesifik di Repository (seperti `getByUserId` atau `getAll`), yang juga telah dioptimasi menggunakan _Eager Loading_ (`with()`) untuk mencegah _N+1 Query Problem_.

## Features

- **OAuth 2.0 Authentication**: Login via Google menggunakan Laravel Socialite.
- **Token-Based API**: Diamankan menggunakan Laravel Sanctum.
- **Role-Based Access Control (RBAC)**: Membedakan hak akses antara `admin` dan `employee`.
- **Automated Quota Validation**: Sistem secara otomatis memvalidasi durasi pengajuan cuti terhadap sisa kuota karyawan.
- **Atomic Operations**: Menggunakan Database Transaction untuk memastikan kuota terpotong secara sinkron saat cuti disetujui.

## Tech Stack

- **Framework**: Laravel 11
- **Database**: PostgreSQL (Hosted on Supabase)
- **Authentication**: Laravel Sanctum & Socialite

## Local Setup & Installation

Berikut adalah panduan langkah demi langkah untuk menjalankan proyek ini di mesin lokal Anda:

1.  **Clone the repository**
    ```bash
    git clone https://github.com/asepjamaludinn/be-employee_system.git
    cd be-employee_system
    ```
2.  **Install dependencies**

    ```Bash
    composer install
    ```

3.  **Environment Setup**

    ```Bash
    cp .env.example .env
    php artisan key:generate
    ```

    **[PENTING]** Buka file `.env` yang baru saja dibuat, lalu sesuaikan konfigurasi Database PostgreSQL dan kredensial Google OAuth sesuai data berikut:

    ```env
    # Setup Database (PostgreSQL / Supabase)
    DB_CONNECTION=pgsql
    DB_HOST=aws-1-ap-south-1.pooler.supabase.com
    DB_PORT=5432
    DB_DATABASE=postgres
    DB_USERNAME=postgres.idaoxaopupweksasmneqxc
    DB_PASSWORD="<akan_dikirimkan_terpisah_via_email>"

    # Setup Google OAuth
    GOOGLE_CLIENT_ID="<akan_dikirimkan_terpisah_via_email>"
    GOOGLE_CLIENT_SECRET="<akan_dikirimkan_terpisah_via_email>"
    GOOGLE_REDIRECT_URI="http://127.0.0.1:8000/api/auth/google/callback"
    ```

4.  **Database Migration & Seeding**
    Jalankan migrasi untuk membangun skema tabel sekaligus mengisi database dengan akun Admin dan Employee untuk keperluan testing:

    ```Bash
    php artisan migrate:fresh --seed
    ```

5.  **Serve the Application**

    ```Bash
    php artisan serve
    ```

API sekarang dapat diakses melalui http://127.0.0.1:8000.

### Authentication

- `POST /api/login` - Conventional login.
- `GET /api/auth/google/redirect` - Get Google OAuth URL.
- `GET /api/auth/google/callback` - Handle Google OAuth callback.

### Leave Requests (Requires Bearer Token)

- `GET /api/leave-requests` - View leave requests (Admin sees all, Employee sees only their own).
- `POST /api/leave-requests` - Submit a new leave request (Employee).
- `PATCH /api/leave-requests/{id}/status` - Approve or reject a leave request (Admin only).

## Default Seeded Accounts

Gunakan kredensial berikut di Postman untuk melakukan testing (telah digenerate otomatis oleh seeder):

- **Admin HR**: `admin@perusahaan.com` / `password123`
- **Employee**: `asep@seal.com` / `password123`
