<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$flash = flash_get('admin');

if (is_post()) {
  csrf_verify();

  $action = $_POST['action'] ?? '';
  $id = (int)($_POST['id'] ?? 0);

  if ($action === 'toggle' && $id > 0) {
    // toggle availability
    $stmt = db_prepare_execute($mysqli, "UPDATE donors SET available = 1 - available WHERE id = ?", "i", [$id]);
    flash_set('admin', 'Donor availability updated.', 'success');
    redirect('admin/donors.php');
  }

  if ($action === 'delete' && $id > 0) {
    $stmt = db_prepare_execute($mysqli, "DELETE FROM donors WHERE id = ?", "i", [$id]);
    flash_set('admin', 'Donor deleted.', 'warning');
    redirect('admin/donors.php');
  }
}

$city = trim($_GET['city'] ?? '');
$type = strtoupper(trim($_GET['type'] ?? ''));
$available = $_GET['available'] ?? '';

$sql = "SELECT id, full_name, phone, city, blood_type, age, last_donation_date, available, created_at
        FROM donors WHERE 1=1";
$types = "";
$params = [];

if ($type && valid_blood_type($type)) {
  $sql .= " AND blood_type = ?";
  $types .= "s";
  $params[] = $type;
}
if ($city !== '') {
  $sql .= " AND city = ?";
  $types .= "s";
  $params[] = $city;
}
if ($available === '1' || $available === '0') {
  $sql .= " AND available = ?";
  $types .= "i";
  $params[] = (int)$available;
}
$sql .= " ORDER BY created_at DESC";

$stmt = db_prepare_execute($mysqli, $sql, $types, $params);
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$PAGE_TITLE = 'Admin: Donors';
$PAGE_DESC = 'Manage donors: filter, toggle availability, or delete records.';
$PAGE_BADGE = 'Admin';
$PAGE_ACTIONS = [
  ['label' => 'Requests admin', 'href' => 'admin/requests.php', 'class' => 'btn btn-ghost'],
  ['label' => 'Back to site', 'href' => 'index.php', 'class' => 'btn'],
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="stack">

  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <form method="get" class="card">
    <div class="grid">
      <div class="field">
        <label>Blood type</label>
        <select name="type">
          <option value="">All</option>
          <?php foreach (['O-','O+','A-','A+','B-','B+','AB-','AB+'] as $bt): ?>
            <option value="<?= e($bt) ?>" <?= ($type === $bt ? 'selected' : '') ?>><?= e($bt) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label>City</label>
        <input name="city" value="<?= e($city) ?>">
      </div>
      <div class="field">
        <label>Availability</label>
        <select name="available">
          <option value="">All</option>
          <option value="1" <?= ($available==='1'?'selected':'') ?>>Available</option>
          <option value="0" <?= ($available==='0'?'selected':'') ?>>Not available</option>
        </select>
      </div>
    </div>
    <button class="btn" type="submit">Filter</button>
    <a class="btn btn-ghost" href="<?= e(url('admin/donors.php')) ?>">Reset</a>
  </form>

  <div class="card">
    <h3 style="margin:0 0 8px">Results (<?= e((string)count($rows)) ?>)</h3>
    <?php if (!$rows): ?>
      <p class="muted">No donors.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Blood</th>
            <th>City</th>
            <th>Phone</th>
            <th>Available</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= e($r['full_name']) ?></td>
              <td><span class="badge"><?= e($r['blood_type']) ?></span></td>
              <td><?= e($r['city']) ?></td>
              <td><?= e($r['phone']) ?></td>
              <td>
                <?php if ((int)$r['available'] === 1): ?>
                  <span class="badge badge-ok">Yes</span>
                <?php else: ?>
                  <span class="badge badge-no">No</span>
                <?php endif; ?>
              </td>
              <td>
                <form method="post" style="display:inline">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= e((string)$r['id']) ?>">
                  <input type="hidden" name="action" value="toggle">
                  <button class="btn btn-ghost" type="submit">Toggle</button>
                </form>
                <form method="post" style="display:inline" onsubmit="return confirm('Delete this donor?')">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="id" value="<?= e((string)$r['id']) ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
