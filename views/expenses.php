<div class="hd">
  <div>
    <h1>Expenses</h1>
    <p>Record and find everything you spent.</p>
  </div>
</div>
<div class="panel">
  <h2><?= $row ? 'Edit expense' : 'Add expense' ?></h2>
  <form method="post" class="grid"><?= csrf_field() ?>
    <input type="hidden" name="action" value="save"><input type="hidden" name="entity" value="expense"><input
      type="hidden" name="id" value="<?= (int) ($row['expense_id'] ?? 0) ?>">
    <label>Amount (₱)<input name="amount" type="number" step="0.01" min="0.01" required
        value="<?= e($row['amount'] ?? '') ?>"></label>
    <label>Description<input name="description" maxlength="255" required
        value="<?= e($row['description'] ?? '') ?>"></label>
    <label>Category<select name="category_id" required><?php foreach ($cats as $c): ?>
          <option value="<?= $c['category_id'] ?>" <?= ($row['category_id'] ?? 0) == $c['category_id'] ? 'selected' : '' ?>>
            <?= e($c['category_name']) ?>
          </option><?php endforeach; ?>
      </select></label>
    <label>Date<input name="expense_date" type="date" required
        value="<?= e($row['expense_date'] ?? date('Y-m-d')) ?>"></label>
    <div class="row"><button class="btn">Save</button><?php if ($row): ?><a class="btn ghost"
          href="index.php?page=expenses">Cancel</a><?php endif; ?></div>
  </form>
</div>
<div class="panel">
  <form method="get" class="filters"><input type="hidden" name="page" value="expenses">
    <input name="q" placeholder="Search description or category" aria-label="Search" value="<?= e($f['q']) ?>">
    <select name="c" aria-label="Category">
      <option value="0">All categories</option><?php foreach ($cats as $c): ?>
        <option value="<?= $c['category_id'] ?>" <?= $f['c'] == $c['category_id'] ? 'selected' : '' ?>>
          <?= e($c['category_name']) ?>
        </option><?php endforeach; ?>
    </select>
    <input type="date" name="from" aria-label="From date" value="<?= e($f['from']) ?>"><input type="date" name="to"
      aria-label="To date" value="<?= e($f['to']) ?>">
    <button class="btn">Filter</button>
  </form>
  <?php if (!$rows): ?>
    <div class="empty"><b>No expenses found</b>Add an expense above or change your filters.</div>
  <?php else: ?>
    <div class="tw">
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Category</th>
            <th>Amount</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= fdate($r['expense_date']) ?></td>
              <td><?= e($r['description']) ?></td>
              <td><?= e($r['category_name']) ?></td>
              <td class="out"><b><?= peso($r['amount']) ?></b></td>
              <td><a class="lk action-icon" href="index.php?page=expenses&edit=<?= $r['expense_id'] ?>"
                  aria-label="Edit expense">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m16 4 4 4M4 20l4-.8L19.2 8a2.1 2.1 0 0 0-3-3L5 16.2 4 20Z"></path></svg>
                </a>
                <form method="post" class="inline" onsubmit="return confirm('Delete this expense?')">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="entity"
                    value="expense"><input type="hidden" name="id" value="<?= $r['expense_id'] ?>"><button
                    class="lk d action-icon" aria-label="Delete expense">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"></path></svg>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="mute"><?= count($rows) ?> expense(s) totaling <?= peso(array_sum(array_column($rows, 'amount'))) ?></p>
  <?php endif; ?>
</div>