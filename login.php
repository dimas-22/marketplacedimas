<?php
require __DIR__ . '/includes/init.php';
$judul = 'Masuk';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? ''); $pass = $_POST['password'] ?? '';
    $st = $db->prepare("SELECT * FROM users WHERE email = ?");
    $st->bind_param('s', $email); $st->execute();
    $u = $st->get_result()->fetch_assoc();
    if ($u && password_verify($pass, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id_user'], 'nama' => $u['nama'], 'role' => $u['role'], 'alamat' => $u['alamat']];
        redirect($u['role'] === 'penjual' ? 'admin/produk.php' : 'index.php');
    }
    $err = 'Email atau password salah.';
}
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body">
<h4>Masuk</h4>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
  <button class="btn btn-primary w-100">Masuk</button>
</form></div></div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
