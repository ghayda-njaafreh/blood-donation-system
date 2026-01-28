<?php
require_once __DIR__ . '/includes/init.php';
$HIDE_PAGEHEAD = true;
require_once __DIR__ . '/includes/header.php';

// Stats
$stmt1 = db_prepare_execute($mysqli, "SELECT COUNT(*) AS c FROM donors WHERE available=1");
$c1 = $stmt1->get_result()->fetch_assoc()['c'] ?? 0;

$stmt2 = db_prepare_execute($mysqli, "SELECT COUNT(*) AS c FROM requests");
$c2 = $stmt2->get_result()->fetch_assoc()['c'] ?? 0;

$flash = flash_get('global');
?>

<?php
// Home content
?>
<section class="hero">
  <div class="hero__left">
    <div class="hero__kicker">🩸 Blood Donation</div>
    <h1 class="hero__title">Donate blood, save lives.</h1>
    <p class="hero__subtitle">
      Register as a donor, find compatible donors quickly, and track urgent blood requests in one simple system.
    </p>

    <?php if ($flash): ?>
      <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <div class="hero__actions">
      <a class="btn" href="<?= e(url('donor_add.php')) ?>">Become a donor</a>
      <a class="btn btn-ghost" href="<?= e(url('match.php')) ?>">Find donors</a>
      <a class="btn btn-ghost" href="<?= e(url('request_add.php')) ?>">Create request</a>
    </div>

    <div class="hero__stats">
      <div class="stat">
        <div class="stat__num"><?= e((string)$c1) ?></div>
        <div class="stat__label">Available donors</div>
      </div>
      <div class="stat">
        <div class="stat__num"><?= e((string)$c2) ?></div>
        <div class="stat__label">Blood requests</div>
      </div>
    </div>
  </div>

  <div class="hero__right">
    <img class="hero__img" src="<?= e(url('assets/hero-right.png')) ?>" alt="Blood donation illustration">
  </div>
</section>

<div class="grid" style="margin-top:14px">
  <div class="card">
    <h3 style="margin:0 0 6px">Become a donor</h3>
    <p class="muted" style="margin:0 0 10px">
      Add your blood type and contact details, and mark availability so recipients can reach you.
    </p>
    <a class="btn" href="<?= e(url('donor_add.php')) ?>">Donate</a>
  </div>

  <div class="card">
    <h3 style="margin:0 0 6px">Find compatible donors</h3>
    <p class="muted" style="margin:0 0 10px">
      Search by blood group and availability, then contact donors to coordinate the donation.
    </p>
    <a class="btn btn-ghost" href="<?= e(url('match.php')) ?>">Search</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
