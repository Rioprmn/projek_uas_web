# Panduan Deploy Laravel ke Railway

Panduan ini akan membimbing kamu dari awal hingga Laravel siap online di Railway, termasuk setting database agar tidak error.

---

## 1. Push Project ke GitHub
- Pastikan semua file sudah di-push ke repository GitHub.

## 2. Buat Project Railway dari GitHub
- Login ke https://railway.app
- Klik "New Project" > "Deploy from GitHub repo"
- Pilih repo Laravel kamu

## 3. Setting Environment Variables di Railway
- Buka tab "Variables" di Railway project
- Tambahkan variable berikut (isi dari Railway MySQL):
  - `DB_CONNECTION` = `mysql`
  - `DB_HOST` = (isi host dari Railway DB)
  - `DB_PORT` = (isi port dari Railway DB, biasanya 3306)
  - `DB_DATABASE` = (isi nama database dari Railway DB)
  - `DB_USERNAME` = (isi username dari Railway DB)
  - `DB_PASSWORD` = (isi password dari Railway DB)
  - `APP_KEY` = (lihat langkah 5)
  - `APP_URL` = (isi dengan url Railway kamu, misal https://your-app.up.railway.app)

## 4. Pastikan Procfile dan .env.example
- Procfile sudah ada dan berisi:
  ```
  web: php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
  ```
- .env.example sudah sesuai (lihat file ini)

## 5. Generate APP_KEY di Railway Console
- Setelah deploy pertama selesai, buka tab "Console" di Railway
- Jalankan perintah:
  ```
  php artisan key:generate --show
  ```
- Copy hasilnya, lalu tambahkan ke variable `APP_KEY` di Railway

## 6. Jalankan Migrasi dan Storage Link
- Masih di Railway Console, jalankan:
  ```
  php artisan migrate --force
  php artisan storage:link
  ```

## 7. Selesai! Cek Website
- Buka URL Railway kamu, Laravel sudah online dan database sudah terhubung.

---

### Troubleshooting
- Jika error database, cek ulang semua variable DB di Railway.
- Jika error APP_KEY, pastikan sudah generate dan isi di variable.
- Jika error storage, pastikan sudah jalankan `php artisan storage:link`.

---

Selesai! Project Laravel kamu sudah siap online di Railway 🚀
