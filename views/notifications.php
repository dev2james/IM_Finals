<div class="hd">
    <div>
        <h1>Notifications</h1>
        <p>Reminders about your budget and savings.</p>
    </div>
</div>
<?php foreach ($notes as $n): ?>
    <div class="note <?= e($n[0]) ?>">
        <div><b><?= e($n[1]) ?></b><?= e($n[2]) ?></div>
    </div><?php endforeach; ?>
<?php if (!array_filter($notes, fn($n) => $n[0] !== 'info')): ?>
    <div class="note ok">
        <div><b>No warnings</b>You are within your budget.</div>
    </div><?php endif; ?>