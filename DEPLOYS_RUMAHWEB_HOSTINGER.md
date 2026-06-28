# Panduan Hosting Laravel di Rumahweb / Hostinger (cPanel / hPanel)

Dokumen ini berisi panduan langkah demi langkah untuk melakukan deploy aplikasi Franchise Kebab Masyul ke shared hosting (Rumahweb atau Hostinger) menggunakan branch `rumahweb` ini.

---

## 1. Persiapan Awal di Komputer Lokal

Sebelum mengupload file ke hosting, pastikan kamu melakukan kompilasi aset frontend secara lokal agar file CSS & JS buatan Vite terangkut ke hosting:

1. Buka terminal lokal kamu.
2. Jalankan perintah kompilasi aset:
   ```bash
   npm run build
   ```
3. Kompilasi ini akan menghasilkan folder `public/build`. Karena kita sudah mengubah `.gitignore` pada branch `rumahweb` ini, folder `public/build` sekarang akan terdeteksi oleh Git dan bisa dicommit.
4. Commit dan push aset tersebut:
   ```bash
   git add public/build
   git commit -m "build: compile assets for production shared hosting"
   git push origin rumahweb
   ```

---

## 2. Struktur Direktori yang Aman di Hosting

**JANGAN** mengupload seluruh file Laravel langsung ke dalam folder `public_html`. Ini sangat berbahaya karena file sensitif seperti `.env` bisa diakses oleh publik secara langsung.

Ikuti struktur direktori yang direkomendasikan ini:
* Letakkan seluruh folder aplikasi Laravel di luar folder `public_html` (misal di direktori `/home/u123456/franchise-app`).
* Arahkan **Document Root** domain utama kamu ke folder `/home/u123456/franchise-app/public`.

### Cara Mengatur di Hostinger (hPanel):
1. Masuk ke **hPanel Hostinger** -> **Websites** -> **Dashboard**.
2. Cari menu **Domain** -> **Subdomains** atau **Add Website**.
3. Jika menggunakan domain utama, cari menu **Website Settings** -> **Directory / Folder**.
4. Ubah tujuan folder dari `public_html` ke `franchise-app/public`.

### Cara Mengatur di Rumahweb (cPanel):
1. Masuk ke **cPanel** -> **Domains**.
2. Klik **Manage** pada domain kamu.
3. Ubah kolom **Document Root** menjadi `franchise-app/public`.

---

## 3. Konfigurasi File `.env` di Hosting

1. Copy file `.env.production` yang ada di root direktori branch ini menjadi `.env` di server hosting kamu.
2. Edit file `.env` tersebut di File Manager hosting dan sesuaikan konfigurasi database MySQL kamu:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domainmitrakamu.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=u123456_dbmasyul  # Nama database dari hosting
   DB_USERNAME=u123456_usermasyul  # Username database dari hosting
   DB_PASSWORD=password_db_kamu   # Password database dari hosting
   ```
3. Generate application key dengan masuk ke SSH hosting (jika tersedia) dan jalankan:
   ```bash
   php artisan key:generate
   ```
   *Jika tidak ada SSH*, kamu bisa mencopy nilai `APP_KEY` dari file `.env` lokal kamu ke `.env` server hosting.

---

## 4. Jalankan Migrasi & Database Seeder di Hosting

### Opsi A: Jika Ada Akses SSH (Direkomendasikan)
Hubungkan terminal kamu ke SSH hosting, masuk ke folder aplikasi (`cd franchise-app`), lalu jalankan perintah:
```bash
# Jalankan migrasi dan seeder database
php artisan migrate --force
php artisan db:seed --force
```

### Opsi B: Jika Tidak Ada Akses SSH (Lewat Web Routing Sementara)
Jika hosting tidak menyediakan akses SSH, kamu bisa menjalankan migrasi secara sementara via route web:
1. Buka file [routes/web.php](file:///c:/laragon/www/franchise-app/routes/web.php) di editor cPanel.
2. Tambahkan baris kode ini di bagian paling atas (di luar auth middleware):
   ```php
   Route::get('/run-migration-temp', function () {
       try {
           Artisan::call('migrate:fresh', ['--force' => true]);
           Artisan::call('db:seed', ['--force' => true]);
           return "Migration & Seeding berhasil!";
       } catch (\Exception $e) {
           return "Gagal: " . $e->getMessage();
       }
   });
   ```
3. Akses URL `https://domainkamu.com/run-migration-temp` di browser sekali saja.
4. **PENTING:** Setelah berhasil, segera hapus kembali route tersebut dari `routes/web.php` demi keamanan database kamu!

---

## 5. Mengatur Izin Folder (Permissions)

Web server di shared hosting memerlukan akses menulis (*write access*) ke beberapa folder penting Laravel.
Di File Manager cPanel / hPanel:
1. Klik kanan folder `storage` -> pilih **Permissions** -> atur ke `775` (atau `755` tergantung provider hosting).
2. Lakukan hal yang sama untuk folder `bootstrap/cache` -> atur ke `775`.

---

## 6. Membuat Symlink Storage (Penting untuk Gambar/MOU)

Agar dokumen MOU dan gambar pendukung yang diupload oleh mitra dapat diakses oleh publik, buatlah symbolic link:

* **Jika ada SSH:**
  ```bash
  php artisan storage:link
  ```
* **Jika tidak ada SSH (Gunakan Cron Job cPanel/Hostinger):**
  1. Masuk ke menu **Cron Jobs** di cPanel/hPanel.
  2. Tambahkan tugas baru yang berjalan sekali saja dengan perintah command:
     ```bash
     ln -s /home/username/franchise-app/storage/app/public /home/username/franchise-app/public/storage
     ```
     *(Ganti `/home/username/franchise-app` sesuai dengan absolute path folder kamu di hosting)*.
  3. Jalankan cron job tersebut, lalu hapus kembali cron job-nya setelah symlink terbentuk.
