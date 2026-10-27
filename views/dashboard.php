<div class="hd">
  <div>
    <h1>Dashboard</h1>
    <p>Here is where your money stands.</p>
  </div>
</div>
<div class="panel hero">
  <div class="big"><small>Remaining balance</small><strong><?= peso($bal) ?></strong><small>Allowance minus expenses and
      savings</small></div>
  <div class="trio">
    <div><small>Allowance received</small><strong class="in"><?= peso($al) ?></strong></div>
    <div><small>Total expenses</small><strong class="out"><?= peso($ex) ?></strong></div>
    <div><small>Total savings</small><strong><?= peso($sv) ?></strong></div>
  </div>
</div>
<div class="panel">
  <h2>This month's budget</h2>
  <?php if ($b['monthly_limit'] > 0):
    $p = $b['spent'] / $b['monthly_limit']; ?>
    <div class="bar <?= $p >= 1 ? 'x' : ($p >= .8 ? 'w' : '') ?>"><i style="width:<?= min(100, $p * 100) ?>%"></i></div>
    <p class="mute"><?= peso($b['spent']) ?> spent of <?= peso($b['monthly_limit']) ?>.
      <?= $p > 1 ? peso($b['spent'] - $b['monthly_limit']) . ' over the limit.' : peso($b['remaining_balance']) . ' left.' ?>
    </p>
  <?php else: ?>
    <p class="mute">No limit set. <a href="index.php?page=budget">Set a monthly budget</a> to track it here.</p>
  <?php endif; ?>
</div>
<div class="cols">
  <div class="panel">
    <h2>Spending by category</h2>
    <?php if (!$cats): ?>
      <div class="empty"><b>No expenses yet</b>Record your first expense to see where your money goes.</div>
    <?php else:
      $mx = (float) $cats[0]['total'];
      foreach ($cats as $c): ?>
        <div class="brow"><span><?= e($c['label']) ?></span>
          <div class="bar"><i style="width:<?= $c['total'] / $mx * 100 ?>%;background:var(--gold)"></i></div>
          <span><?= peso($c['total']) ?></span>
        </div>
      <?php endforeach; endif; ?>
  </div>
  <div class="panel">
    <h2>Recent expenses</h2>
    <?php if (!$recent): ?>
      <div class="empty"><b>Nothing recorded</b>Your latest expenses will appear here.</div>
    <?php else: ?>
      <div class="tw">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Details</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $r): ?>
              <tr>
                <td><?= fdate($r['expense_date']) ?></td>
                <td><?= e($r['description']) ?></td>
                <td class="out"><b><?= peso($r['amount']) ?></b></td>
              </tr><?php endforeach; ?>
          </tbody>
        </table>
      </div><?php endif; ?>
  </div>
</div>