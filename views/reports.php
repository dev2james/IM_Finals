<div class="hd">
  <div>
    <h1>Reports</h1>
    <p>Summaries of your allowance, expenses and savings.</p>
  </div>
</div>
<div class="panel noprint">
  <form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="action" value="report">
    <select name="report_type" style="max-width:260px" aria-label="Report type"><?php foreach (Report::TYPES as $t): ?>
        <option <?= $type === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
    </select>
    <button class="btn">Generate report</button>
  </form>
</div>
<?php if ($result): ?>
  <div class="panel">
    <div class="hd">
      <h2><?= e($type) ?></h2><button class="btn ghost noprint" onclick="window.print()">Print</button>
    </div>
    <?php if (!$result['rows']): ?>
      <div class="empty"><b>Nothing to report yet</b>Add some records, then generate this report again.</div>
    <?php else: ?>
      <div class="tw">
        <table>
          <thead>
            <tr><?php foreach ($result['head'] as $h): ?>
                <th><?= e($h) ?></th><?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($result['rows'] as $r): ?>
              <tr><?php foreach ($r as $c): ?>
                  <td><?= is_float($c) ? peso($c) : e((string) $c) ?></td><?php endforeach; ?>
              </tr><?php endforeach; ?>
            <?php if ($result['total'] !== null): ?>
              <tr>
                <td><b>Total</b></td>
                <td></td>
                <td><b><?= peso($result['total']) ?></b></td>
              </tr><?php endif; ?>
          </tbody>
        </table>
      </div><?php endif; ?>
  </div><?php endif; ?>
<div class="panel noprint">
  <h2>Generated reports</h2>
  <?php if (!$history): ?>
    <div class="empty"><b>No reports yet</b>Choose a report type and select Generate report.</div>
  <?php else: ?>
    <div class="tw">
      <table>
        <thead>
          <tr>
            <th>Report</th>
            <th>Generated</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $h): ?>
            <tr>
              <td><?= e($h['report_type']) ?></td>
              <td><?= fdate($h['generated_date']) ?></td>
              <td><a class="lk" href="index.php?page=reports&type=<?= urlencode($h['report_type']) ?>">View</a></td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div><?php endif; ?>
</div>