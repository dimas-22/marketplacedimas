<?php
require __DIR__ . '/includes/init.php';
$id = (int)($_GET['id'] ?? 0);
$st = $db->prepare("SELECT p.*, k.nama_kategori, u.nama AS penjual FROM produk p
    JOIN kategori k ON k.id_kategori = p.id_kategori
    JOIN users u ON u.id_user = p.id_penjual WHERE p.id_produk = ?");
$st->bind_param('i', $id);
$st->execute();
$p = $st->get_result()->fetch_assoc();
if (!$p) { http_response_code(404); exit('Produk tidak ditemukan.'); }
$judul = $p['nama_produk'];
include __DIR__ . '/includes/header.php';
?>
<nav aria-label="breadcrumb"><ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="index.php">Produk</a></li>
  <li class="breadcrumb-item active"><?= e($p['nama_produk']) ?></li>
</ol></nav>
<div class="row g-4">
  <div class="col-md-5"><div class="card"><?= gambar_html($p['gambar'], 320) ?></div></div>
  <div class="col-md-7">
    <span class="badge bg-info text-dark"><?= e($p['nama_kategori']) ?></span>
    <h3 class="mt-2"><?= e($p['nama_produk']) ?></h3>
    <h4 class="text-primary"><?= rupiah($p['harga']) ?></h4>
    <p class="text-muted">Penjual: <?= e($p['penjual']) ?> &middot; Stok: <?= (int)$p['stok'] ?></p>
    <p><?= nl2br(e($p['deskripsi'])) ?></p>
    <?php if ($p['stok'] > 0): ?>
    <form method="post" action="keranjang.php" class="row g-2 align-items-end">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="tambah">
      <input type="hidden" name="id" value="<?= $p['id_produk'] ?>">
      <div class="col-auto"><label class="form-label">Jumlah</label>
        <input type="number" name="jumlah" value="1" min="1" max="<?= (int)$p['stok'] ?>" class="form-control" style="width:100px"></div>
      <div class="col-auto"><button class="btn btn-primary">+ Tambah ke Keranjang</button></div>
    </form>
    <?php else: ?><div class="alert alert-warning">Stok habis.</div><?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
