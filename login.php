<?php
require_once __DIR__ . '/includes/init.php';

if (auth_user()) {
  redirect('index.php');
}

$flash = flash_get('auth');
$err = '';

if (is_post()) {
  csrf_verify();
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  $stmt = db_prepare_execute($mysqli, "SELECT id, full_name, email, password_hash FROM users WHERE email = ?", "s", [$email]);
  $row = $stmt->get_result()->fetch_assoc();

  if (!$row || !password_verify($pass, $row['password_hash'])) {
    $err = "Invalid email or password.";
  } else {
    session_regenerate_id(true);
    $_SESSION['user'] = [
      'id' => (int)$row['id'],
      'full_name' => $row['full_name'],
      'email' => $row['email'],
    ];
    flash_set('global', 'Logged in successfully.', 'success');
    redirect('index.php');
  }
}

$PAGE_TITLE = 'Login';
$PAGE_DESC = 'Admin access for managing donors and requests.';
$PAGE_BADGE = 'Account';
require_once __DIR__ . '/includes/header.php';
?>
<div class="layout">
  <div class="card stack">
    <h2 style="margin:0">Welcome back</h2>

  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($err): ?>
    <div class="alert danger"><?= e($err) ?></div>
  <?php endif; ?>

  <form method="post" autocomplete="off" class="stack">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="field">
      <label>Email</label>
      <input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Password</label>
      <input name="password" type="password" required>
    </div>
    <button class="btn" type="submit">Login</button>
  </form>

    <p class="muted">No account? <a href="<?= e(url('register.php')) ?>">Register</a></p>
  </div>

  <aside class="card card--soft">
    <div class="helpbox">
      <b>Tip</b>: If you're a donor, you don't need an account.
      You can register directly from the donor page.
    </div>
    <img class="hero__img" style="margin-top:12px; width:min(320px,100%); filter:none" src="<?= e(url('assets/hero-right.png')) ?>" alt="">
    <ul class="list">
      <li>Manage donors availability</li>
      <li>Review blood requests</li>
      <li>Quick filtering and search</li>
    </ul>
  </aside>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
