<?php
error_reporting(E_ALL);
include_once 'koneksi.php';

$errors = [];

if (isset($_POST['submit'])) {
    $id         = (int)($_POST['id'] ?? 0);
    $nama       = trim($_POST['nama'] ?? '');
    $kategori   = $_POST['kategori'] ?? '';
    $harga_beli = $_POST['harga_beli'] ?? '';
    $harga_jual = $_POST['harga_jual'] ?? '';
    $stok       = $_POST['stok'] ?? '';

    if ($nama === '') $errors[] = 'Nama barang wajib diisi.';
    foreach (['harga_beli' => 'Harga beli', 'harga_jual' => 'Harga jual', 'stok' => 'Stok'] as $k => $label) {
        if (!is_numeric($$k) || $$k < 0) $errors[] = "$label harus berupa angka 0 atau lebih.";
    }

    if (!$errors) {
        $beli = (int)$harga_beli;
        $jual = (int)$harga_jual;
        $st   = (int)$stok;
        $gambar = upload_gambar($_FILES['file_gambar'] ?? null);

        if ($gambar) {
            $stmt = mysqli_prepare($conn,
                'UPDATE data_barang SET nama=?, kategori=?, harga_jual=?, harga_beli=?, stok=?, gambar=? WHERE id_barang=?');
            mysqli_stmt_bind_param($stmt, 'ssiiisi', $nama, $kategori, $jual, $beli, $st, $gambar, $id);
        } else {
            $stmt = mysqli_prepare($conn,
                'UPDATE data_barang SET nama=?, kategori=?, harga_jual=?, harga_beli=?, stok=? WHERE id_barang=?');
            mysqli_stmt_bind_param($stmt, 'ssiiii', $nama, $kategori, $jual, $beli, $st, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?msg=ubah');
            exit;
        }
        $errors[] = 'Gagal menyimpan ke database: ' . mysqli_error($conn);
    }
    // Jika gagal validasi, tampilkan kembali isian pengguna
    $data = ['id_barang' => $id, 'nama' => $nama, 'kategori' => $kategori,
             'harga_beli' => $harga_beli, 'harga_jual' => $harga_jual, 'stok' => $stok, 'gambar' => ''];
} else {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = mysqli_prepare($conn, 'SELECT * FROM data_barang WHERE id_barang = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$data) {
        header('Location: index.php');
        exit;
    }
}
$judul_tombol = 'Simpan perubahan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-in">
        <span class="brand"><span class="brand-mark"></span>Gudang Praktikum</span>
        <span class="topbar-note">Lab 8 · PHP &amp; MySQL</span>
    </div>
</header>
<main class="wrap wrap-narrow">
    <a href="index.php" class="back">← Kembali ke daftar</a>
    <div class="page-head"><div><h1>Ubah Barang</h1><p class="sub">Perbarui data barang, lalu simpan.</p></div></div>
    <div class="panel panel-pad">
        <?php include 'form_barang.php'; ?>
    </div>
</main>
</body>
</html>
