# 📚 Sistem Informasi Manajemen Perpustakaan (Library Management System)

Project Final Praktikum Pemrograman Web 2025.
Aplikasi perpustakaan digital berbasis web yang modern, mencakup pengelolaan sirkulasi buku, denda otomatis, notifikasi sistem, dan reservasi buku.

## 🚀 Fitur Unggulan (Advanced Features)

### 1. Sistem Cerdas
- **Denda Otomatis & Real-time:** Menghitung denda keterlambatan secara otomatis saat buku dikembalikan, tanpa perlu input manual.
- **Blokir Peminjaman:** Mahasiswa dengan denda tertunggak atau buku yang sedang telat (overdue) otomatis diblokir dari peminjaman baru.
- **Rekomendasi Personal:** Dashboard mahasiswa menampilkan rekomendasi buku berdasarkan kategori yang terakhir dipinjam.

### 2. Manajemen Sirkulasi Lengkap
- **Reservasi Buku:** Jika stok buku habis, tombol "Pinjam" berubah menjadi "Reservasi". Notifikasi dikirim otomatis saat buku tersedia.
- **Pengingat Jatuh Tempo:** Sistem dapat mengirim notifikasi pengingat H-1 dan H-0 kepada mahasiswa (via Scheduler atau Trigger Manual Pegawai).
- **Perpanjangan (Renew):** Mahasiswa dapat memperpanjang masa pinjam (+3 hari) jika belum jatuh tempo.

### 3. Multi-Role User
- **Admin:** Manajemen User, Buku, dan Laporan Statistik Visual.
- **Pegawai:** Sirkulasi (Pinjam/Kembali), Cek Denda, Kelola Stok.
- **Mahasiswa:** Dashboard Peminjaman, Riwayat, Notifikasi, Review Buku.
- **Guest:** Katalog Buku & Pencarian (Search/Filter).

---

## 🛠️ Teknologi yang Digunakan
- **Framework:** Laravel 11
- **Authentication:** Laravel Breeze
- **Database:** MySQL
- **Frontend:** Blade Templates + Tailwind CSS
- **Visualization:** Chart.js (Untuk Grafik Admin)

---

## ⚙️ Cara Install & Menjalankan

1. **Clone Repository**
   ```bash
   git clone [https://github.com/USERNAME-KAMU/NAMA-REPO-KAMU.git](https://github.com/USERNAME-KAMU/NAMA-REPO-KAMU.git)
   cd nama-folder-project
   ```

2. **Install Dependency**
    ```bash
    composer install
    npm install && npm run build
    ```

3. **Setup Database**
    ```bash
    - Buat database baru di MySQL (misal: db_perpustakaan).
    - Copy file .env.example menjadi .env.
    - Sesuaikan konfigurasi database di .env.
    ```
    
4. **Migrate & Seed**
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```

5. **Link Storage**
    ```bash
    php artisan storage:link
    ```

6. **Jalankan Server**
    ```bash
    php artisan serve
    ```