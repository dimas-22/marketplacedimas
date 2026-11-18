<?php
require __DIR__ . '/includes/init.php';
require_login('pembeli');
$judul = 'Checkout';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('Keranjang kosong.', 'warning'); redirect('keranjang.php'); }
$u = user();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $alamat = trim($_POST['alamat'] ?? '');
    if ($alamat === '') { $err = 'Alamat pengiriman wajib diisi.'; }
    else {
        try {
            $db->begin_transaction();
            $total = 0; $lines = [];
            ksort($cart); // urutan konsisten untuk mengurangi deadlock
            foreach ($cart as $idp => $qty) {
                $st = $db->prepare("SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ? FOR UPDATE");
                $st->bind_param('i', $idp); $st->execute();
                $p = $st->get_result()->fetch_assoc();
                if (!$p) throw new Exception('Ada produk yang sudah tidak tersedia.');
                if ($p['stok'] < $qty) throw new Exception('Stok "' . $p['nama_produk'] . '" tidak mencukupi.');
                $sub = $qty * $p['harga']; $total += $sub;
                $lines[] = [$idp, $qty, $p['harga'], $sub];
            }
            $ps = $db->prepare("INSERT INTO pesanan (id_pembeli, total, alamat_kirim, status) VALUES (?,?,?, 'baru')");
            $ps->bind_param('ids', $u['id'], $total, $alamat); $ps->execute();
            $idPesanan = $db->insert_id;
            $ds = $db->prepare("INSERT INTO detail_pesanan (id_pesanan,id_produk,jumlah,harga_satuan,subtotal) VALUES (?,?,?,?,?)");
            $us = $db->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
            foreach ($lines as [$idp, $qty, $harga, $sub]) {
                $ds->bind_param('iiidd', $idPesanan, $idp, $qty, $harga, $sub); $ds->execute();
                $us->bind_param('ii', $qty, $idp); $us->execute();
            }
            $db->commit();
            unset($_SESSION['cart']);
            flash('Pesanan berhasil dibuat.');
            redirect('pesanan_sukses.php?id=' . $idPesanan);
        } catch (Exception $ex) {
            $db->rollback();
            $err = $ex->getMessage();
        }
    }
}

$items = []; $total = 0;
$in = implode(',', array_map('intval', array_keys($cart)));
foreach ($db->query("SELECT * FROM produk WHERE id_produk IN ($in)") as $p) {
    $p['qty'] = $cart[$p['id_produk']]; $p['sub'] = $p['qty'] * $p['harga'];
    $total += $p['sub']; $items[] = $p;
}
include __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Checkout</h3>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-md-7"><div class="card shadow-sm"><div class="card-body">
    <h5>Ringkasan Pesanan</h5>
    <table class="table table-sm">
      <?php foreach ($items as $i): ?>
        <tr><td><?= e($i['nama_produk']) ?> &times; <?= $i['qty'] ?></td><td class="text-end"><?= rupiah($i['sub']) ?></td></tr>
      <?php endforeach; ?>
      <tr class="fw-bold"><td>Total</td><td class="text-end"><?= rupiah($total) ?></td></tr>
    </table>
  </div></div></div>
  <div class="col-md-5"><div class="card shadow-sm"><div class="card-body">
    <h5>Alamat Pengiriman</h5>
    <form method="post"><?= csrf_field() ?>
      <textarea name="alamat" class="form-control mb-3" rows="4" required><?= e($_POST['alamat'] ?? $u['alamat']) ?></textarea>
      <button class="btn btn-success w-100">Buat Pesanan</button>
    </form>
  </div></div></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
