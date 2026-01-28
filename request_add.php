<?php
require_once __DIR__ . '/includes/init.php';

$err = '';
$ok = '';

if (is_post()) {
  csrf_verify();

  $requester_name = trim($_POST['requester_name'] ?? '');
  $phone = normalize_phone($_POST['phone'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $needed_blood_type = strtoupper(trim($_POST['needed_blood_type'] ?? ''));
  $units = (int)($_POST['units'] ?? 1);
  $needed_date = trim($_POST['needed_date'] ?? '');
  $notes = trim($_POST['notes'] ?? '');

  if (strlen($requester_name) < 3) $err = "Requester name is required.";
  elseif (!preg_match('/^[0-9\+\-\s]{7,}$/', $phone)) $err = "Phone looks invalid.";
  elseif (strlen($city) < 2) $err = "City is required.";
  elseif (!valid_blood_type($needed_blood_type)) $err = "Invalid blood type.";
  elseif ($units < 1 || $units > 20) $err = "Units must be between 1 and 20.";

  if (!$err) {
    $dateStr = ($needed_date === '') ? null : $needed_date;
    $notesStr = ($notes === '') ? null : $notes;

    $sql = "INSERT INTO requests (requester_name, phone, city, needed_blood_type, units, needed_date, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
      $err = "DB error (prepare).";
    } else {
      $stmt->bind_param("ssssiss", $requester_name, $phone, $city, $needed_blood_type, $units, $dateStr, $notesStr);
      if (!$stmt->execute()) {
        $err = "Could not save request.";
      } else {
        $ok = "Request submitted successfully.";
        $_POST = [];
      }
    }
  }
}

 $PAGE_TITLE = 'Create Blood Request';
 $PAGE_DESC = 'Post a request to find compatible donors quickly.';
 $PAGE_BADGE = 'Request';
 $PAGE_ACTIONS = [
   ['label' => 'View requests', 'href' => 'requests_list.php', 'class' => 'btn btn-ghost'],
   ['label' => 'Find donors', 'href' => 'match.php', 'class' => 'btn'],
 ];

require_once __DIR__ . '/includes/header.php';
?>

<div class="layout">
  <div class="card stack">
    <h2 style="margin:0">New request</h2>
    <p class="muted" style="margin:0">Fill the details below. Keep your phone number accurate so donors can reach you.</p>

  <?php if ($err): ?>
    <div class="alert danger"><?= e($err) ?></div>
  <?php endif; ?>
  <?php if ($ok): ?>
    <div class="alert success"><?= e($ok) ?></div>
  <?php endif; ?>

  <form method="post" autocomplete="off" class="stack">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="grid">
      <div class="field">
        <label>Requester name</label>
        <input name="requester_name" required value="<?= e($_POST['requester_name'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Phone</label>
        <input name="phone" required value="<?= e($_POST['phone'] ?? '') ?>">
      </div>
      <div class="field">
        <label>City</label>
        <input name="city" required value="<?= e($_POST['city'] ?? '') ?>">
      </div>
      <div class="field">
        <label>Needed blood type</label>
        <select name="needed_blood_type" required>
          <option value="">Choose...</option>
          <?php foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $bt): ?>
            <option value="<?= e($bt) ?>" <?= (($_POST['needed_blood_type'] ?? '') === $bt ? 'selected' : '') ?>><?= e($bt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label>Units</label>
        <input name="units" type="number" min="1" max="20" value="<?= e($_POST['units'] ?? '1') ?>">
      </div>

      <div class="field">
        <label>Needed date (optional)</label>
        <input name="needed_date" type="date" value="<?= e($_POST['needed_date'] ?? '') ?>">
      </div>
    </div>

    <div class="field">
      <label>Notes (optional)</label>
      <textarea name="notes"><?= e($_POST['notes'] ?? '') ?></textarea>
    </div>

    <button class="btn" type="submit">Submit request</button>
  </form>
  </div>

  <aside class="card card--soft stack">
    <div class="helpbox">
      <b>Urgency tip</b>: If this is an emergency, include the needed date and any relevant notes (hospital, contact person, etc.).
    </div>
    <img class="hero__img" style="width:min(320px,100%); filter:none" src="<?= e(url('assets/hero-right.png')) ?>" alt="">
    <h3 style="margin:0">What happens next?</h3>
    <ul class="list">
      <li>Your request appears in the Requests page.</li>
      <li>Use “Find Donors” to search compatible donor types.</li>
      <li>Contact donors directly to coordinate the donation.</li>
    </ul>
  </aside>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
