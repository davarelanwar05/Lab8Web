<?php
// Form bersama untuk tambah.php dan ubah.php
// Variabel: $data (array), $errors (array), $judul_tombol
$kategori_list = ['Komputer', 'Elektronik', 'Hand Phone'];
?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>Data belum bisa disimpan:</strong>
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="form">
    <?php if (!empty($data['id_barang'])): ?>
        <input type="hidden" name="id" value="<?= (int)$data['id_barang'] ?>">
    <?php endif; ?>

    <div class="field span-2">
        <label for="nama">Nama barang</label>
        <input type="text" id="nama" name="nama" value="<?= e($data['nama']) ?>" placeholder="Contoh: HP Samsung Android" required>
    </div>

    <div class="field">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
            <?php foreach ($kategori_list as $k): ?>
                <option value="<?= e($k) ?>" <?= $data['kategori'] === $k ? 'selected' : '' ?>><?= e($k) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field">
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" min="0" value="<?= e($data['stok']) ?>" placeholder="0" required>
    </div>

    <div class="field">
        <label for="harga_beli">Harga beli (Rp)</label>
        <input type="number" id="harga_beli" name="harga_beli" min="0" value="<?= e($data['harga_beli']) ?>" placeholder="0" required>
    </div>

    <div class="field">
        <label for="harga_jual">Harga jual (Rp)</label>
        <input type="number" id="harga_jual" name="harga_jual" min="0" value="<?= e($data['harga_jual']) ?>" placeholder="0" required>
    </div>

    <div class="field span-2">
        <label for="file_gambar">Foto barang</label>
        <div class="upload">
            <?php if (!empty($data['gambar'])): ?>
                <img src="gambar/<?= e($data['gambar']) ?>" alt="Foto saat ini" class="upload-preview" id="preview">
            <?php else: ?>
                <img src="" alt="" class="upload-preview" id="preview" hidden>
            <?php endif; ?>
            <div>
                <input type="file" id="file_gambar" name="file_gambar" accept="image/*">
                <p class="hint">JPG, PNG, GIF, atau WEBP.<?= !empty($data['id_barang']) ? ' Kosongkan jika tidak ingin mengganti foto.' : '' ?></p>
            </div>
        </div>
    </div>

    <div class="actions span-2">
        <button type="submit" name="submit" class="btn btn-primary"><?= e($judul_tombol) ?></button>
        <a href="index.php" class="btn btn-ghost">Batal</a>
    </div>
</form>

<script>
// Pratinjau foto sebelum diunggah
document.getElementById('file_gambar').addEventListener('change', function () {
    var p = document.getElementById('preview');
    if (this.files && this.files[0]) {
        p.src = URL.createObjectURL(this.files[0]);
        p.hidden = false;
    }
});
</script>
