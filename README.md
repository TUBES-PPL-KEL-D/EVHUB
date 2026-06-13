# EVHub - Ekosistem Infrastruktur SPKLU Terintegrasi

EVHub adalah platform berbasis web yang dirancang untuk menjembatani dan mengoptimalkan interaksi antara Pengendara Kendaraan Listrik (Rider), Penyedia Infrastruktur (Vendor), dan Pengelola Platform (Admin). Sistem ini menyediakan solusi *end-to-end* mulai dari pemetaan stasiun pengisian daya, manajemen antrean digital, pencocokan perangkat keras, hingga rekonsiliasi keuangan otomatis.

## Identitas Pengembang
Kelompok D - Kelas SI-47-04, S1 Sistem Informasi

**Anggota Kelompok:**
1. Wisnu Cakra P. P.
2. Fakhri M. Habibi
3. Langgeng Yongi Surya
4. Riehand Muhammad
5. M. Byan Burika
6. M. Azka As-Sidqi
7. Aimee Clarissa A. S.

---

## Fitur Utama

Sistem ini dibangun dengan mempertimbangkan tiga perspektif pengguna utama:

### 1. Modul Rider (Pengendara)
* **Smart Connector Matching:** Memvalidasi kecocokan tipe colokan stasiun pengisian dengan spesifikasi kendaraan di Garasi Digital milik pengendara secara otomatis.
* **Queueing System:** Manajemen antrean digital secara *real-time* dengan estimasi durasi pengisian.
* **EV-Pay & Transaksi Digital:** Sistem pembayaran terintegrasi yang mampu menerbitkan struk digital bersinkronisasi waktu nyata.

### 2. Modul Vendor (Mitra SPKLU)
* **Manajemen Stasiun & Mesin:** Kontrol penuh atas pengaturan jam operasional, penambahan mesin charger, dan penetapan tarif per kWh.
* **Wallet & Automated Withdrawal:** Pencatatan otomatis pendapatan operasional (Wallet History) dan kemudahan penarikan dana usaha tanpa perlu mengisi ulang data rekening.
* **Validasi Legalitas:** Keamanan pendaftaran dengan mandatori kelengkapan Nomor Rekening dan NPWP.

### 3. Modul Admin (Pengelola Platform)
* **Verifikasi Dokumen:** Otoritas untuk menyetujui atau menolak pendaftaran vendor baru berdasarkan kelengkapan legalitas.
* **Sistem Pelaporan Terpusat:** Meninjau dan memberikan umpan balik langsung terhadap tiket laporan kendala dari pengguna.
* **Audit Keuangan & Analitik:** Melakukan validasi pengajuan penarikan dana vendor serta memantau analitik tren operasional SPKLU.

---

## Panduan Instalasi & Menjalankan Aplikasi

Pastikan Anda telah menginstal PHP, Composer, Node.js, dan MySQL di sistem Anda sebelum memulai. Ikuti langkah-langkah terstruktur berikut untuk menjalankan aplikasi di lingkungan lokal:

**1. Clone Repositori**
git clone https://github.com/TUBES-PPL-KEL-D/EVHUB.git
cd evhub


**2. Instalasi Dependensi Backend (PHP/Laravel)**
composer install

**3. Instalasi Dependensi Frontend (Node/Vite)**
npm install

**4. Konfigurasi Environment**
Salin file `.env.example` menjadi `.env`.

cp .env.example .env

Buka file `.env` dan sesuaikan konfigurasi basis data Anda (pastikan Anda sudah membuat database kosong di MySQL terlebih dahulu):

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_evhub
DB_USERNAME=root
DB_PASSWORD=

**5. Generate Application Key**
php artisan key:generate

**6. Migrasi dan Seeding Basis Data**
Sistem membutuhkan *dummy data* awal untuk peran Admin, Vendor, Rider, serta master data mesin dan kendaraan.

php artisan migrate:fresh --seed

**7. Jalankan Kompilasi Aset Frontend (Terminal 1)**
npm run dev

**8. Jalankan Server Development Laravel (Terminal 2)**
php artisan serve


Aplikasi kini dapat diakses melalui browser di `http://127.0.0.1:8000`.

## Catatan Khusus untuk Pengujian (Testing)

Proyek ini dilengkapi dengan serangkaian *Test Case* fungsional. Karena sistem sangat bergantung pada ketersediaan data *seeder* yang spesifik untuk setiap alur transaksi, status antrean, dan saldo simulasi, terdapat aturan wajib yang harus diikuti saat menjalankan pengujian:

**PENTING:** Anda diwajibkan untuk menjalankan perintah migrasi ulang beserta *seeder* **sebelum menjalankan setiap pengujian individual (per test case)**.

Gunakan perintah ini setiap kali sebelum mengetes fitur baru:

php artisan migrate:fresh --seed

Tindakan ini mencegah terjadinya *error* akibat data *seeder* yang habis atau status data yang sudah berubah karena pengujian pada skenario sebelumnya.
