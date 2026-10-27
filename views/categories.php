<div class="hd">
  <div>
    <h1>Categories</h1>
    <p>Group your expenses so spending patterns are clear.</p>
  </div>
</div>
<div class="panel">
  <h2><?= $row ? 'Edit category' : 'Add category' ?></h2>
  <form method="post" class="grid"><?= csrf_field() ?>
    <input type="hidden" name="action" value="save"><input type="hidden" name="entity" value="category"><input
      type="hidden" name="id" value="<?= (int) ($row['category_id'] ?? 0) ?>">
    <label>Name<input name="category_name" maxlength="50" required
        value="<?= e($row['category_name'] ?? '') ?>"></label>
    <label>Description<input name="description" maxlength="255" value="<?= e($row['description'] ?? '') ?>"></label>
    <div class="row"><button class="btn">Save</button><?php if ($row): ?><a class="btn ghost"
          href="index.php?page=categories">Cancel</a><?php endif; ?></div>
  </form>
</div>
<div class="panel">
  <div class="tw">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Expenses</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><b><?= e($r['category_name']) ?></b></td>
            <td><?= e($r['description']) ?></td>
            <td><?= $counter->usageCount((int) $r['category_id']) ?></td>
            <td><a class="lk action-icon" href="index.php?page=categories&edit=<?= $r['category_id'] ?>"
                aria-label="Edit category">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m16 4 4 4M4 20l4-.8L19.2 8a2.1 2.1 0 0 0-3-3L5 16.2 4 20Z"></path></svg>
              </a>
              <form method="post" class="inline" onsubmit="return confirm('Delete this category?')">
                <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="entity"
                  value="category"><input type="hidden" name="id" value="<?= $r['category_id'] ?>"><button
                  class="lk d action-icon" aria-label="Delete category">
                  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"></path></svg>
                </button></form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>