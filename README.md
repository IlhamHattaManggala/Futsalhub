# 🏆 FutsalHub — Sistem Informasi Manajemen Tim Futsal Multi-Tenant SaaS

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 📌 Pengenalan FutsalHub

**FutsalHub** adalah platform manajemen tim futsal berbasis web yang dibangun dengan arsitektur **Software as a Service (SaaS) Multi-Tenant**. Ini memungkinkan berbagai tim futsal untuk menggunakan satu sistem secara bersamaan dengan data yang terisolasi. FutsalHub bertujuan menggantikan proses manajemen tim futsal manual yang tidak efisien (misalnya grup WhatsApp, spreadsheet) dengan satu aplikasi terpusat yang mudah diakses kapan saja dan di mana saja.

## ✨ Fitur Utama

*   **Multi-Tenancy SaaS Ringan:** Setiap tim mendapatkan lingkungan data yang terisolasi dengan URL unik (`/v1/{slug}`).
*   **Papan Taktik Interaktif:** Pelatih dapat merancang skema formasi di lapangan futsal 2D menggunakan fitur drag-and-drop dan drawing tools. Taktik disimpan sebagai JSON.
*   **Otomatisasi Absensi & Iuran Terpadu:** Memungkinkan pencatatan kehadiran pemain dan verifikasi pembayaran iuran kegiatan dalam satu alur kerja yang efisien.
*   **Manajemen Roster Tim:** Kelola pemain dan pelatih, termasuk pendaftaran anggota baru dan pembuatan akun otomatis.
*   **Statistik & Leaderboard Real-Time:** Otomatis merangkum statistik performa individu pemain (gol, assist, dll.) dan menampilkannya di leaderboard.
*   **Modul Kas Keuangan:** Pencatatan transparan pemasukan dan pengeluaran tim.
*   **Integrasi TriPay Payment Gateway:** Memproses upgrade paket tim ke Premium secara otomatis via QRIS atau Virtual Account dengan sistem callback webhook.

## 👥 Sistem Peran (Role-Based Access Control)

FutsalHub menerapkan empat peran utama untuk mengelola akses dan fungsionalitas:

| Role          | Deskripsi                                                                 |
| :------------ | :------------------------------------------------------------------------ |
| **Superadmin**    | Mengelola seluruh platform secara global: tim, pengguna, dan transaksi premium.   |
| **Management** | Manajer/pemilik tim, mengelola roster, keuangan, dan agenda tim.           |
| **Coach**     | Pelatih, fokus pada taktik, statistik pertandingan, dan jadwal tim.          |
| **Player**    | Pemain, akses informasi tim (read-only), konfirmasi absensi, dan unggah bukti bayar. |

## 🛠️ Tech Stack

*   **Backend:** Laravel 13 (PHP Framework)
*   **Frontend:** Blade Template + Tailwind CSS + Vanilla JS
*   **Database:** MySQL
*   **Payment Gateway:** TriPay (QRIS & Virtual Account)
*   **Fitur Tambahan:** PWA (Progressive Web App), Web Push Notification, Google OAuth Login, Chart.js

## 🚀 Memulai Proyek

1.  Clone repository ini:
    ```bash
    git clone https://github.com/IlhamHattaManggala/Laravel-Futsal.git
    cd Laravel-Futsal
    ```
2.  Install dependensi Composer:
    ```bash
    composer install
    ```
3.  Salin file konfigurasi `.env` dan buat app key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
4.  Konfigurasi database di file `.env` Anda.
5.  Jalankan migrasi database:
    ```bash
    php artisan migrate --seed
    ```
6.  Jalankan aplikasi:
    ```bash
    php artisan serve
    ```
    Aplikasi akan tersedia di `http://127.0.0.1:8000`.

### Agentic Development

FutsalHub, dibangun dengan Laravel, sangat cocok untuk pengembangan dengan bantuan AI. Instal [Laravel Boost](https://laravel.com/docs/ai) untuk mempercepat alur kerja AI Anda:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost menyediakan lebih dari 15 alat dan keterampilan yang membantu agen AI membangun aplikasi Laravel sesuai praktik terbaik.

## 🤝 Kontribusi

Terima kasih atas pertimbangan Anda untuk berkontribusi pada kerangka kerja Laravel! Panduan kontribusi dapat ditemukan di [dokumentasi Laravel](https://laravel.com/docs/contributions).

## 📄 Code of Conduct

Untuk memastikan komunitas Laravel menyambut semua orang, harap tinjau dan patuhi [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## 🔒 Kerentanan Keamanan

Jika Anda menemukan kerentanan keamanan dalam Laravel, harap kirim email ke Taylor Otwell melalui [taylor@laravel.com](mailto:taylor@laravel.com). Semua kerentanan keamanan akan segera ditangani.

## 📜 Lisensi

Kerangka kerja Laravel adalah perangkat lunak sumber terbuka yang dilisensikan di bawah [lisensi MIT](https://opensource.org/licenses/MIT).
