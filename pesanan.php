<?php
require __DIR__ . '/includes/init.php';
require_login('pembeli');
$judul = 'Pesanan Saya'; $u = user();
$st = $db->prepare("SELECT * FROM pesanan WHERE id_pembeli = ? ORDER BY tanggal DESC");
$st->bind_param('i', $u['id']); $st->execute(); $rs = $st->get_result();
include __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Pesanan Saya</h3>
<div class="card shadow-sm"><table class="table mb-0">
  <thead class="table-light"><tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Status</th><th></th></tr></thead>
  <?php foreach ($rs as $p): ?>
    <tr><td>#<?= $p['id_pesanan'] ?></td><td><?= e($p['tanggal']) ?></td><td><?= rupiah($p['total']) ?></td>
    <td><span class="badge bg-primary"><?= e($p['status']) ?></span></td>
    <td><a class="btn btn-sm btn-outline-primary" href="pesanan_sukses.php?id=<?= $p['id_pesanan'] ?>">Detail</a></td></tr>
  <?php endforeach; ?>
  <?php if ($rs->num_rows === 0): ?><tr><td colspan="5" class="text-center text-muted">Belum ada pesanan.</td></tr><?php endif; ?>
</table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
