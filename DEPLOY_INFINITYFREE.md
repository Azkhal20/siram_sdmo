# Panduan Deploy Laravel ke InfinityFree

## PERSIAPAN DI LAPTOP (Jalankan command ini di terminal)

### 1. Build Assets (Wajib!)
```bash
npm install
npm run build
```

### 2. Install Dependencies Production
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Buat File ZIP untuk Upload
**Windows (PowerShell):**
```powershell
Compress-Archive -Path * -DestinationPath siram-sdmo.zip -Force
```

**Atau gunakan WinRAR/7-Zip:**
- Klik kanan folder project → Compress to ZIP
- Pastikan semua file termasuk folder `vendor`, `public`, `app`, dll.

---

## UPLOAD KE INFINITYFREE

### Opsi A: Via File Manager (Tercepat)
1. Login ke cPanel InfinityFree
2. Klik **"Online File Manager"**
3. Masuk ke folder **`htdocs`**
4. **HAPUS** semua file yang ada di `htdocs` (file default InfinityFree)
5. Klik **"Upload"**
6. Upload file `siram-sdmo.zip`
7. Setelah upload selesai, klik kanan file ZIP → **"Extract"**
8. Tunggu sampai proses extract selesai

### Opsi B: Via FTP (Lebih Stabil untuk File Besar)
1. Download **FileZilla** (gratis)
2. Buka FileZilla, isi:
   - **Host**: (lihat di cPanel → FTP Accounts)
   - **Username**: (dari cPanel)
   - **Password**: (password hosting Anda)
   - **Port**: 21
3. Koneksikan
4. Di sisi kanan (Remote), masuk ke folder **`htdocs`**
5. Hapus semua file default
6. Drag semua file Laravel dari laptop ke folder `htdocs`

---

## KONFIGURASI .ENV

1. Di File Manager, buka file `.env`
2. Edit dengan konfigurasi ini:

```env
APP_NAME="SIRAM SDMO"
APP_ENV=production
APP_KEY=[SALIN DARI .ENV LOKAL ANDA]
APP_DEBUG=false  # PENTING: Harus false di production!
APP_URL=http://siramsdmo.rf.gd  # Ganti dengan domain Anda

DB_CONNECTION=mysql
DB_HOST=[HOST MYSQL DARI INFINITYFREE]
DB_PORT=3306
DB_DATABASE=[DATABASE NAME]
DB_USERNAME=[DATABASE USER]
DB_PASSWORD=[DATABASE PASSWORD]

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=daily
LOG_LEVEL=error
```

3. Klik **"Save Changes"**

---

## UBAH DOCUMENT ROOT (Sangat Penting!)

Laravel mengharuskan document root mengarah ke folder `public/`, bukan folder utama.

1. Di cPanel, cari **".htaccess"** di folder `htdocs`
2. Buat file `.htaccess` baru jika belum ada
3. Isi dengan kode ini:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

4. Save file tersebut

---

## JALANKAN MIGRASI DATABASE

1. Di File Manager, buka folder `public`
2. Buat file baru bernama `migrate.php`
3. Isi dengan kode ini:

```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('migrate', ['--force' => true]);
echo $status === 0 ? 'Migration success!' : 'Migration failed!';
```

4. Buka browser, akses: `http://siramsdmo.rf.gd/migrate.php`
5. Jika muncul "Migration success!", **hapus file migrate.php** (untuk keamanan)

---

## TESTING

1. Buka website Anda: `http://siramsdmo.rf.gd`
2. Jika muncul dashboard, **SELAMAT!** Laravel Anda sudah online
3. Test upload PDF dan fungsi lainnya

---

## TROUBLESHOOTING

### Error "500 Internal Server Error"
- Periksa file `.env` sudah benar
- Pastikan `APP_DEBUG=true` sementara untuk melihat error detail
- Periksa permission folder `storage` dan `bootstrap/cache` harus **755**

### Error "Database Connection"
- Pastikan kredensial database benar
- Test koneksi database via phpMyAdmin di cPanel

### Tampilan Rusak (CSS tidak load)
- Jalankan `php artisan storage:link` via file `migrate.php` tadi
- Atau ubah di `.env`: `ASSET_URL=http://siramsdmo.rf.gd`

---

## UPDATE PROJECT (Jika ada perubahan kode)

1. Edit kode di laptop Anda
2. Build ulang: `npm run build`
3. Upload file yang berubah saja via FileZilla FTP
4. Refresh browser

---

**Selamat! Project Laravel Anda sekarang 100% ONLINE dan GRATIS selamanya!** 🎉
