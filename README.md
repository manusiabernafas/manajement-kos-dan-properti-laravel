# KostManager

Sistem manajemen kost/property management yang lengkap dan production-ready, dibangun dengan Laravel 12, MySQL, Blade, dan CSS custom (tanpa Tailwind/Bootstrap).

Mendukung banyak properti dalam satu akun owner (Properti → Gedung → Lantai → Kamar), lengkap dengan alur bisnis penuh: booking, check-in, check-out, kontrak, tagihan berulang otomatis, verifikasi pembayaran, denda keterlambatan, meter listrik/air, maintenance, komplain, inventaris, pengumuman, laporan, portal penghuni terpisah, notifikasi, audit log, dan manajemen role & permission granular.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4+
- **Database:** MySQL 8
- **Frontend:** Blade, CSS custom (design system dengan CSS variables + dark mode), Vanilla JavaScript, Vite
- **Chart:** Chart.js
- **Icon:** Font Awesome
- **PDF:** barryvdh/laravel-dompdf
- **Excel/CSV Export:** maatwebsite/excel
- **RBAC:** spatie/laravel-permission

## Requirement

- PHP >= 8.2 (dikembangkan & diuji dengan PHP 8.4)
- Composer 2.x
- MySQL >= 8.0
- Node.js >= 18 & npm
- Ekstensi PHP: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, gd/imagick (untuk dompdf), fileinfo

## Instalasi

```bash
# 1. Clone / masuk ke direktori proyek
cd management_kos

# 2. Install dependency PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate
```

### Konfigurasi Database

Buat database MySQL:

```sql
CREATE DATABASE kostmanager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan kredensial di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kostmanager
DB_USERNAME=root
DB_PASSWORD=

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
KOSTMANAGER_TIMEZONE=Asia/Jakarta
KOSTMANAGER_CURRENCY=IDR
```

### Migrasi & Seeder

```bash
# Jalankan migrasi + seed data demo sekaligus
php artisan migrate --seed

# Atau reset total (drop semua tabel lalu migrate + seed ulang)
php artisan migrate:fresh --seed
```

Seeder akan membuat:
- Role & permission lengkap (Super Admin, Owner, Manager, Finance, Staff, Technician, Tenant)
- 5 akun default (lihat bagian **Akun Default** di bawah)
- 2 properti, 4 gedung, 8 lantai, 24 kamar, 6 tipe kamar, 12 fasilitas
- 10 penghuni dengan variasi status (aktif, booking, mantan penghuni)
- Kontrak aktif, deposit, tagihan (lunas/partial/belum bayar/overdue), riwayat pembayaran
- Meter listrik/air + pembacaan, pengeluaran, tiket maintenance, komplain, inventaris, pengumuman

### Storage Link

Wajib dijalankan agar file upload (foto profil, bukti pembayaran, dokumen, lampiran) bisa diakses publik:

```bash
php artisan storage:link
```

### Asset Frontend

```bash
npm install

# Build untuk production
npm run build

# Mode development (hot reload)
npm run dev
```

### Menjalankan Aplikasi

```bash
php artisan serve
```

Aplikasi dapat diakses di `http://127.0.0.1:8000`. Jika menggunakan `npm run dev`, jalankan bersamaan dengan `php artisan serve` di terminal terpisah agar Vite HMR aktif.

## Scheduler (Tagihan Berulang & Denda Otomatis)

Sistem memiliki command `billing:generate` yang:
1. Membuat tagihan sewa bulanan otomatis untuk seluruh kontrak aktif sesuai tanggal billing (idempotent — tidak membuat duplikat).
2. Menandai invoice yang sudah lewat jatuh tempo sebagai `overdue`.
3. Menerapkan denda keterlambatan sesuai konfigurasi di halaman Denda (sekali per invoice).
4. Mengirim notifikasi (tagihan baru, jatuh tempo, overdue, kontrak akan berakhir).

Command ini dijadwalkan berjalan otomatis setiap hari jam 01:00 (lihat `routes/console.php`). Untuk menjalankan scheduler di server:

