<?php
error_reporting(E_ALL);
include_once 'koneksi.php';

$errors = [];
$data = ['nama' => '', 'kategori' => 'Elektronik', 'harga_beli' => '', 'harga_jual' => '', 'stok' => '', 'gambar' => ''];

if (isset($_POST['submit'])) {
    $data['nama']       = trim($_POST['nama'] ?? '');
    $data['kategori']   = $_POST['kategori'] ?? '';
    $data['harga_beli'] = $_POST['harga_beli'] ?? '';
    $data['harga_jual'] = $_POST['harga_jual'] ?? '';
    $data['stok']       = $_POST['stok'] ?? '';

    if ($data['nama'] === '') $errors[] = 'Nama barang wajib diisi.';
    foreach (['harga_beli' => 'Harga beli', 'harga_jual' => 'Harga jual', 'stok' => 'Stok'] as $k => $label) {
        if (!is_numeric($data[$k]) || $data[$k] < 0) $errors[] = "$label harus berupa angka 0 atau lebih.";
    }

    if (!$errors) {
        $gambar = upload_gambar($_FILES['file_gambar'] ?? null);
        $beli = (int)$data['harga_beli'];
        $jual = (int)$data['harga_jual'];
        $stok = (int)$data['stok'];

        $stmt = mysqli_prepare($conn,
            'INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssiiis', $data['nama'], $data['kategori'], $jual, $beli, $stok, $gambar);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?msg=tambah');
            exit;
        }
        $errors[] = 'Gagal menyimpan ke database: ' . mysqli_error($conn);
    }
}
$judul_tombol = 'Simpan barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Barang</title>
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
    <div class="page-head"><div><h1>Tambah Barang</h1><p class="sub">Isi data barang baru di bawah ini.</p></div></div>
    <div class="panel panel-pad">
        <?php include 'form_barang.php'; ?>
    </div>
</main>
</body>
</html>
