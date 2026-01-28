<?php
// includes/header.php
$PAGE_THEME = $PAGE_THEME ?? page_theme_auto($PAGE_BADGE ?? null);
$PAGE_ICON  = $PAGE_ICON  ?? page_icon_auto($PAGE_THEME, $PAGE_BADGE ?? null);
$THEME_STYLE = theme_style($PAGE_THEME);
$u = auth_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e(($PAGE_TITLE ?? APP_NAME)) ?></title>
  <link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>" />
</head>
<body data-theme="<?= e($PAGE_THEME) ?>" style="<?= e($THEME_STYLE) ?>">
  <header class="topbar">
    <div class="container row">
      <div class="brand">
        <a href="<?= e(url('index.php')) ?>"><?= e(APP_NAME) ?></a>
      </div>
      <nav class="nav">
        <a href="<?= e(url('donor_add.php')) ?>">Donate</a>
        <a href="<?= e(url('match.php')) ?>">Find Donors</a>
        <a href="<?= e(url('requests_list.php')) ?>">Requests</a>
        <?php if ($u): ?>
          <a href="<?= e(url('admin/donors.php')) ?>">Admin</a>
          <span class="pill">Hi, <?= e($u['full_name']) ?></span>
          <a class="btn btn-ghost" href="<?= e(url('logout.php')) ?>">Logout</a>
        <?php else: ?>
          <a class="btn btn-ghost" href="<?= e(url('login.php')) ?>">Login</a>
          <a class="btn" href="<?= e(url('register.php')) ?>">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <main class="container page">

  <?php if (empty($HIDE_PAGEHEAD) && !empty($PAGE_TITLE)): ?>
    <section class="pagehead" role="banner">
      <div class="pagehead__inner">
        <div class="pagehead__content">
          <?php if (!empty($PAGE_BADGE)): ?>
            <div class="pagehead__badge">
              <?php if (!empty($PAGE_ICON)): ?>
                <span class="badgeicon" aria-hidden="true"><?= page_icon_svg($PAGE_ICON, 16) ?></span>
              <?php endif; ?>
              <?= e($PAGE_BADGE) ?>
            </div>
          <?php endif; ?>
          <h1 class="pagehead__title"><?= e($PAGE_TITLE) ?></h1>
          <?php if (!empty($PAGE_DESC)): ?>
            <p class="pagehead__desc"><?= e($PAGE_DESC) ?></p>
          <?php endif; ?>

          <?php if (!empty($PAGE_ACTIONS) && is_array($PAGE_ACTIONS)): ?>
            <div class="pagehead__actions">
              <?php foreach ($PAGE_ACTIONS as $a):
                $label = $a['label'] ?? '';
                $href  = $a['href'] ?? '#';
                $cls   = $a['class'] ?? 'btn btn-ghost';
                if (!$label) continue;
              ?>
                <a class="<?= e($cls) ?>" href="<?= e(url($href)) ?>"><?= e($label) ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="pagehead__art" aria-hidden="true"></div>
      </div>
    </section>
  <?php endif; ?>
