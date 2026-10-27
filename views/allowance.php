<div class="hd">
  <div>
    <h1>Allowance</h1>
    <p>Money you received, from parents or other sources.</p>
  </div>
</div>
<div class="panel">
  <h2><?= $row ? 'Edit allowance' : 'Add allowance' ?></h2>
  <form method="post" class="grid"><?= csrf_field() ?>
    <input type="hidden" name="action" value="save"><input type="hidden" name="entity" value="allowance"><input
      type="hidden" name="id" value="<?= (int) ($row['allowance_id'] ?? 0) ?>">
    <label>Amount (₱)<input name="amount" type="number" step="0.01" min="0.01" required
        value="<?= e($row['amount'] ?? '') ?>"></label>
    <label>Source<input name="source" maxlength="50" required value="<?= e($row['source'] ?? 'Parents') ?>"></label>
    <label>Date received<input name="date_received" type="date" required
        value="<?= e($row['date_received'] ?? date('Y-m-d')) ?>"></label>
    <div class="row"><button class="btn">Save</button><?php if ($row): ?><a class="btn ghost"
          href="index.php?page=allowance">Cancel</a><?php endif; ?></div>
  </form>
</div>
<div class="panel">
  <?php if (!$rows): ?>
    <div class="empty"><b>No allowance recorded</b>Add the allowance you received to start tracking your balance.</div>
  <?php else: ?>
    <div class="tw">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Source</th>
            <th>Amount</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= fdate($r['date_received']) ?></td>
              <td><?= e($r['source']) ?></td>
              <td class="in"><b><?= peso($r['amount']) ?></b></td>
              <td><a class="lk action-icon" href="index.php?page=allowance&edit=<?= $r['allowance_id'] ?>"
                  aria-label="Edit allowance">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m16 4 4 4M4 20l4-.8L19.2 8a2.1 2.1 0 0 0-3-3L5 16.2 4 20Z"></path></svg>
                </a>
                <form method="post" class="inline" onsubmit="return confirm('Delete this allowance?')">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="entity"
                    value="allowance"><input type="hidden" name="id" value="<?= $r['allowance_id'] ?>"><button
                    class="lk d action-icon" aria-label="Delete allowance">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"></path></svg>
                  </button></form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="mute">Total received: <?= peso(array_sum(array_column($rows, 'amount'))) ?></p><?php endif; ?>
</div>