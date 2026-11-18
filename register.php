<?php
require __DIR__ . '/includes/init.php';
$judul = 'Daftar';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? ''); $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? ''; $role = $_POST['role'] ?? 'pembeli'; $alamat = trim($_POST['alamat'] ?? '');
    if (!in_array($role, ['penjual', 'pembeli'], true)) $role = 'pembeli';
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $err = 'Isi nama, email valid, dan password minimal 6 karakter.';
    } else {
        $c = $db->prepare("SELECT id_user FROM users WHERE email = ?");
        $c->bind_param('s', $email); $c->execute();
        if ($c->get_result()->num_rows) { $err = 'Email sudah terdaftar.'; }
        else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $st = $db->prepare("INSERT INTO users (nama,email,password,role,alamat) VALUES (?,?,?,?,?)");
            $st->bind_param('sssss', $nama, $email, $hash, $role, $alamat); $st->execute();
            flash('Registrasi berhasil, silakan masuk.'); redirect('login.php');
        }
    }
}
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm"><div class="card-body">
<h4>Daftar Akun</h4>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <div class="mb-3"><label class="form-label">Nama</label><input name="nama" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Daftar sebagai</label>
    <select name="role" class="form-select"><option value="pembeli">Pembeli</option><option value="penjual">Penjual</option></select></div>
  <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
  <button class="btn btn-primary w-100">Daftar</button>
</form></div></div></div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
