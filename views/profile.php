<?php
$profileName = trim($profile['full_name'] ?? '');
$nameParts = preg_split('/\s+/u', $profileName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
$initials = strtoupper(
  ($nameParts ? mb_substr($nameParts[0], 0, 1) : 'B') .
  (count($nameParts) > 1 ? mb_substr($nameParts[count($nameParts) - 1], 0, 1) : '')
);
?>
<div class="hd">
  <div>
    <span class="eyebrow">ACCOUNT</span>
    <h1>Your profile</h1>
    <p>Manage the personal details shown across your BudgetWise workspace.</p>
  </div>
</div>

<form class="profile-grid" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="profile">

  <aside class="panel profile-summary">
    <div class="profile-cover"></div>
    <div class="profile-summary-body">
      <div class="profile-avatar">
        <?php if (!empty($profile['profile_image'])): ?>
          <img src="uploads/profile/<?= e($profile['profile_image']) ?>" alt="Current profile picture">
        <?php else: ?>
          <span aria-hidden="true"><?= e($initials) ?></span>
        <?php endif; ?>
      </div>
      <span class="profile-kicker">PERSONAL PROFILE</span>
      <h2><?= e($profileName ?: 'Your name') ?></h2>
      <p><?= e($profile['email'] ?? '') ?></p>
      <div class="profile-divider"></div>
      <label class="photo-upload" for="profile-image">
        <span class="photo-upload-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" focusable="false"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v5h14v-5"></path></svg>
        </span>
        <span class="photo-upload-copy">
          <strong>Update photo</strong>
          <small>JPG, PNG, or WebP · up to 2 MB</small>
        </span>
        <input id="profile-image" type="file" name="profile_image" accept="image/jpeg,image/png,image/webp">
      </label>
      <p class="profile-note">Used only to personalize your BudgetWise profile.</p>
    </div>
  </aside>

  <section class="panel profile-form-panel" aria-labelledby="profile-details-title">
    <div class="profile-section-heading">
      <div>
        <span class="eyebrow">YOUR INFORMATION</span>
        <h2 id="profile-details-title">Personal details</h2>
      </div>
      <span class="profile-secure">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"></path></svg>
        Saved privately
      </span>
    </div>

    <div class="profile-fields">
      <label for="profile-name">Full name
        <input id="profile-name" name="full_name" maxlength="100" autocomplete="name" required value="<?= e($profile['full_name']) ?>" placeholder="Enter your full name">
      </label>
      <label for="profile-email">Email address
        <input id="profile-email" name="email" type="email" maxlength="100" autocomplete="email" required value="<?= e($profile['email']) ?>" placeholder="you@example.com">
      </label>
      <label for="profile-phone">Phone number <span class="optional-label">Optional</span>
        <input id="profile-phone" name="phone_number" maxlength="15" autocomplete="tel" value="<?= e($profile['phone_number']) ?>" placeholder="Add a contact number">
      </label>
    </div>

    <div class="profile-form-footer">
      <p>There is no account login or password for this profile.</p>
      <button class="btn" type="submit">Save changes</button>
    </div>
  </section>
</form>
