# 📚 NeoPustaka - Sistem Informasi Manajemen Perpustakaan

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-F5788D?style=for-the-badge&logo=chart.js&logoColor=white)

**NeoPustaka** adalah aplikasi perpustakaan digital berbasis web modern yang dirancang untuk memenuhi kebutuhan tugas akhir Praktikum Pemrograman Web. Sistem ini mengelola sirkulasi buku secara komprehensif mulai dari peminjaman, pengembalian, denda otomatis, hingga sistem reservasi cerdas.

---

## 🚀 Fitur Unggulan (Advanced Features)

Aplikasi ini dilengkapi dengan fitur-fitur canggih untuk meningkatkan efisiensi dan pengalaman pengguna:

### 1. Sistem Sirkulasi Cerdas 🔄
- **Denda Otomatis & Real-time:** Sistem menghitung keterlambatan dan nominal denda secara otomatis (On-the-fly) menggunakan logika tanggal yang presisi (`startOfDay`), memastikan perhitungan adil tanpa selisih jam.
- **Blokir Otomatis (Auto-Block):** Mahasiswa yang memiliki denda tertunggak atau masih memegang buku yang sudah lewat jatuh tempo (overdue) **DIBLOKIR** secara otomatis dari peminjaman baru.
- **Perpanjangan (Renew):** Mahasiswa dapat memperpanjang masa pinjam (+3 hari) secara mandiri selama belum lewat jatuh tempo.

### 2. Sistem Reservasi Buku (Book Queue) 🎫
- **Antrean Otomatis:** Jika stok buku habis, tombol "Pinjam" berubah menjadi "Reservasi".
- **Notifikasi Ketersediaan:** Saat buku dikembalikan oleh peminjam lain atau stok ditambah oleh Admin, sistem otomatis mendeteksi antrean reservasi dan mengirim notifikasi: *"Buku yang Anda reservasi sudah tersedia!"*.

### 3. Notifikasi & Rekomendasi 🔔
- **Notification System:** Notifikasi real-time tersimpan di database untuk setiap aktivitas (Pinjam, Kembali, Denda, Pengingat).
- **Pengingat Jatuh Tempo:** Pegawai dapat memicu pengiriman notifikasi massal (H-1 dan H-0) kepada mahasiswa yang akan jatuh tempo.
- **Rekomendasi Personal:** Dashboard mahasiswa menampilkan rekomendasi buku berdasarkan kategori buku terakhir yang dipinjam.

### 4. Laporan & Analitik (Visual Data) 📊
- Dashboard Admin dilengkapi dengan **Grafik Batang (Chart.js)** yang memvisualisasikan statistik peminjaman per kategori buku.
- Laporan buku terpopuler (Top 5) dan ringkasan keuangan denda.

---

## 👥 Hak Akses Pengguna (User Roles)

| Role | Deskripsi Hak Akses |
| :--- | :--- |
| **👮‍♂️ Admin** | Mengelola User (CRUD), Mengelola Buku (CRUD), Melihat Laporan Analitik & Grafik, Menghapus Data Sensitif. |
| **👩‍💼 Pegawai** | Mengelola Sirkulasi (Konfirmasi Kembali), Cek & Lunasi Denda, Update Stok Buku, Mengirim Pengingat Jatuh Tempo. |
| **🎓 Mahasiswa** | Meminjam Buku, Reservasi, Perpanjang (Renew), Review & Rating, Melihat Riwayat & Status Denda, Edit Profil. |
| **👤 Guest** | Melihat Katalog Buku, Mencari Buku (Search/Filter), Melihat Detail Buku (Tanpa Login). |

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** Laravel 11 (PHP Framework)
- **Authentication:** Laravel Breeze
- **Database:** MySQL
- **Frontend:** Blade Templates + Tailwind CSS + Alpine.js
- **Visualization:** Chart.js
- **Alerts:** SweetAlert2

---

## ⚙️ Panduan Instalasi (Cara Menjalankan)

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

### 1. Clone Repository
```bash
git clone [https://github.com/MahesaPutriLukman/manajemen-perpustakaan-web.git]
cd nama-folder-project
```

### 2. Install Dependencies
Pastikan Anda sudah menginstall PHP, Composer, dan Node.js.
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` dan atur database.
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan konfigurasi database Anda:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Key & Database Seeding
Jalankan perintah ini untuk membuat tabel dan mengisi data dummy (Akun Admin, Pegawai, Buku, dll).
```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
```

### 5. Jalankan Server
```bash
php artisan serve
```
Buka browser dan akses: `http://127.0.0.1:8000`

## 🔑 Akun Demo (Untuk Pengujian)

Gunakan akun-akun berikut untuk menguji setiap role:

**(Role, E-mail, Password)**
- (Admin, admin@library.com, abc)
- (Pegawai, petugas@library.com, def)
- (Mahasiswa, mahasiswa@unhas.ac.id, ghi)

## 🧪 Skenario Pengujian (Flow Testing)

Untuk melihat fitur unggulan, silakan coba alur berikut:
1. **Skenario Denda:** Login Mahasiswa -> Pinjam Buku -> Ubah tanggal `due_date` di database (mundurkan 1 hari) -> Login Pegawai -> Konfirmasi Kembali. (Denda akan terhitung otomatis).

2. **Skenario Reservasi:** Login Admin -> Ubah Stok Buku jadi 0 -> Login Mahasiswa -> Cek Detail Buku (Tombol jadi "Reservasi") -> Klik Reservasi -> Login Admin -> Tambah Stok -> Cek Notifikasi Mahasiswa.

3. **Skenario Blokir:** Pastikan Mahasiswa punya denda "Belum Lunas" -> Coba Pinjam Buku Baru -> Sistem akan menolak.


© 2025 - Tugas Final Praktikum Pemrograman Web