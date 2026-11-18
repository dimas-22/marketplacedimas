<?php
require __DIR__ . '/includes/init.php';
$judul = 'Keranjang Belanja';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($aksi === 'tambah') {
        $jml = max(1, (int)($_POST['jumlah'] ?? 1));
        $st = $db->prepare("SELECT stok FROM produk WHERE id_produk = ?");
        $st->bind_param('i', $id); $st->execute();
        $p = $st->get_result()->fetch_assoc();
        if (!$p) { flash('Produk tidak ditemukan.', 'danger'); }
        else {
            $baru = ($_SESSION['cart'][$id] ?? 0) + $jml;
            if ($baru > $p['stok']) { $baru = (int)$p['stok']; flash('Jumlah disesuaikan dengan stok tersedia.', 'warning'); }
            else flash('Produk ditambahkan ke keranjang.');
            if ($baru > 0) $_SESSION['cart'][$id] = $baru;
        }
    } elseif ($aksi === 'ubah') {
        $jml = (int)($_POST['jumlah'] ?? 1);
        if ($jml <= 0) unset($_SESSION['cart'][$id]);
        else {
            $st = $db->prepare("SELECT stok FROM produk WHERE id_produk = ?");
            $st->bind_param('i', $id); $st->execute();
            $p = $st->get_result()->fetch_assoc();
            if ($p) $_SESSION['cart'][$id] = min($jml, (int)$p['stok']);
        }
        flash('Keranjang diperbarui.');
    } elseif ($aksi === 'hapus') {
        unset($_SESSION['cart'][$id]); flash('Produk dihapus dari keranjang.');
    }
    redirect('keranjang.php');
}

$items = []; $total = 0;
if ($_SESSION['cart']) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $in = implode(',', $ids); // aman: hasil intval
    $rs = $db->query("SELECT * FROM produk WHERE id_produk IN ($in)");
    foreach ($rs as $p) {
        $qty = $_SESSION['cart'][$p['id_produk']];
        $p['qty'] = $qty; $p['sub'] = $qty * $p['harga'];
        $total += $p['sub']; $items[] = $p;
    }
}
include __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Keranjang Belanja</h3>
<?php if (!$items): ?>
  <div class="alert alert-secondary">Keranjang masih kosong. <a href="index.php">Belanja sekarang</a></div>
<?php else: ?>
<div class="card shadow-sm"><div class="table-responsive">
<table class="table align-middle mb-0">
  <thead class="table-light"><tr><th>Produk</th><th>Harga</th><th style="width:150px">Jumlah</th><th>Subtotal</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($items as $i): ?>
    <tr>
      <td><a href="produk_detail.php?id=<?= $i['id_produk'] ?>"><?= e($i['nama_produk']) ?></a></td>
      <td><?= rupiah($i['harga']) ?></td>
      <td>
        <form method="post" class="d-flex gap-1"><?= csrf_field() ?>
          <input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= $i['id_produk'] ?>">
          <input type="number" name="jumlah" value="<?= $i['qty'] ?>" min="0" max="<?= (int)$i['stok'] ?>" class="form-control form-control-sm">
          <button class="btn btn-sm btn-outline-secondary">OK</button>
        </form>
      </td>
      <td><?= rupiah($i['sub']) ?></td>
      <td>
        <form method="post"><?= csrf_field() ?>
          <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $i['id_produk'] ?>">
          <button class="btn btn-sm btn-outline-danger">Hapus</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot><tr class="table-light"><th colspan="3" class="text-end">Total</th><th colspan="2"><?= rupiah($total) ?></th></tr></tfoot>
</table></div></div>
<div class="d-flex justify-content-between mt-3">
  <a href="index.php" class="btn btn-outline-secondary">&larr; Lanjut Belanja</a>
  <a href="checkout.php" class="btn btn-success">Checkout &rarr;</a>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
