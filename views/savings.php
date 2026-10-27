<div class="hd">
  <div>
    <h1>Savings</h1>
    <p>Money you set aside for your goals.</p>
  </div>
</div>
<?php if ($goals): ?>
  <div class="goals"><?php foreach ($goals as $g): ?>
      <div class="goal"><span class="mute"><?= e($g['label']) ?></span><strong><?= peso($g['total']) ?></strong><span
          class="mute"><?= $g['n'] ?> deposit<?= $g['n'] > 1 ? 's' : '' ?></span></div><?php endforeach; ?>
  </div><?php endif; ?>
<div class="panel">
  <h2><?= $row ? 'Edit savings' : 'Add savings' ?></h2>
  <form method="post" class="grid"><?= csrf_field() ?>
    <input type="hidden" name="action" value="save"><input type="hidden" name="entity" value="saving"><input
      type="hidden" name="id" value="<?= (int) ($row['savings_id'] ?? 0) ?>">
    <label>Goal name<input name="goal_name" maxlength="100" list="goals" required
        value="<?= e($row['goal_name'] ?? '') ?>"></label>
    <datalist id="goals"><?php foreach ($goals as $g): ?>
        <option value="<?= e($g['label']) ?>"><?php endforeach; ?>
    </datalist>
    <label>Amount saved (₱)<input name="savings_amount" type="number" step="0.01" min="0.01" required
        value="<?= e($row['savings_amount'] ?? '') ?>"></label>
    <label>Date<input name="savings_date" type="date" required
        value="<?= e($row['savings_date'] ?? date('Y-m-d')) ?>"></label>
    <div class="row"><button class="btn">Save</button><?php if ($row): ?><a class="btn ghost"
          href="index.php?page=savings">Cancel</a><?php endif; ?></div>
  </form>
</div>
<div class="panel">
  <h2>History</h2>
  <?php if (!$rows): ?>
    <div class="empty"><b>No savings yet</b>Add a goal name and amount to start saving.</div>
  <?php else: ?>
    <div class="tw">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Goal</th>
            <th>Amount</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= fdate($r['savings_date']) ?></td>
              <td><?= e($r['goal_name']) ?></td>
              <td class="in"><b><?= peso($r['savings_amount']) ?></b></td>
              <td><a class="lk action-icon" href="index.php?page=savings&edit=<?= $r['savings_id'] ?>"
                  aria-label="Edit savings entry">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m16 4 4 4M4 20l4-.8L19.2 8a2.1 2.1 0 0 0-3-3L5 16.2 4 20Z"></path></svg>
                </a>
                <form method="post" class="inline" onsubmit="return confirm('Delete this entry?')"><?= csrf_field() ?><input
                    type="hidden" name="action" value="delete"><input type="hidden" name="entity" value="saving"><input
                    type="hidden" name="id" value="<?= $r['savings_id'] ?>"><button class="lk d action-icon"
                    aria-label="Delete savings entry">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"></path></svg>
                  </button></form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div><?php endif; ?>
</div>