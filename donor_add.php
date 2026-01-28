<?php
require_once __DIR__ . '/includes/init.php';

$err = '';
$ok = '';

if (is_post()) {
  csrf_verify();

  $full_name = trim($_POST['full_name'] ?? '');
  $phone = normalize_phone($_POST['phone'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $blood_type = strtoupper(trim($_POST['blood_type'] ?? ''));
  $age = trim($_POST['age'] ?? '');
  $lastVal = trim($_POST['last_donation_date'] ?? '');
  $available = isset($_POST['available']) ? 1 : 0;

  if (strlen($full_name) < 3) $err = "Full name is required.";
  elseif (!preg_match('/^[0-9\+\-\s]{7,}$/', $phone)) $err = "Phone looks invalid.";
  elseif (strlen($city) < 2) $err = "City is required.";
  elseif (!valid_blood_type($blood_type)) $err = "Invalid blood type.";
  elseif ($age !== '' && (!ctype_digit($age) || (int)$age < 16 || (int)$age > 80)) $err = "Age must be 16-80.";

  if (!$err) {
    $ageVal = ($age === '') ? null : (int)$age;
    $dateVal = ($lastVal === '') ? null : $lastVal;

    $sql = "INSERT INTO donors (full_name, phone, city, blood_type, age, last_donation_date, available)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
      $err = "DB error (prepare).";
    } else {
      // For nullable fields, we bind as strings/ints and send NULL by setting variable to null (mysqlnd).
      $ageParam = ($ageVal === null) ? null : (int)$ageVal;
      $dateParam = ($dateVal === null) ? null : (string)$dateVal;
      // Bind with correct types: age=int, date=string, available=int
      $stmt->bind_param("ssssisi", $full_name, $phone, $city, $blood_type, $ageParam, $dateParam, $available);

      if (!$stmt->execute()) {
        $err = "Could not save donor. Please try again.";
      } else {
        $ok = "Thank you! You are registered as a donor.";
        $_POST = [];
      }
    }
  }
}

$PAGE_TITLE = 'Donor Registration';
$PAGE_DESC = 'Register as a donor so recipients can reach you quickly.';
$PAGE_BADGE = 'Donate';
require_once __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <div class="card stack">
    <h2 style="margin:0">Become a donor</h2>
    <p class="muted" style="margin:0">Share your blood type and contact details. You can toggle availability any time by re-submitting.</p>

  <div id="formErrors" class="alert danger" style="display:none"></div>

  <?php if ($err): ?>
    <div class="alert danger"><?= e($err) ?></div>
  <?php endif; ?>
  <?php if ($ok): ?>
    <div class="alert success"><?= e($ok) ?></div>
  <?php endif; ?>

  <form id="donorForm" method="post" autocomplete="off" class="stack">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="grid">
      <div class="field">
        <label>Full name</label>
        <input id="full_name" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Phone</label>
        <input id="phone" name="phone" required value="<?= e($_POST['phone'] ?? '') ?>">
      </div>

      <div class="field">
        <label>City</label>
        <input id="city" name="city" required value="<?= e($_POST['city'] ?? '') ?>">
      </div>

      <div class="field">
        <label>Blood type</label>
        <select id="blood_type" name="blood_type" required>
          <option value="">Choose...</option>
          <?php foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $bt): ?>
            <option value="<?= e($bt) ?>" <?= (($_POST['blood_type'] ?? '') === $bt ? 'selected' : '') ?>><?= e($bt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label>Age (optional)</label>
        <input id="age" name="age" inputmode="numeric" value="<?= e($_POST['age'] ?? '') ?>">
      </div>

      <div class="field">
        <label>Last donation date (optional)</label>
        <input name="last_donation_date" type="date" value="<?= e($_POST['last_donation_date'] ?? '') ?>">
      </div>
    </div>

    <div class="field" style="margin-top:6px">
      <label style="display:flex; align-items:center; gap:8px">
        <input type="checkbox" name="available" <?= (isset($_POST['available']) || !is_post()) ? 'checked' : '' ?>>
        I am currently available to donate
      </label>
    </div>

    <button class="btn" type="submit">Submit</button>
  </form>

  </div>

  <aside class="card card--soft stack">
    <div class="helpbox">
      <b>Good to know</b>: Keeping your city and phone accurate helps people reach you faster.
    </div>
    <img class="hero__img" style="width:min(320px,100%); filter:none" src="<?= e(url('assets/hero-right.png')) ?>" alt="">
    <h3 style="margin:0">Donation tips</h3>
    <ul class="list">
      <li>Bring an ID and drink water before donating.</li>
      <li>If you donated recently, mention the last donation date.</li>
      <li>Mark availability only if you can donate soon.</li>
    </ul>
  </aside>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
