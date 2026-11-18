
<?php

// ==========================================
// KONFIGURASI DATABASE INFINITYFREE
// ==========================================

define('DB_HOST', 'sql306.infinityfree.com');
define('DB_USER', 'if0_43469311');
define('DB_PASS', 'n1NbsMVRTbjJeof');
define('DB_NAME', 'if0_43469311_schema');
define('DB_PORT', 3306);

// ==========================================
// KONFIGURASI WEBSITE
// ==========================================

define('BASE_URL', 'https://marketplacedimas.xo.je/');

// ==========================================
// KONEKSI DATABASE
// ==========================================

$db = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    DB_PORT
);

// ==========================================
// CEK KONEKSI
// ==========================================

if ($db->connect_error) {
    die('Koneksi database gagal: ' . $db->connect_error);
}

$db->set_charset('utf8mb4');

