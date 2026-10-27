<?php
class Saving extends Model
{
    protected const TABLE = 'savings';
    protected const PK = 'savings_id';
    protected const FIELDS = ['savings_amount', 'savings_date', 'goal_name', 'user_id'];

    public function validate(array $d): ?string
    {
        if (!$this->validAmount($d['savings_amount'] ?? null))
            return 'Enter an amount greater than zero.';
        if (trim($d['goal_name'] ?? '') === '' || strlen($d['goal_name']) > 100)
            return 'Enter a goal name.';
        if (!$this->validDate($d['savings_date'] ?? null))
            return 'Enter a valid date.';
        return null;
    }

    public function all(): array
    {
        return $this->rows('SELECT * FROM savings WHERE user_id = ? ORDER BY savings_date DESC, savings_id DESC', [USER_ID]);
    }

    public function total(): float
    {
        return (float) $this->scalar('SELECT COALESCE(SUM(savings_amount),0) FROM savings WHERE user_id = ?', [USER_ID]);
    }

    public function byGoal(): array
    {
        return $this->rows('SELECT goal_name AS label, COUNT(*) AS n, SUM(savings_amount) AS total FROM savings WHERE user_id = ? GROUP BY goal_name ORDER BY total DESC', [USER_ID]);
    }
}