```bash
# Tambahkan cron job berikut (jalan setiap menit, Laravel akan menentukan kapan task benar-benar dieksekusi)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Untuk menjalankan manual (misalnya saat testing):

```bash
php artisan billing:generate
```

## Akun Default (Demo)

Semua akun memakai password: **`password`**

| Role | Email | Deskripsi |
|---|---|---|
| Super Admin | admin@kostmanager.test | Akses penuh seluruh sistem |
| Owner | owner@kostmanager.test | Pemilik kost, akses penuh operasional |
| Finance | finance@kostmanager.test | Fokus keuangan (tagihan, pembayaran, deposit, pengeluaran) |
| Staff | staff@kostmanager.test | Operasional harian (penghuni, booking, check-in/out, maintenance) |
| Tenant | tenant@kostmanager.test | Portal penghuni — hanya melihat data miliknya sendiri |

Role **Technician** dan **Manager** juga tersedia (buat user baru melalui menu **Sistem → Users**).

## Struktur Modul

- **Properti** — Properti → Gedung → Lantai → Kamar (dengan visualisasi grid status kamar)
- **Tipe Kamar & Fasilitas** — master data harga & fasilitas, relasi many-to-many polymorphic
- **Penghuni** — profil lengkap dengan dokumen (KTP/KK/Perjanjian), tab riwayat
- **Booking → Check-in → Kontrak → Check-out** — alur sewa penuh dengan DB transaction, otomatis membuat kontrak + deposit + tagihan pertama saat check-in, dan menghitung refund deposit dikurangi piutang saat check-out
- **Tagihan & Pembayaran** — tagihan manual/otomatis, item multi-baris, verifikasi pembayaran (update status invoice otomatis), cetak PDF
- **Deposit & Denda** — pengelolaan refund deposit, mesin denda otomatis (tetap/per-hari/persentase) dengan grace period
- **Meter Listrik/Air** — pencatatan meter dengan validasi (meter akhir ≥ meter awal), generate tagihan otomatis dari pembacaan
- **Pengeluaran, Maintenance, Komplain, Inventaris, Pengumuman** — CRUD penuh dengan histori & comment thread
- **Dashboard** — statistik real-time & 4 chart (Chart.js) dari data database
- **Laporan** — 12 jenis laporan dengan filter, export CSV/Excel/PDF
- **Portal Penghuni** — dashboard terpisah, hanya menampilkan data milik penghuni yang login (diisolasi ketat via Policy + pengecekan tenant_id)
- **Notifikasi** — in-app (database) + abstraksi WhatsApp (log-based stub, siap diintegrasikan provider nyata) untuk 10 jenis event
- **Audit Log** — mencatat seluruh aksi penting (create/update/delete/login/verify payment/check-in/check-out, dsb)
- **Users, Roles & Permissions, Settings** — manajemen akun & konfigurasi aplikasi (prefix dokumen, currency, timezone, tema default)

## Menjalankan Test

```bash
# Buat database khusus testing terlebih dahulu
mysql -u root -e "CREATE DATABASE kostmanager_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan test
```

47 Feature test mencakup: login & autentikasi, otorisasi berbasis permission, CRUD properti/kamar/penghuni, booking, check-in, check-out, tagihan & pembayaran (termasuk validasi overpayment), isolasi data penghuni, generate tagihan berulang (idempotent), dan kalkulasi meter.

## Deployment Production

1. Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`.
2. Jalankan `composer install --optimize-autoloader --no-dev`.
3. Jalankan `npm run build`.
4. Cache konfigurasi: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
5. Pastikan folder `storage/` dan `bootstrap/cache/` writable oleh web server.
6. Arahkan document root web server ke folder `public/`.
7. Pasang cron job scheduler (lihat bagian **Scheduler** di atas).
8. Konfigurasi queue worker jika ingin notifikasi/email berjalan asinkron (`QUEUE_CONNECTION=database` sudah didukung — jalankan `php artisan queue:work`).
9. Set `MAIL_MAILER` ke provider SMTP sungguhan untuk notifikasi email.
10. Untuk integrasi WhatsApp nyata, implementasikan ulang `app/Notifications/Channels/WhatsAppChannel.php` dengan provider pilihan (Fonnte, Twilio, WhatsApp Business API, dll) — abstraksi channel sudah siap pakai di seluruh notifikasi.

## Ringkasan Teknis

- Laravel 12 (PHP 8.4)
- Database: MySQL 8
- 12 file migration (32 tabel)
- 32 Eloquent model dengan relasi lengkap
- 42 controller
- 22 Policy (otorisasi granular per modul)
- 10 Service class (business logic: check-in, check-out, billing, penalty, payment, dsb)
- 10 Notification class + abstraksi WhatsApp channel
- 179 route terdaftar
- 131 Blade view
- 47 automated test (95 assertion), seluruhnya lulus
# Manajemen-kost
