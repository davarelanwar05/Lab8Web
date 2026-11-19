<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "latihan1";

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi ke server gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
// echo "Koneksi berhasil";

/* ---------- Fungsi bantu ---------- */

// Amankan output HTML (mencegah XSS)
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Format angka ke Rupiah
function rupiah($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }

// Upload gambar. Mengembalikan nama file, atau null jika tidak ada upload
function upload_gambar($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return null;

    $folder = __DIR__ . '/gambar/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = $base . '_' . substr(uniqid(), -5) . '.' . $ext;

    return move_uploaded_file($file['tmp_name'], $folder . $filename) ? $filename : null;
}
?>
