# TUTORIAL: Cara Menjalankan Laravel Chat Multiuser

## Daftar Isi

1. [Persyaratan Sistem](#persyaratan-sistem)
2. [Instalasi Lokal](#instalasi-lokal)
3. [Konfigurasi](#konfigurasi)
4. [Menjalankan Aplikasi](#menjalankan-aplikasi)
5. [Fitur Aplikasi](#fitur-aplikasi)
6. [Akun Default](#akun-default)
7. [Setup Pusher (Realtime)](#setup-pusher-realtime)
8. [Setup Gemini AI](#setup-gemini-ai)
9. [Deploy ke Shared Hosting](#deploy-ke-shared-hosting)
10. [Troubleshooting](#troubleshooting)

---

## Persyaratan Sistem

| Software | Versi Minimum |
|----------|--------------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| NPM | 9+ |
| MySQL | 8.0+ (atau SQLite untuk development) |

---

## Instalasi Lokal

### 1. Clone Repository

```bash
git clone https://github.com/zakianmaulana01/LARAVEL-CHAT-MULTIUSER.git
cd LARAVEL-CHAT-MULTIUSER
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup Database

**Opsi A: MySQL (Recommended untuk production)**
```bash
# Buat database di MySQL
mysql -u root -e "CREATE DATABASE laravel_chat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Edit .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_chat
DB_USERNAME=root
DB_PASSWORD=
```

**Opsi B: SQLite (Untuk development cepat)**
```bash
touch database/database.sqlite

# Edit .env
DB_CONNECTION=sqlite
DB_DATABASE=/path/absolute/ke/database/database.sqlite
```

### 5. Jalankan Migration & Seeder

```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
npm run build
```

---

## Konfigurasi

### File `.env` yang Perlu Diisi

```env
# App
APP_NAME="Laravel Chat Multiuser"
APP_URL=http://localhost:8000

# Database (sesuaikan)
DB_CONNECTION=mysql
DB_DATABASE=laravel_chat
DB_USERNAME=root
DB_PASSWORD=

# Pusher (untuk realtime chat)
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=ap1

# Gemini AI (untuk AI Assistant)
GEMINI_API_KEY=your-gemini-api-key

# Vite Pusher (auto dari PUSHER_*)
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

---

## Menjalankan Aplikasi

### Development

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (opsional, hanya jika mau hot-reload)
npm run dev
```

Buka browser: **http://localhost:8000**

### Production (tanpa `npm run dev`)

```bash
npm run build
php artisan serve
```

---

## Fitur Aplikasi

### Halaman & URL

| URL | Deskripsi |
|-----|-----------|
| `/` | Landing page (company profile) |
| `/blade/login` | Login (Blade + jQuery) |
| `/blade/register` | Register |
| `/blade/conversations` | Chat realtime (WhatsApp-like) |
| `/blade/ai-chat` | AI Chat Assistant (Gemini) |
| `/blade/admin` | Admin Dashboard (superadmin only) |
| `/vue` | Vue 3 SPA (alternatif frontend) |

### Fitur Utama

1. **Chat Realtime** — Kirim pesan instant via Pusher, typing indicator, read receipt
2. **AI Chat Assistant** — Tanya jawab seputar produk, powered by Google Gemini 2.0 Flash
3. **Admin Panel** — Ban/unban user, hapus pesan, lihat statistik
4. **Landing Page** — SaaS product showcase dengan video demo placeholder
5. **Dual Frontend** — Blade+jQuery (Konsep A) dan Vue 3 SPA (Konsep B)

---

## Akun Default

Setelah `php artisan db:seed`:

| Email | Password | Role |
|-------|----------|------|
| `admin@erp.test` | `password` | Superadmin |
| (10 random users) | `password` | User |

---

## Setup Pusher (Realtime)

1. Buat akun gratis di [pusher.com](https://pusher.com)
2. Buat Channel App baru
3. Copy kredensial ke `.env`:

```env
PUSHER_APP_ID=1234567
PUSHER_APP_KEY=abcdef123456
PUSHER_APP_SECRET=secretkey123
PUSHER_APP_CLUSTER=ap1
```

4. Pastikan `VITE_PUSHER_APP_KEY` dan `VITE_PUSHER_APP_CLUSTER` ikut ter-set
5. Rebuild assets: `npm run build`

**Tanpa Pusher?** Chat tetap bisa dipakai, tapi pesan baru hanya muncul setelah refresh halaman.

---

## Setup Gemini AI

1. Buka [Google AI Studio](https://aistudio.google.com/apikey)
2. Klik "Create API Key" (gratis, tidak perlu billing)
3. Copy API key ke `.env`:

```env
GEMINI_API_KEY=AIzaSy...your-key-here
```

4. Akses AI Chat di `/blade/ai-chat`

**Free Tier Limit:** 15 request/menit, 1500 request/hari

**Tanpa API Key?** Halaman AI Chat tetap bisa diakses, tapi akan menampilkan pesan error saat kirim pertanyaan.

---

## Deploy ke Shared Hosting

### Persiapan

```bash
# Build assets dulu di lokal
npm run build

# Upload semua file ke hosting (via FTP/File Manager)
# KECUALI: node_modules/, .git/, .env
```

### Langkah di Hosting

1. **Upload files** ke `public_html/` atau subfolder
2. **Setup database** via phpMyAdmin:
   - Import `database/db-builder/schema.sql`
   - Import `database/db-builder/seed.sql`
3. **Setup `.env`** — copy `.env.example`, isi kredensial DB & Pusher & Gemini
4. **Generate key:**
   ```bash
   php artisan key:generate
   ```
5. **Konfigurasi cron** (untuk queue):
   ```
   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```
   Atau jalankan manual: `php artisan queue:work --once`

### Struktur Folder Hosting

```
public_html/
├── public/          ← Document root (point domain ke sini)
│   ├── index.php
│   ├── build/       ← Compiled assets (dari npm run build)
│   └── ...
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── ...
```

**Jika hosting tidak bisa set document root**, pindahkan isi `public/` ke root dan edit `index.php`:
```php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

---

## Troubleshooting

### "Route [login] not defined"
Pastikan `routes/blade.php` ter-load. Cek `bootstrap/app.php` ada `then:` callback.

### Pusher tidak connect
- Cek key & cluster di `.env`
- Pastikan `VITE_PUSHER_APP_KEY` sudah diisi
- Rebuild: `npm run build`
- Cek console browser untuk error

### AI Chat error "API key not configured"
- Pastikan `GEMINI_API_KEY` diisi di `.env`
- Cek limit free tier (15 RPM)

### Migration gagal di SQLite
- SQLite tidak support `enum()` secara native, tapi Laravel 11 handle via CHECK constraint
- Pastikan versi PHP 8.2+ dan ext-sqlite3 aktif

### Asset tidak muncul (CSS/JS broken)
```bash
npm run build
php artisan cache:clear
php artisan config:clear
```

### Permission error di storage
```bash
chmod -R 775 storage bootstrap/cache
```

---

## Menjalankan Test

```bash
# Run semua test
php artisan test

# Run dengan detail
php artisan test --verbose

# Run specific test class
php artisan test --filter=ChatTest
```

Test menggunakan SQLite in-memory (otomatis, tidak perlu setup).

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11, PHP 8.2 |
| Frontend A | Blade + jQuery 3.7 + Tailwind CSS |
| Frontend B | Vue 3 SPA + Vue Router |
| Realtime | Pusher + Laravel Echo |
| AI | Google Gemini 2.0 Flash |
| CSS | Tailwind CSS 3.4 |
| Build | Vite 6 |
| Database | MySQL 8 / SQLite |
| Queue | Database driver + Cron |
