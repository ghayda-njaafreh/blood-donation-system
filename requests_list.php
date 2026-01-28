<?php
require_once __DIR__ . '/includes/init.php';

$type = strtoupper(trim($_GET['type'] ?? ''));
$city = trim($_GET['city'] ?? '');

$sql = "SELECT id, requester_name, phone, city, needed_blood_type, units, needed_date, notes, created_at
        FROM requests WHERE 1=1";
$types = "";
$params = [];

if ($type && valid_blood_type($type)) {
  $sql .= " AND needed_blood_type = ?";
  $types .= "s";
  $params[] = $type;
}
if ($city !== '') {
  $sql .= " AND city = ?";
  $types .= "s";
  $params[] = $city;
}
$sql .= " ORDER BY created_at DESC";

$stmt = db_prepare_execute($mysqli, $sql, $types, $params);
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$PAGE_TITLE = 'Blood Requests';
$PAGE_DESC = 'Browse and filter posted requests, then contact recipients to help.';
$PAGE_BADGE = 'Requests';
$PAGE_ACTIONS = [
  ['label' => 'New request', 'href' => 'request_add.php', 'class' => 'btn'],
  ['label' => 'Find donors', 'href' => 'match.php', 'class' => 'btn btn-ghost'],
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="stack">

  <form method="get" class="card">
    <div class="grid">
      <div class="field">
        <label>Needed blood type</label>
        <select name="type">
          <option value="">All</option>
          <?php foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $bt): ?>
            <option value="<?= e($bt) ?>" <?= ($type === $bt ? 'selected' : '') ?>><?= e($bt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>City (optional exact match)</label>
        <input name="city" value="<?= e($city) ?>">
      </div>
    </div>
    <button class="btn" type="submit">Filter</button>
    <a class="btn btn-ghost" href="<?= e(url('requests_list.php')) ?>">Reset</a>
    <a class="btn" href="<?= e(url('request_add.php')) ?>">New request</a>
  </form>

  <div class="card">
    <h3 style="margin:0 0 8px">Results (<?= e((string)count($rows)) ?>)</h3>

    <?php if (!$rows): ?>
      <p class="muted">No requests found.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th>Requester</th>
            <th>Needed</th>
            <th>City</th>
            <th>Phone</th>
            <th>When</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= e($r['requester_name']) ?></td>
              <td><span class="badge"><?= e($r['needed_blood_type']) ?></span> (<?= e((string)$r['units']) ?> units)</td>
              <td><?= e($r['city']) ?></td>
              <td><?= e($r['phone']) ?></td>
              <td><?= e($r['needed_date'] ?? '-') ?></td>
              <td><?= e($r['notes'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
