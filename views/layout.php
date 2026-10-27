<?php
$nav = [
  'Overview' => ['dashboard' => 'Dashboard'],
  'Money' => ['allowance' => 'Allowance', 'expenses' => 'Expenses', 'categories' => 'Categories', 'budget' => 'Budget', 'savings' => 'Savings'],
  'Insights' => ['reports' => 'Reports', 'notifications' => 'Notifications']
];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BudgetWise: Personal Expense Tracker</title>
  <link rel="stylesheet" href="assets/style.css?v=20261020-action-hover">
</head>

<body>
  <input type="checkbox" id="navtoggle" hidden>
  <header id="top"><label for="navtoggle" aria-label="Open menu">&#9776;</label> BudgetWise</header>
  <label for="navtoggle" id="scrim"></label>
  <nav id="side" aria-label="Main">
    <div class="brand"><i>B</i>BudgetWise</div>
    <?php foreach ($nav as $group => $items): ?>
      <div class="grp"><?= e($group) ?></div>
      <?php foreach ($items as $key => $label): ?>
        <a href="index.php?page=<?= $key ?>" class="<?= $page === $key ? 'on' : '' ?>"><?= e($label) ?>
          <?php if ($key === 'notifications' && $badge): ?><span class="badge"><?= $badge ?></span><?php endif; ?></a>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <div class="side-account">
      <div class="grp">Account</div>
      <a href="index.php?page=profile" class="profile-link <?= $page === 'profile' ? 'on' : '' ?>">
        <svg class="person-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <circle cx="12" cy="8" r="4"></circle>
          <path d="M4 21a8 8 0 0 1 16 0"></path>
        </svg>
        Profile
      </a>
      <div class="who">
        <b><?= e($profile['full_name'] ?: 'Student') ?></b>
        <a class="profile-photo" href="index.php?page=profile" aria-label="Change profile picture">
          <?php if (!empty($profile['profile_image'])): ?>
            <img src="uploads/profile/<?= e($profile['profile_image']) ?>" alt="">
          <?php else: ?>
            <svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">
              <circle cx="24" cy="17" r="8"></circle>
              <path d="M8 43a16 16 0 0 1 32 0"></path>
            </svg>
          <?php endif; ?>
        </a>
      </div>
    </div>
  </nav>
  <main class="page-<?= e($page) ?>">
    <?php if ($flash): ?>
      <div class="flash <?= e($flash[1]) ?>" role="status"><?= e($flash[0]) ?></div><?php endif; ?>
    <?= $content ?>
  </main>
</body>

</html>