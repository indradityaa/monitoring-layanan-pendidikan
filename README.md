# Sistem Informasi Monitoring dan Pelaporan Kegiatan Layanan Pendidikan

## Deskripsi

Aplikasi web untuk membantu pengelolaan dan monitoring kegiatan layanan pendidikan, termasuk pencatatan kegiatan, penjadwalan, penugasan petugas, dan pengelolaan laporan kegiatan.

## Tujuan

Mendukung pengelolaan informasi kegiatan layanan pendidikan agar data kegiatan, jadwal, penugasan, dan laporan dapat dikelola secara terstruktur.

## Fitur Utama

### Admin

- Mengelola data kegiatan.
- Mengelola jadwal kegiatan.
- Mengelola penugasan petugas.
- Memeriksa, memvalidasi, dan meminta revisi laporan.
- Mengelola jenis kegiatan dan lokasi.
- Melihat dashboard monitoring.

### Petugas

- Melihat informasi kegiatan dan jadwal sesuai penugasan.
- Membuat dan mengelola laporan kegiatan.
- Mengirim laporan untuk validasi.
- Memperbaiki dan mengirim ulang laporan yang diminta revisi.

### Pimpinan

- Melihat dashboard monitoring kegiatan.
- Melihat ringkasan dan grafik informasi kegiatan.

## Teknologi

- PHP dan Laravel
- MySQL
- Blade
- Tailwind CSS
- Vite

## Persyaratan

- PHP dan ekstensi yang dibutuhkan oleh proyek.
- Composer.
- Node.js dan npm.
- MySQL.
- Web browser.

Gunakan versi perangkat lunak yang sesuai dengan konfigurasi proyek.

## Instalasi Lokal

1. Clone repository dan masuk ke folder proyek.
2. Jalankan `composer install`.
3. Buat file `.env` berdasarkan `.env.example` jika belum tersedia.
4. Atur konfigurasi database pada `.env`.
5. Jalankan `php artisan key:generate` jika aplikasi baru disiapkan.
6. Pada database baru yang memang disiapkan untuk instalasi, jalankan `php artisan migrate --seed`.
7. Jalankan `npm install`.
8. Jalankan `npm run dev` pada terminal pertama.
9. Jalankan `php artisan serve` pada terminal kedua.
10. Buka alamat lokal yang ditampilkan oleh Laravel.

Jangan menjalankan migrasi atau seeder pada database yang berisi data penting tanpa memahami dampaknya dan membuat backup terlebih dahulu.

## Pengujian

Jalankan pengujian otomatis menggunakan:

`php artisan test`

## Keamanan

- File `.env` tidak boleh diunggah ke repository.
- Jangan menyimpan password atau kredensial asli di README.
- Gunakan `APP_DEBUG=false` untuk lingkungan produksi.
- Akses fitur dibatasi berdasarkan role pengguna.

## Status Pengujian

Terakhir tercatat: 27 pengujian otomatis berhasil (27 PASS).

## Status Proyek

Sistem telah melalui pengujian fungsional manual dan pengujian otomatis. Pengembangan serta validasi lanjutan mengikuti hasil konsultasi dan kebutuhan sistem yang telah disepakati.
