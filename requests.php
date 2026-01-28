<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$flash = flash_get('admin_req');

if (is_post()) {
  csrf_verify();
  $action = $_POST['action'] ?? '';
  $id = (int)($_POST['id'] ?? 0);

  if ($action === 'delete' && $id > 0) {
    db_prepare_execute($mysqli, "DELETE FROM requests WHERE id = ?", "i", [$id]);
    flash_set('admin_req', 'Request deleted.', 'warning');
    redirect('admin/requests.php');
  }
}

$stmt = db_prepare_execute($mysqli, "SELECT id, requester_name, phone, city, needed_blood_type, units, needed_date, notes, created_at FROM requests ORDER BY created_at DESC");
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$PAGE_TITLE = 'Admin: Requests';
$PAGE_DESC = 'Review posted blood requests and remove invalid or completed entries.';
$PAGE_BADGE = 'Admin';
$PAGE_ACTIONS = [
  ['label' => 'Donors admin', 'href' => 'admin/donors.php', 'class' => 'btn btn-ghost'],
  ['label' => 'Back to site', 'href' => 'index.php', 'class' => 'btn'],
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="stack">

  <?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <div class="card">
    <table class="table">
      <thead>
        <tr>
          <th>Requester</th>
          <th>Needed</th>
          <th>City</th>
          <th>Phone</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= e($r['requester_name']) ?></td>
            <td><span class="badge"><?= e($r['needed_blood_type']) ?></span> (<?= e((string)$r['units']) ?>)</td>
            <td><?= e($r['city']) ?></td>
            <td><?= e($r['phone']) ?></td>
            <td><?= e($r['needed_date'] ?? '-') ?></td>
            <td>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete this request?')">
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
  </div>

  <p class="muted">Tip: You can also view public requests list at <a href="<?= e(url('requests_list.php')) ?>">Requests</a>.</p>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
