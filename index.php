<?php
require __DIR__ . '/includes/init.php';
$judul = 'Daftar Produk';
$q = trim($_GET['q'] ?? '');
$kat = (int)($_GET['kategori'] ?? 0);

$sql = "SELECT p.*, k.nama_kategori, u.nama AS penjual FROM produk p
        JOIN kategori k ON k.id_kategori = p.id_kategori
        JOIN users u ON u.id_user = p.id_penjual
        WHERE p.nama_produk LIKE ?";
$types = 's'; $params = ['%' . $q . '%'];
if ($kat) { $sql .= " AND p.id_kategori = ?"; $types .= 'i'; $params[] = $kat; }
$sql .= " ORDER BY p.created_at DESC";
$st = $db->prepare($sql);
$st->bind_param($types, ...$params);
$st->execute();
$produk = $st->get_result();
$kategori = $db->query("SELECT * FROM kategori ORDER BY nama_kategori");

include __DIR__ . '/includes/header.php';
?>
<section class="hero-banner">
  <div class="hero-copy">
    <span class="hero-eyebrow">TEMUKAN YANG KAMU CARI</span>
    <h1>Belanja seru,<br><span>harga bersahabat.</span></h1>
    <p>Jelajahi produk pilihan dari penjual lokal, semua ada di satu tempat.</p>
    <a class="btn hero-button" href="#produk">Mulai jelajahi <span aria-hidden="true">&rarr;</span></a>
  </div>
  <div class="hero-art" aria-hidden="true">
    <span class="hero-sparkle sparkle-one">✦</span>
    <span class="hero-sparkle sparkle-two">✦</span>
    <div class="hero-bag"><span>🛍️</span></div>
    <div class="hero-orbit orbit-one"></div>
    <div class="hero-orbit orbit-two"></div>
    <span class="hero-tag">BELANJA<br>HEMAT!</span>
  </div>
</section>

<section class="shop-panel" aria-label="Cari dan pilih kategori">
  <form class="shop-search" method="get" action="<?= BASE_URL ?>index.php">
    <label class="visually-hidden" for="product-search">Cari produk</label>
    <input id="product-search" type="search" name="q" class="form-control" placeholder="Cari produk yang kamu inginkan..." value="<?= e($q) ?>">
    <?php if ($kat): ?><input type="hidden" name="kategori" value="<?= $kat ?>"><?php endif; ?>
    <button class="btn search-button" type="submit"><span aria-hidden="true">⌕</span> Cari</button>
  </form>
  <div class="category-row">
    <span class="category-label">Kategori</span>
    <a class="category-chip <?= !$kat ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php<?= $q !== '' ? '?q=' . rawurlencode($q) : '' ?>">Semua</a>
    <?php foreach ($kategori as $k): ?>
      <a class="category-chip <?= $kat == $k['id_kategori'] ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php?kategori=<?= (int)$k['id_kategori'] ?><?= $q !== '' ? '&amp;q=' . rawurlencode($q) : '' ?>"><?= e($k['nama_kategori']) ?></a>
    <?php endforeach; ?>
  </div>
</section>

<section id="produk" class="products-section">
  <div class="section-heading">
    <div>
      <span class="section-kicker">PILIHAN UNTUKMU</span>
      <h2><?= $q !== '' ? 'Hasil pencarian' : 'Produk terbaru' ?></h2>
    </div>
    <span class="result-count"><?= $produk->num_rows ?> produk</span>
  </div>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
<?php foreach ($produk as $p): ?>
  <div class="col">
    <article class="card product-card h-100">
      <?= gambar_html($p['gambar']) ?>
      <div class="card-body d-flex flex-column">
        <span class="product-category"><?= e($p['nama_kategori']) ?></span>
        <h3 class="card-title"><?= e($p['nama_produk']) ?></h3>
        <div class="product-price"><?= rupiah($p['harga']) ?></div>
        <small class="product-meta">Oleh <?= e($p['penjual']) ?> <span aria-hidden="true">&middot;</span> Stok <?= (int)$p['stok'] ?></small>
        <a href="<?= BASE_URL ?>produk_detail.php?id=<?= (int)$p['id_produk'] ?>" class="btn product-button mt-auto">Lihat produk <span aria-hidden="true">&rarr;</span></a>
      </div>
    </article>
  </div>
<?php endforeach; ?>
<?php if ($produk->num_rows === 0): ?>
  <div class="col-12"><div class="empty-state"><span aria-hidden="true">🔎</span><h3>Produk belum ditemukan</h3><p>Coba kata kunci atau kategori yang berbeda, ya.</p><a class="btn product-button" href="<?= BASE_URL ?>index.php">Lihat semua produk</a></div></div>
<?php endif; ?>
</div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
