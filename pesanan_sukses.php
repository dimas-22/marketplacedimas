<?php
require __DIR__ . '/includes/init.php';
require_login('pembeli');
$judul = 'Pesanan Berhasil';
$id = (int)($_GET['id'] ?? 0); $u = user();
$st = $db->prepare("SELECT * FROM pesanan WHERE id_pesanan = ? AND id_pembeli = ?");
$st->bind_param('ii', $id, $u['id']); $st->execute();
$ps = $st->get_result()->fetch_assoc();
if (!$ps) { http_response_code(404); exit('Pesanan tidak ditemukan.'); }
$ds = $db->prepare("SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON p.id_produk = d.id_produk WHERE d.id_pesanan = ?");
$ds->bind_param('i', $id); $ds->execute(); $detail = $ds->get_result();
include __DIR__ . '/includes/header.php';
?>
<div class="card shadow-sm"><div class="card-body">
  <h4 class="text-success">Pesanan #<?= $ps['id_pesanan'] ?> berhasil dibuat</h4>
  <p class="text-muted mb-1">Tanggal: <?= e($ps['tanggal']) ?> &middot; Status: <span class="badge bg-primary"><?= e($ps['status']) ?></span></p>
  <p>Dikirim ke: <?= nl2br(e($ps['alamat_kirim'])) ?></p>
  <table class="table table-sm">
    <thead><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
    <?php foreach ($detail as $d): ?>
      <tr><td><?= e($d['nama_produk']) ?></td><td><?= rupiah($d['harga_satuan']) ?></td><td><?= $d['jumlah'] ?></td><td class="text-end"><?= rupiah($d['subtotal']) ?></td></tr>
    <?php endforeach; ?>
    <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end"><?= rupiah($ps['total']) ?></td></tr>
  </table>
  <a href="index.php" class="btn btn-primary">Kembali Belanja</a>
  <a href="pesanan.php" class="btn btn-outline-secondary">Riwayat Pesanan</a>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
