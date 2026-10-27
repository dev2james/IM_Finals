<?php
class Budget extends Model
{
    protected const TABLE = 'budget';
    protected const PK = 'budget_id';
    protected const FIELDS = ['monthly_limit', 'remaining_balance', 'user_id'];

    public function validate(array $d): ?string
    {
        return (is_numeric($d['monthly_limit'] ?? null) && (float)$d['monthly_limit'] >= 0 && (float)$d['monthly_limit'] < 100000000)
            ? null : 'Enter a limit of zero or more.';
    }

    /** The user's budget row plus this month's spending and remaining balance. */
    public function current(): array
    {
        $row = $this->rows('SELECT * FROM budget WHERE user_id = ? LIMIT 1', [USER_ID])[0] ?? null;
        if (!$row) {
            $id = $this->create(['monthly_limit' => 0, 'remaining_balance' => 0, 'user_id' => USER_ID]);
            $row = $this->find($id);
        }
        $row['spent'] = (new Expense)->total(date('Y-m'));
        $row['remaining_balance'] = (float)$row['monthly_limit'] - $row['spent'];
        return $row;
    }

    public function setLimit(float $limit): void
    {
        $b = $this->current();
        $this->update((int)$b['budget_id'], ['monthly_limit' => $limit, 'remaining_balance' => $limit - $b['spent']]);
    }
}
