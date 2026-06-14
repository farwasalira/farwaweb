# SIPUPUK 🌾
### Sistem Informasi Stok dan Distribusi Pupuk Bersubsidi

> Aplikasi web berbasis PHP untuk pengelolaan stok dan distribusi pupuk bersubsidi di **Desa Lumaring**.

---

## 📋 Deskripsi

**SIPUPUK** adalah sistem informasi berbasis web yang dirancang untuk membantu pengelolaan distribusi pupuk bersubsidi secara transparan dan terorganisir. Sistem ini mencakup manajemen data petani, kelompok tani, stok pupuk, alokasi, dan penyaluran pupuk, serta menyediakan halaman publik untuk masyarakat umum.

---

## ✨ Fitur Utama

### 🌐 Halaman Publik
| Halaman | Deskripsi |
|---|---|
| Beranda | Ringkasan informasi stok dan statistik distribusi |
| Stok Pupuk | Data stok pupuk terkini yang tersedia |
| Alokasi | Informasi alokasi pupuk per petani |
| Penyaluran | Riwayat penyaluran pupuk |
| Informasi | Pengumuman dan berita terbaru |

### 🔐 Panel Admin
| Modul | Deskripsi |
|---|---|
| Dashboard | Ringkasan statistik dan aktivitas terkini |
| Data Petani | Kelola data petani terdaftar (CRUD) |
| Data Pupuk | Kelola jenis dan stok pupuk (CRUD + upload foto) |
| Kelompok Tani | Kelola kelompok tani dan keanggotaan (CRUD) |
| Manajemen Stok | Catat penerimaan stok pupuk + upload bukti |
| Alokasi Pupuk | Atur kuota alokasi pupuk per petani per periode |
| Penyaluran | Catat transaksi penyaluran pupuk ke petani + bukti |
| Laporan | Cetak laporan distribusi |
| Informasi | Kelola pengumuman publik |
| Ubah Password | Ganti password akun admin |

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP (Native / Vanilla PHP, MVC-like architecture)
- **Database:** MySQL / MariaDB
- **Server:** Apache (XAMPP)
- **Frontend:** HTML5, CSS3, JavaScript
- **Charset:** UTF-8 MB4

---

## 📁 Struktur Direktori

```
webpupuk/
├── admin.php               # Router utama panel admin
├── index.php               # Router utama halaman publik
├── assets/
│   ├── css/                # File CSS
│   ├── js/                 # File JavaScript
│   └── img/                # Gambar aset
├── config/
│   └── database.php        # Konfigurasi koneksi database & session
├── controllers/
│   ├── auth.php            # Autentikasi (login/logout)
│   ├── dashboard.php       # Logika dashboard
│   ├── petani.php          # CRUD data petani
│   ├── pupuk.php           # CRUD data pupuk
│   ├── kelompok.php        # CRUD kelompok tani
│   ├── stok.php            # Manajemen stok
│   ├── alokasi.php         # Manajemen alokasi
│   ├── penyaluran.php      # Manajemen penyaluran
│   ├── laporan.php         # Cetak laporan
│   ├── informasi.php       # Kelola informasi/pengumuman
│   └── ubah_password.php   # Ganti password
├── views/
│   ├── admin/              # Template halaman admin
│   ├── publik/             # Template halaman publik
│   └── layouts/            # Layout utama (admin & publik)
├── database/
│   ├── webpupuk.sql        # Schema database + data awal (seed)
│   ├── migration.php       # Script migrasi
│   ├── constraint_migration.php
│   └── fix_duplicates.php
├── libraries/
│   └── helpers.php         # Fungsi utilitas global
└── uploads/                # Folder upload file (bukti, foto pupuk)
```

---

## ⚙️ Instalasi & Konfigurasi

### Prasyarat
- **XAMPP** (PHP 7.4+ dan MySQL/MariaDB)
- Web browser modern

### Langkah Instalasi

**1. Clone / Salin Proyek**
```bash
# Salin folder proyek ke direktori htdocs XAMPP
C:\xampp\htdocs\webpupuk\
```

**2. Import Database**

- Buka **phpMyAdmin** → `http://localhost/phpmyadmin`
- Buat database baru bernama `webpupuk` (opsional, script SQL sudah otomatis membuatnya)
- Klik **Import** → pilih file `database/webpupuk.sql`
- Klik **Go / Eksekusi**

**3. Konfigurasi Database**

Edit file `config/database.php` sesuai konfigurasi lokal Anda:

```php
$db_host = '127.0.0.1';   // Host database
$db_user = 'root';         // Username database
$db_pass = '';             // Password database
$db_name = 'webpupuk';     // Nama database
```

**4. Jalankan Aplikasi**

- Pastikan **Apache** dan **MySQL** di XAMPP sudah berjalan (hijau)
- Buka browser dan akses:
  - **Halaman Publik:** `http://localhost/webpupuk/`
  - **Panel Admin:** `http://localhost/webpupuk/admin.php`

---

## 🔑 Akun Default Admin

| Username | Password |
|----------|----------|
| `admin`  | `admin123` |

> ⚠️ **Penting:** Segera ubah password default setelah login pertama kali melalui menu **Ubah Password**.

---

## 🗄️ Skema Database

| Tabel | Keterangan |
|---|---|
| `admin` | Data akun administrator |
| `pupuk` | Jenis-jenis pupuk beserta info dan stok |
| `kelompok_tani` | Data kelompok tani |
| `petani` | Data petani yang terdaftar dalam kelompok |
| `stok` | Riwayat penerimaan stok pupuk |
| `alokasi` | Kuota alokasi pupuk per petani per periode |
| `penyaluran` | Riwayat transaksi penyaluran pupuk ke petani |
| `informasi` | Pengumuman dan informasi publik |

---

## 📦 Data Seed (Contoh Data Awal)

Saat import `webpupuk.sql`, sistem akan otomatis memuat:
- **5 jenis pupuk:** UREA, NPK PHONSKA, SP-36, ZA, ORGANIK
- **3 kelompok tani:** Makmur Jaya, Sumber Rejeki, Suka Maju
- **10 data petani** dari Desa Lumaring
- Contoh data stok, alokasi, penyaluran, dan informasi

---

## 🔒 Keamanan

- Semua input pengguna difilter menggunakan fungsi `sanitize()` dari `helpers.php`
- Password admin disimpan dengan enkripsi `bcrypt` (`password_hash`)
- Autentikasi sesi PHP digunakan untuk melindungi panel admin
- Proteksi route: halaman admin hanya dapat diakses oleh pengguna yang sudah login

---

## 📝 Catatan Pengembangan

- Proyek ini menggunakan arsitektur **MVC sederhana** berbasis native PHP tanpa framework tambahan.
- Upload file (foto pupuk, bukti penyaluran/stok) disimpan di folder `uploads/`.
- Pastikan folder `uploads/` memiliki izin tulis yang cukup.

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan pengelolaan distribusi pupuk bersubsidi **Desa Lumaring**.

---

*Dikembangkan dengan ❤️ untuk kemudahan pengelolaan distribusi pupuk bersubsidi.*
