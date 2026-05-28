# Laravel Chat Multiuser

> Aplikasi chat realtime multi-user dengan Laravel 11, dua konsep frontend (Blade + Vue 3), compatible dengan **shared hosting**.

![Laravel](https://img.shields.io/badge/Laravel-11-red?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=flat-square)
![Pusher](https://img.shields.io/badge/Realtime-Pusher-purple?style=flat-square)

## Deskripsi

Satu project Laravel 11 dengan **dua konsep frontend** dalam folder terpisah:

| Konsep | Stack | Lokasi |
|--------|-------|--------|
| **Konsep A** | PHP + Blade + Alpine.js | `resources/views/blade/` |
| **Konsep B** | Vue 3 SPA (pre-build) | `resources/js/vue/` → build ke `public/` |

Kedua konsep punya **backend, database, dan fitur yang sama** — hanya frontend yang berbeda.

## Fitur

- **Autentikasi** — Login & register dengan role (user / superadmin)
- **Chat Realtime** — Chat 1-on-1 via Pusher + Laravel Echo
- **Typing Indicator** — "Sedang mengetik..." realtime
- **Status Pesan** — Terkirim & dibaca (read receipt)
- **Notifikasi Realtime** — Bell icon + badge saat ada pesan baru
- **File Upload** — Kirim gambar/dokumen (opsional)
- **Superadmin Panel** — Dashboard stats, hapus pesan, ban/hapus user
- **UI WhatsApp-like** — Modern, responsif, mobile-first
- **DB Builder** — SQL script siap import ke phpMyAdmin

## Requirements

- PHP 8.2+
- Laravel 11
- MySQL 5.7+ / 8.0+
- Composer
- Node.js 18+ (hanya untuk build di lokal, **tidak perlu di hosting**)
- Akun Pusher (free tier cukup — [pusher.com](https://pusher.com))

## Instalasi Lokal

```bash
# 1. Clone repository
git clone <repo-url>
cd laravel-chat-multiuser

# 2. Install PHP dependencies
composer install

# 3. Install & build frontend assets
npm install
npm run build

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_DATABASE=laravel_chat
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Konfigurasi Pusher di .env
# PUSHER_APP_ID=xxx
# PUSHER_APP_KEY=xxx
# PUSHER_APP_SECRET=xxx
# PUSHER_APP_CLUSTER=ap1

# 7. Migrasi & seed database
php artisan migrate --seed

# ATAU import SQL langsung:
# mysql -u root laravel_chat < database/db-builder/schema.sql
# mysql -u root laravel_chat < database/db-builder/seed.sql

# 8. Jalankan server
php artisan serve
```

## Akses Aplikasi

| URL | Konsep |
|-----|--------|
| `http://localhost:8000/blade` | Blade + Alpine.js |
| `http://localhost:8000/vue` | Vue 3 SPA |
| `http://localhost:8000/blade/admin` | Admin Panel (Blade) |
| `http://localhost:8000/vue/admin` | Admin Panel (Vue) |

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Superadmin | `admin@erp.test` | `password` |
| User (10 akun) | random | `password` |

## Deploy di Shared Hosting

### 1. Upload Files

Upload semua file **kecuali**:
- `node_modules/`
- `.env` (buat langsung di hosting)

### 2. Setup Database

**Opsi A — Import SQL (Recommended):**
1. Buka phpMyAdmin / MySQL Builder di cPanel
2. Buat database baru (misal: `laravel_chat`)
3. Import `database/db-builder/schema.sql`
4. Import `database/db-builder/seed.sql` (opsional, untuk sample data)

**Opsi B — Artisan Migrate:**
```bash
php artisan migrate --seed
```

### 3. Konfigurasi .env

Buat file `.env` di root project:

```env
APP_NAME="Chat App"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=nama_database
DB_USERNAME=username_db
DB_PASSWORD=password_db

BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-id
PUSHER_APP_KEY=your-key
PUSHER_APP_SECRET=your-secret
PUSHER_APP_CLUSTER=ap1

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=file
```

### 4. Setup Pusher

1. Daftar di [pusher.com](https://pusher.com) (free tier: 200K messages/day)
2. Create new Channel app
3. Copy App ID, Key, Secret, Cluster
4. Paste ke `.env`

### 5. Pre-Build Vue (di Lokal)

```bash
npm run build
```

Upload hasil build dari folder `public/build/` ke hosting.

### 6. Setup Cron Job

Di cPanel → Cron Jobs, tambahkan:

```
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Atau untuk queue worker via cron:

```
* * * * * cd /home/username/public_html && php artisan queue:work --once >> storage/logs/queue.log 2>&1
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

## Struktur Folder

```
app/
  Models/          → User, Conversation, Message
  Events/          → MessageSent, TypingStarted, NotificationSent
  Services/        → ChatService
  Http/
    Controllers/   → ChatController, AdminController, Auth/
    Middleware/    → EnsureNotBanned, SuperadminMiddleware
resources/
  views/blade/     → Konsep A: Blade + Alpine.js
    layouts/       → Layout utama
    auth/          → Login, Register
    chat/          → Chat interface
    admin/         → Dashboard, Users, Messages
  js/vue/          → Konsep B: Vue 3 SPA
    views/         → Login, Register, Chat, Admin
  css/             → Tailwind CSS
database/
  migrations/      → Schema migrations
  seeders/         → User, Conversation, Message seeders
  factories/       → Model factories
  db-builder/      → schema.sql + seed.sql
routes/
  blade.php        → Routes untuk konsep Blade
  vue.php          → Routes untuk konsep Vue
  channels.php     → Broadcasting channels
```

## Environment Variables

| Variable | Deskripsi | Contoh |
|----------|-----------|--------|
| `DB_CONNECTION` | Driver database | `mysql` |
| `DB_DATABASE` | Nama database | `laravel_chat` |
| `BROADCAST_CONNECTION` | Driver broadcast | `pusher` |
| `PUSHER_APP_KEY` | Pusher app key | `abc123` |
| `PUSHER_APP_SECRET` | Pusher secret | `xyz789` |
| `PUSHER_APP_CLUSTER` | Pusher cluster | `ap1` |
| `SESSION_DRIVER` | Driver session | `file` atau `database` |
| `QUEUE_CONNECTION` | Driver queue | `database` |

## Pusher Events

| Event | Channel | Data |
|-------|---------|------|
| `MessageSent` | `presence-conversation.{id}` | message object |
| `TypingStarted` | `presence-conversation.{id}` | user_id, user_name |
| `NotificationSent` | `private-user.{id}` | message preview |

## Troubleshooting

### Pesan tidak realtime
- Pastikan Pusher key benar di `.env`
- Pastikan `BROADCAST_CONNECTION=pusher`
- Cek console browser untuk error WebSocket

### Error 500 di shared hosting
- Pastikan PHP 8.2+ aktif
- Jalankan `composer install --no-dev`
- Pastikan folder `storage/` writable (755 atau 775)
- Pastikan `.env` sudah terisi

### Vue tidak loading
- Pastikan sudah `npm run build` di lokal
- Upload folder `public/build/` ke hosting
- Pastikan file `.htaccess` ada di `public/`

### Queue tidak jalan
- Pastikan cron job aktif di cPanel
- Cek `storage/logs/queue.log` untuk error
- Pastikan tabel `jobs` sudah ada di database

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Database:** MySQL 5.7+/8.0+
- **Frontend A:** Blade, Alpine.js, Tailwind CSS
- **Frontend B:** Vue 3, Vue Router, Tailwind CSS
- **Realtime:** Pusher + Laravel Echo
- **Build Tool:** Vite
- **Queue:** Database driver + Cron

## License

MIT License
