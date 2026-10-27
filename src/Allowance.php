<?php
class Allowance extends Model
{
    protected const TABLE = 'allowance';
    protected const PK = 'allowance_id';
    protected const FIELDS = ['amount', 'source', 'date_received', 'user_id'];

    public function validate(array $d): ?string
    {
        if (!$this->validAmount($d['amount'] ?? null)) return 'Enter an amount greater than zero.';
        if (trim($d['source'] ?? '') === '' || strlen($d['source']) > 50) return 'Enter a source (up to 50 characters).';
        if (!$this->validDate($d['date_received'] ?? null)) return 'Enter a valid date.';
        return null;
    }

    public function all(): array
    {
        return $this->rows('SELECT * FROM allowance WHERE user_id = ? ORDER BY date_received DESC, allowance_id DESC', [USER_ID]);
    }

    public function total(): float
    {
        return (float)$this->scalar('SELECT COALESCE(SUM(amount),0) FROM allowance WHERE user_id = ?', [USER_ID]);
    }
}
