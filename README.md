# E-Learning Kampus

Aplikasi E-Learning Kampus dibangun dengan Laravel dan menggunakan Laravel Sanctum untuk autentikasi. Sistem ini mendukung dua peran pengguna, yaitu Mahasiswa dan Dosen.

## Instalasi

1. Clone repository ini.
2. Jalankan `composer install` untuk menginstal dependensi.
3. Buat file `.env` dan sesuaikan dengan konfigurasi database Anda:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database
    DB_USERNAME=username_database
    DB_PASSWORD=password_database
    ```
4. Konfigurasi pengaturan email di file `.env` Anda:
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.example.com
    MAIL_PORT=587
    MAIL_USERNAME=your_email@example.com
    MAIL_PASSWORD=your_email_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=your_email@example.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```
5. Jalankan `php artisan migrate` untuk menjalankan migrasi.
6. Jalankan `php artisan serve` untuk menjalankan server.
7. Buka `http://127.0.0.1:8000/api/documentation` untuk melihat dokumentasi.

## Endpoint

-   **Autentikasi**

    -   POST `/register` - Registrasi pengguna.
    -   POST `/login` - Login dan mendapatkan token.
    -   POST `/logout` - Logout dan revoke token.

-   **Mata Kuliah**

    -   GET `/courses` - Menampilkan semua mata kuliah.
    -   POST `/courses` - Menambahkan mata kuliah (Dosen).
    -   PUT `/courses/{id}` - Mengedit mata kuliah (Dosen).
    -   DELETE `/courses/{id}` - Menghapus mata kuliah (Dosen).
    -   POST `/courses/{id}/enroll` - Mendaftar ke mata kuliah (Mahasiswa).

-   **Materi**

    -   POST `/materials` - Mengupload materi (Dosen).
    -   GET `/materials/{id}/download` - Mengunduh materi (Mahasiswa).

-   **Tugas**

    -   POST `/assignments` - Membuat tugas (Dosen).
    -   POST `/submissions` - Mengunggah jawaban (Mahasiswa).
    -   POST `/submissions/{id}/grade` - Memberi nilai (Dosen).

-   **Forum Diskusi**

    -   POST `/discussions` - Membuat diskusi (Mahasiswa & Dosen).
    -   POST `/discussions/{id}/replies` - Membalas diskusi (Mahasiswa & Dosen).

-   **Laporan & Statistik**
    -   GET `/reports/courses` - Statistik jumlah mahasiswa per mata kuliah.
    -   GET `/reports/assignments` - Statistik tugas yang sudah/belum dinilai.
    -   GET `/reports/students/{id}` - Statistik tugas dan nilai mahasiswa tertentu.
