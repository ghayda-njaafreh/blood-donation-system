<?php
require_once __DIR__ . '/includes/init.php';

if (auth_user()) {
  redirect('index.php');
}

$err = '';
if (is_post()) {
  csrf_verify();
  $full_name = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  if (strlen($full_name) < 3) $err = "Name is too short.";
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = "Invalid email.";
  elseif (strlen($pass) < 8) $err = "Password must be at least 8 characters.";
  else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // Insert
    $stmt = $mysqli->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    if (!$stmt) { $err = "DB error."; }
    else {
      $stmt->bind_param("sss", $full_name, $email, $hash);
      if (!$stmt->execute()) {
        $err = "Email already exists or DB error.";
      } else {
        flash_set('global', 'Registration successful. Please login.', 'success');
        redirect('login.php');
      }
    }
  }
}

$PAGE_TITLE = 'Register';
$PAGE_DESC = 'Create an admin account to manage donors and requests.';
$PAGE_BADGE = 'Account';
require_once __DIR__ . '/includes/header.php';
?>
<div class="layout">
  <div class="card stack">
    <h2 style="margin:0">Create an admin account</h2>
    <p class="muted" style="margin:0">Donors can register without an account. This is for management only.</p>

  <?php if ($err): ?>
    <div class="alert danger"><?= e($err) ?></div>
  <?php endif; ?>

  <form method="post" autocomplete="off" class="stack">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="field">
      <label>Full name</label>
      <input name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Email</label>
      <input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Password</label>
      <input name="password" type="password" required minlength="8">
      <small class="muted">Minimum 8 characters.</small>
    </div>
    <button class="btn" type="submit">Create account</button>
  </form>

    <p class="muted">Already have an account? <a href="<?= e(url('login.php')) ?>">Login</a></p>
  </div>

  <aside class="card card--soft">
    <div class="helpbox">
      <b>Security note</b>: Use a strong password (8+ characters) and a valid email address.
    </div>
    <img class="hero__img" style="margin-top:12px; width:min(320px,100%); filter:none" src="<?= e(url('assets/hero-right.png')) ?>" alt="">
    <ul class="list">
      <li>Manage donor registrations</li>
      <li>Monitor blood requests</li>
      <li>Keep data organized</li>
    </ul>
  </aside>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
