<?php
/** Builds budget and savings reminders from the other classes. */
final class Notifier
{
    public function __construct(private Budget $budget, private Allowance $allowance, private Expense $expense, private Saving $saving) {}

    /** @return array<int, array{0:string,1:string,2:string}> [level, title, message] */
    public function all(): array
    {
        $n = [];
        $b = $this->budget->current();
        $limit = (float)$b['monthly_limit'];
        if ($limit > 0) {
            $pct = $b['spent'] / $limit;
            if ($pct >= 1)      $n[] = ['danger', 'Monthly budget exceeded', 'You spent ' . peso($b['spent']) . ' this month, ' . peso($b['spent'] - $limit) . ' over your limit.'];
            elseif ($pct >= .8) $n[] = ['warn', 'Close to your monthly limit', 'You used ' . round($pct * 100) . '% of your budget. ' . peso($limit - $b['spent']) . ' left.'];
        } else {
            $n[] = ['info', 'No monthly budget yet', 'Set a spending limit on the Budget page to get reminders.'];
        }
        $al = $this->allowance->total();
        if ($al > 0 && $al - $this->expense->total() - $this->saving->total() < 0)
            $n[] = ['danger', 'Spending is above your allowance', 'Expenses and savings are more than the allowance you recorded.'];
        if ($al == 0)                    $n[] = ['info', 'No allowance recorded', 'Add the allowance you received so your balance is accurate.'];
        if ($this->saving->total() == 0) $n[] = ['info', 'No savings goal yet', 'Start a savings goal to track what you set aside.'];
        return $n;
    }

    public function alertCount(): int { return count(array_filter($this->all(), fn($x) => $x[0] !== 'info')); }
}
