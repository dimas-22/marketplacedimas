# Marketplace Sederhana (PHP Native + MySQLi + Bootstrap)

## Fitur
Daftar produk, detail produk, CRUD produk (penjual), keranjang belanja, checkout & simpan pesanan, riwayat pesanan, login/register.

## Instalasi lokal (XAMPP/Laragon)
1. Buat database `marketplace`, lalu impor `schema.sql`.
2. Salin `config.example.php` menjadi `config.php`, isi `DB_HOST=localhost`, `DB_USER=root`, `DB_PASS=''`, `DB_NAME=marketplace`, `BASE_URL='/marketplace/'` (sesuai folder).
3. Buka `http://localhost/marketplace/`.

## Deploy InfinityFree
1. Daftar di infinityfree.com, buat akun hosting + subdomain.
2. Control Panel → *MySQL Databases* → buat database; catat hostname, username, password, nama DB.
3. *phpMyAdmin* → pilih database → tab Import → unggah `schema.sql`.
4. Buat `config.php` (dari `config.example.php`) berisi kredensial tadi, `BASE_URL='/'`.
5. Unggah semua file ke folder `htdocs` via File Manager/FTP (**termasuk config.php, tapi config.php TIDAK ada di GitHub**).
6. Pastikan folder `uploads/` dapat ditulis (permission 755/775).

## Alur uji
Daftar sebagai **penjual** → tambah produk → keluar → daftar sebagai **pembeli** → tambah ke keranjang → checkout → cek tabel `pesanan`, `detail_pesanan`, dan stok di phpMyAdmin.

## Keamanan
`config.php` di-ignore oleh Git. Jangan commit password database/hosting.
# marketplacePasarKita
