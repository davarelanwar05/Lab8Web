<?php
include_once 'koneksi.php';

// ----- Pencarian -----
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = mysqli_prepare($conn, 'SELECT * FROM data_barang WHERE nama LIKE ? OR kategori LIKE ? ORDER BY id_barang DESC');
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, 'SELECT * FROM data_barang ORDER BY id_barang DESC');
}

// ----- Ringkasan -----
$ringkas = mysqli_fetch_assoc(mysqli_query($conn,
    'SELECT COUNT(*) AS jenis, COALESCE(SUM(stok),0) AS total_stok,
            COALESCE(SUM(harga_jual*stok),0) AS nilai FROM data_barang'));

// ----- Pesan setelah aksi -----
$pesan = [
    'tambah' => 'Barang baru berhasil ditambahkan.',
    'ubah'   => 'Perubahan barang berhasil disimpan.',
    'hapus'  => 'Barang berhasil dihapus.',
];
$flash = $pesan[$_GET['msg'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
    <div class="wrap topbar-in">
        <span class="brand"><span class="brand-mark"></span>Gudang Praktikum</span>
        <span class="topbar-note">Lab 8 · PHP &amp; MySQL</span>
    </div>
</header>

<main class="wrap">
    <div class="page-head">
        <div>
            <h1>Data Barang</h1>
            <p class="sub">Kelola stok, harga, dan foto barang dalam satu tempat.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">+ Tambah barang</a>
    </div>

    <?php if ($flash): ?><div class="alert alert-ok"><?= e($flash) ?></div><?php endif; ?>

    <section class="stats">
        <div><span class="stat-num"><?= (int)$ringkas['jenis'] ?></span><span class="stat-label">Jenis barang</span></div>
        <div><span class="stat-num"><?= number_format($ringkas['total_stok'], 0, ',', '.') ?></span><span class="stat-label">Total stok</span></div>
        <div><span class="stat-num"><?= rupiah($ringkas['nilai']) ?></span><span class="stat-label">Nilai stok (harga jual)</span></div>
    </section>

    <form class="search" method="get">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau kategori barang">
        <button class="btn btn-dark" type="submit">Cari</button>
        <?php if ($q !== ''): ?><a href="index.php" class="btn btn-ghost">Reset</a><?php endif; ?>
    </form>

    <div class="panel">
        <table>
            <thead>
            <tr>
                <th>Barang</th>
                <th>Kategori</th>
                <th class="num">Harga beli</th>
                <th class="num">Harga jual</th>
                <th class="num">Stok</th>
                <th class="num">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $stok = (int)$row['stok'];
                    $cls  = $stok === 0 ? 'stok-habis' : ($stok <= 2 ? 'stok-tipis' : 'stok-aman');
                    $lbl  = $stok === 0 ? 'Habis' : ($stok <= 2 ? 'Menipis' : 'Aman');
                    ?>
                    <tr>
                        <td data-label="Barang">
                            <div class="item">
                                <?php if (!empty($row['gambar'])): ?>
                                    <img class="thumb" src="gambar/<?= e($row['gambar']) ?>" alt="<?= e($row['nama']) ?>"
                                         onerror="this.outerHTML='<span class=&quot;thumb thumb-empty&quot;><?= e(strtoupper(substr($row['nama'], 0, 1))) ?></span>'">
                                <?php else: ?>
                                    <span class="thumb thumb-empty"><?= e(strtoupper(substr($row['nama'], 0, 1))) ?></span>
                                <?php endif; ?>
                                <span class="item-name"><?= e($row['nama']) ?></span>
                            </div>
                        </td>
                        <td data-label="Kategori"><span class="tag"><?= e($row['kategori']) ?></span></td>
                        <td data-label="Harga beli" class="num"><?= rupiah($row['harga_beli']) ?></td>
                        <td data-label="Harga jual" class="num strong"><?= rupiah($row['harga_jual']) ?></td>
                        <td data-label="Stok" class="num"><span class="stok <?= $cls ?>"><?= $stok ?> · <?= $lbl ?></span></td>
                        <td data-label="Aksi" class="num">
                            <a class="link" href="ubah.php?id=<?= (int)$row['id_barang'] ?>">Ubah</a>
                            <a class="link link-danger" href="hapus.php?id=<?= (int)$row['id_barang'] ?>"
                               onclick="return confirm('Hapus &quot;<?= e(addslashes($row['nama'])) ?>&quot;? Tindakan ini tidak bisa dibatalkan.')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr class="empty-row">
                    <td colspan="6">
                        <div class="empty">
                            <strong><?= $q !== '' ? 'Tidak ada barang yang cocok.' : 'Belum ada barang.' ?></strong>
                            <span><?= $q !== '' ? 'Coba kata kunci lain.' : 'Tambahkan barang pertama untuk mulai.' ?></span>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
