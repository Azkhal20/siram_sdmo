# Panduan Setup Laravel dengan Docker

## Cara Install di Laptop Lain (Windows/Mac/Linux)

### Persyaratan:
- Docker Desktop terinstall ([Download di sini](https://www.docker.com/products/docker-desktop))
- Git terinstall (opsional, untuk clone repository)

---

## Langkah 1: Persiapan Project

### A. Jika Punya File Project (ZIP):
1. Extract file project ke folder, misal: `C:\Projects\siram-sdmo`
2. Buka Command Prompt / Terminal
3. Masuk ke folder project:
   ```bash
   cd C:\Projects\siram-sdmo
   ```

### B. Jika Clone dari GitHub:
```bash
git clone https://github.com/Azkhal20/siram_sdmo.git
cd siram_sdmo
```

---

## Langkah 2: Setup Environment

1. **Copy file .env.docker menjadi .env:**
   ```bash
   # Windows (PowerShell)
   copy .env.docker .env
   
   # Mac/Linux
   cp .env.docker .env
   ```

2. **(Opsional) Edit .env** jika perlu ubah password database, dll.

---

## Langkah 3: Jalankan Docker

```bash
docker-compose up -d
```

**Penjelasan:**
- `up`: Menjalankan semua service (app, nginx, database)
- `-d`: Detached mode (jalan di background)

**Tunggu 2-5 menit** untuk download image dan setup container pertama kali.

---

## Langkah 4: Install Dependencies & Setup Database

### A. Masuk ke Container Laravel:
```bash
docker-compose exec app bash
```

### B. Install Composer Dependencies:
```bash
composer install
```

### C. Install NPM Dependencies & Build Assets:
```bash
npm install
npm run build
```

### D. Generate Application Key (jika belum ada):
```bash
php artisan key:generate
```

### E. Jalankan Migrasi Database:
```bash
php artisan migrate --seed
```

### F. Keluar dari Container:
```bash
exit
```

---

## Langkah 5: Akses Aplikasi

Buka browser, akses:
```
http://localhost:8080
```

**Selamat!** Laravel sudah jalan di Docker! 🎉

---

## Perintah Docker yang Berguna

### Melihat Status Container:
```bash
docker-compose ps
```

### Melihat Log:
```bash
docker-compose logs -f
```

### Stop Semua Container:
```bash
docker-compose down
```

### Start Ulang:
```bash
docker-compose up -d
```

### Hapus Semua (termasuk database):
```bash
docker-compose down -v
```

### Akses MySQL via Terminal:
```bash
docker-compose exec db mysql -u root -psecret db_absensi_bkn
```

---

## Troubleshooting

### Error Port Sudah Dipakai:
Jika port 8080 atau 3307 sudah dipakai, edit `docker-compose.yml`:
```yaml
ports:
  - "9090:80"  # Ganti 8080 jadi 9090
```

### Permission Denied (Linux/Mac):
```bash
sudo chmod -R 777 storage bootstrap/cache
```

### Database Connection Error:
1. Pastikan container 'db' sudah jalan: `docker-compose ps`
2. Periksa `.env`:
   - `DB_HOST=db` (bukan localhost!)
   - `DB_PORT=3306`

---

## Update Kode Project

Jika ada perubahan kode:
```bash
docker-compose restart app
```

Jika perlu rebuild image:
```bash
docker-compose up -d --build
```

---

## Sharing ke Teman/Tim

1. **Kirim folder project** (tanpa vendor dan node_modules)
2. Teman tinggal:
   ```bash
   docker-compose up -d
   docker-compose exec app composer install
   docker-compose exec app npm install && npm run build
   docker-compose exec app php artisan migrate
   ```

**DONE!** Project langsung jalan di laptop mereka.
