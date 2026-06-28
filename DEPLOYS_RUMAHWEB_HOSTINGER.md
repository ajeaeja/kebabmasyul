# Panduan Hosting Manual Laravel (ZIP) di Rumahweb / Hostinger (Tanpa SSH / Paket Murah)

Dokumen ini berisi panduan lengkap untuk melakukan deploy manual menggunakan file ZIP ke cPanel (Rumahweb) atau hPanel (Hostinger) pada paket hosting murah yang **tidak memiliki akses SSH** dan **tidak bisa mengubah Document Root** (terkunci di `public_html`).

---

## 1. Persiapan Awal di Komputer Lokal (Laragon)

Karena hosting kamu tidak memiliki SSH, kita harus menyiapkan seluruh aset (`public/build`) dan library vendor (`vendor`) secara lokal sebelum di-ZIP.

1. Buka terminal di folder project kamu (`c:\laragon\www\franchise-app`).
2. Jalankan perintah kompilasi aset CSS/JS:
   ```bash
   npm run build
   ```
   *(Perintah ini akan membuat folder `public/build`)*.
3. Jalankan instalasi composer versi produksi lokal untuk memastikan folder `vendor` bersih dari dev-dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
4. Compress seluruh isi folder `franchise-app` menjadi satu file ZIP (misal: `project.zip`).
   * **PENTING:** Pastikan file `.env` (atau `.env.production`) dan folder `vendor` juga ikut terkompres di dalam file ZIP tersebut.
   * *Tips:* Folder `node_modules` **TIDAK PERLU** diikutkan di dalam ZIP karena ukurannya sangat besar dan tidak digunakan di server produksi.

---

## 2. Proses Upload & Ekstrak di cPanel / hPanel

Metode ini memisahkan file inti aplikasi (core) di luar folder publik demi keamanan, agar file konfigurasi database `.env` tidak dapat diakses langsung oleh publik.

1. Masuk ke **cPanel** (Rumahweb) atau **hPanel** (Hostinger) -> buka **File Manager**.
2. Di dalam direktori root hosting kamu (sejajar dengan folder `public_html`), buatlah folder baru bernama **`franchise-core`** (sehingga jalurnya menjadi `/home/username/franchise-core`).
3. Upload file `project.zip` ke dalam folder **`franchise-core`** tersebut.
4. Klik kanan file `project.zip` di File Manager, pilih **Extract** ke dalam folder `franchise-core`.

---

## 3. Pemindahan Folder Publik

Sekarang kita akan memindahkan file-file yang seharusnya diakses oleh publik ke dalam folder `public_html`.

1. Masuk ke folder `/home/username/franchise-core/public/`.
2. Pilih semua file dan folder di dalamnya (seperti `.htaccess`, `favicon.ico`, `index.php`, `robots.txt`, dan folder `build`), lalu gunakan fitur **Move** di File Manager untuk memindahkannya ke dalam folder **`public_html`** (sehingga jalurnya menjadi `/home/username/public_html/`).
3. Sekarang, folder `/home/username/franchise-core/public/` seharusnya sudah kosong.

---

## 4. Edit File `index.php` di `public_html`

Karena kita telah memisahkan file core Laravel dan file public, kita harus memberi tahu file `public_html/index.php` di mana letak file core aplikasi.

1. Masuk ke folder **`public_html`**.
2. Klik kanan file **`index.php`**, lalu klik **Edit**.
3. Cari baris berikut (biasanya di sekitar baris 34 atau di dekat `autoload.php`):
   ```php
   require __DIR__.'/../vendor/autoload.php';
   ```
   Ubah menjadi:
   ```php
   require __DIR__.'/../franchise-core/vendor/autoload.php';
   ```
4. Cari baris berikut (biasanya di sekitar baris 47 atau di dekat `bootstrap/app.php`):
   ```php
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   Ubah menjadi:
   ```php
   $app = require_once __DIR__.'/../franchise-core/bootstrap/app.php';
   ```
5. Simpan perubahan file tersebut.

---

## 5. Konfigurasi Database `.env`

1. Masuk ke folder **`franchise-core`**.
2. Cari file `.env` (atau rename `.env.production` menjadi `.env`).
3. Klik kanan dan **Edit** file `.env` tersebut. Sesuaikan kredensial database MySQL baru yang sudah kamu buat di cPanel/hPanel:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domainkamu.com  # Ganti dengan domain kamu

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=username_nama_database  # Nama database dari cPanel
   DB_USERNAME=username_user_database  # Username database dari cPanel
   DB_PASSWORD="password_database_kamu" # Password database dari cPanel
   ```
4. Simpan file tersebut.

---

## 6. Jalankan Migrasi & Database Seeder (Tanpa SSH)

Karena hosting murah tidak memiliki SSH, kamu tidak bisa menjalankan perintah `php artisan migrate`. Sebagai gantinya, saya sudah membuat **route web otomatis** yang bisa kamu jalankan sekali melalui browser.

1. Buka browser kamu.
2. Akses alamat URL berikut:
   ```
   https://domainkamu.com/run-migration-temp-abc123xyz
   ```
3. Tunggu hingga layar menampilkan pesan **"Migrasi & Seeding sukses!"**.
4. **PENTING / SECURITY WARNING:** Setelah sukses, demi alasan keamanan database kamu, silakan edit kembali file `franchise-core/routes/web.php` di File Manager cPanel, lalu hapus atau comment out kode route `/run-migration-temp-abc123xyz` tersebut.

---

## 7. Folder Permission & Storage Link (Upload Gambar/MOU)

Shared hosting memerlukan permission write untuk folder storage agar aplikasi bisa menyimpan session, cache, dan file upload.

1. Di File Manager cPanel, klik kanan folder **`franchise-core/storage`** -> pilih **Permissions** -> atur menjadi `775` (atau `755` jika `775` dilarang oleh hosting).
2. Lakukan hal yang sama untuk folder **`franchise-core/bootstrap/cache`** -> atur menjadi `775`.
3. **Membuat Symlink Storage (Penting untuk file upload MOU/Gambar):**
   Karena cPanel tidak ada SSH, gunakan fitur **Cron Jobs** di cPanel/hPanel kamu:
   * Masuk ke menu **Cron Jobs** di cPanel/hPanel.
   * Tambahkan tugas baru yang diatur berjalan sekali saja (misal pilih per menit, tapi nanti langsung dihapus setelah jalan sekali).
   * Pada kolom command, masukkan perintah ini:
     ```bash
     ln -s /home/username/franchise-core/storage/app/public /home/username/public_html/storage
     ```
     *(Ganti `username` sesuai dengan nama user cPanel kamu, kamu bisa melihat absolute path ini di pojok kiri atas File Manager cPanel)*.
   * Klik **Add New Cron Job**.
   * Tunggu 1 menit, setelah symlink terbentuk di folder `public_html/storage`, hapus kembali cron job tersebut agar tidak berjalan berulang-ulang.
