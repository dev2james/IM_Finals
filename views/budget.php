<?php $lim = (float) $b['monthly_limit'];
$p = $lim > 0 ? $b['spent'] / $lim : 0; ?>
<div class="hd">
  <div>
    <h1>Budget</h1>
    <p>Set a monthly spending limit and watch the remaining balance.</p>
  </div>
</div>
<div class="panel">
  <h2>Monthly limit</h2>
  <form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="action" value="budget">
    <label style="flex:1;min-width:200px;margin:0">Limit (₱)<input name="monthly_limit" type="number" min="0"
        step="0.01" required value="<?= $lim ?: '' ?>"></label>
    <button class="btn" style="align-self:flex-end">Save budget</button>
  </form>
</div>
<div class="panel">
  <h2>Remaining this month</h2>
  <?php if ($lim > 0): ?><strong
      class="big2 <?= $b['remaining_balance'] < 0 ? 'out' : 'in' ?>"><?= peso($b['remaining_balance']) ?></strong>
    <div class="bar <?= $p >= 1 ? 'x' : ($p >= .8 ? 'w' : '') ?>"><i style="width:<?= min(100, $p * 100) ?>%"></i></div>
    <p class="mute"><?= peso($b['spent']) ?> spent of <?= peso($lim) ?> (<?= round($p * 100) ?>%).</p>
  <?php else: ?>
    <p class="mute">Enter a limit above to see what is left.</p><?php endif; ?>
</div>
<div class="panel">
  <h2>This month by category</h2>
  <?php if (!$cats): ?>
    <div class="empty"><b>No spending this month</b>Expenses you add this month show up here.</div>
  <?php else:
    foreach ($cats as $c): ?>
      <div class="brow"><span><?= e($c['label']) ?></span>
        <div class="bar"><i
            style="width:<?= min(100, $c['total'] / max($lim, $b['spent'], 1) * 100) ?>%;background:var(--gold)"></i></div>
        <span><?= peso($c['total']) ?></span>
      </div><?php endforeach; endif; ?>
</div>