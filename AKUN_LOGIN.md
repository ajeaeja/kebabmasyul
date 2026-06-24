# Daftar Akun Login Aplikasi Franchise Masyul Kebab

Berikut adalah daftar akun login (credentials) berdasarkan masing-masing role yang telah disiapkan melalui database seeder untuk memudahkan pengujian aplikasi.

---

## 🔐 Informasi Kredensial Default

| Nama Role | Email Address | Password | Deskripsi Akses / Fitur Utama |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner@masyulkebab.com` | `password` | Memiliki akses penuh terhadap seluruh sistem, melakukan persetujuan (approval) pengajuan edit data dari Admin, serta melihat laporan keuangan secara keseluruhan. |
| **Admin** | `admin@masyulkebab.com` | `password` | Mengelola data mitra, data cabang, laporan omset, pesanan bahan baku mitra, serta mengajukan edit data penting ke Owner jika ada kesalahan input. |
| **Gudang (Warehouse)** | `gudang@masyulkebab.com` | `password` | Mengelola stok bahan baku (stok masuk, safety stock) dan memproses pengiriman barang pesanan mitra (status dipacking, dikirim, dll). |

---

## 🚀 Cara Menggunakan Akun
1. Pastikan server lokal Anda sudah berjalan (misalnya `php artisan serve` pada `http://127.0.0.1:8000`).
2. Buka halaman login aplikasi di peramban Anda.
3. Masukkan salah satu **Email** di atas beserta password default: **`password`**.
4. Sistem akan otomatis mengarahkan Anda ke dashboard sesuai dengan hak akses (role) masing-masing akun tersebut.

> [!NOTE]
> Akun-akun di atas dihasilkan secara otomatis oleh file database seeder [DatabaseSeeder.php](file:///c:/laragon/www/franchise-app/database/seeders/DatabaseSeeder.php). Jika database di-reset (`php artisan migrate:fresh --seed`), data akun ini akan kembali ke kondisi semula.
