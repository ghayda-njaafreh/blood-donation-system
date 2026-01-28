<?php
require_once __DIR__ . '/includes/init.php';

$type = strtoupper(trim($_GET['type'] ?? ''));
$city = trim($_GET['city'] ?? '');
$results = [];
$compat = [];

if ($type && valid_blood_type($type)) {
  $compat = blood_compatible_types($type);

  if (!empty($compat)) {
    $placeholders = implode(',', array_fill(0, count($compat), '?'));
    $sql = "SELECT id, full_name, phone, city, blood_type, age, last_donation_date, available
            FROM donors
            WHERE available=1 AND blood_type IN ($placeholders)";

    $types = str_repeat('s', count($compat));
    $params = $compat;

    if ($city !== '') {
      $sql .= " AND city = ?";
      $types .= "s";
      $params[] = $city;
    }

    $sql .= " ORDER BY city, blood_type, full_name";

    $stmt = db_prepare_execute($mysqli, $sql, $types, $params);
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }
}

$PAGE_TITLE = 'Find Compatible Donors';
$PAGE_DESC = 'Choose the recipient blood type and (optionally) a city. The system shows compatible donor types.';
$PAGE_BADGE = 'Match';
$PAGE_ACTIONS = [
  ['label' => 'Register as donor', 'href' => 'donor_add.php', 'class' => 'btn'],
  ['label' => 'View requests', 'href' => 'requests_list.php', 'class' => 'btn btn-ghost'],
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="stack">

  <form method="get" class="card">
    <div class="grid">
      <div class="field">
        <label>Recipient blood type</label>
        <select name="type" required>
          <option value="">Choose...</option>
          <?php foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $bt): ?>
            <option value="<?= e($bt) ?>" <?= ($type === $bt ? 'selected' : '') ?>><?= e($bt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>City (optional exact match)</label>
        <input name="city" value="<?= e($city) ?>" placeholder="e.g., Amman">
      </div>
    </div>
    <button class="btn" type="submit">Search</button>
  </form>

  <?php if ($type && !valid_blood_type($type)): ?>
    <div class="alert danger">Invalid blood type.</div>
  <?php endif; ?>

  <?php if ($type && valid_blood_type($type)): ?>
    <div class="alert info">
      Compatible donor types for <b><?= e($type) ?></b>:
      <?= e(implode(', ', $compat)) ?>
    </div>

    <div class="card">
      <h3 style="margin:0 0 8px">Results (<?= e((string)count($results)) ?>)</h3>
      <?php if (!$results): ?>
        <p class="muted">No donors found. Try another city or blood type.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Blood</th>
              <th>City</th>
              <th>Phone</th>
              <th>Last donation</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $r): ?>
              <tr>
                <td><?= e($r['full_name']) ?></td>
                <td><span class="badge"><?= e($r['blood_type']) ?></span></td>
                <td><?= e($r['city']) ?></td>
                <td><?= e($r['phone']) ?></td>
                <td><?= e($r['last_donation_date'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
