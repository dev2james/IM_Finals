<?php
class Report extends Model
{
    public const TYPES = ['Expense summary', 'Allowance summary', 'Savings summary', 'Monthly overview'];
    protected const TABLE = 'reports';
    protected const PK = 'report_id';
    protected const FIELDS = ['report_type', 'generated_date', 'user_id'];

    public function validate(array $d): ?string { return in_array($d['report_type'] ?? '', self::TYPES, true) ? null : 'Choose a report type.'; }

    public function history(): array { return $this->rows('SELECT * FROM reports WHERE user_id = ? ORDER BY report_id DESC LIMIT 20', [USER_ID]); }

    public function log(string $type): int { return $this->create(['report_type' => $type, 'generated_date' => date('Y-m-d'), 'user_id' => USER_ID]); }

    /** Build a report: ['head' => [...], 'rows' => [[...]], 'total' => float|null] */
    public function build(string $type): array
    {
        $group = fn(array $r) => array_map(fn($x) => [$x['label'], (string)$x['n'], (float)$x['total']], $r);
        switch ($type) {
            case 'Expense summary':
                $rows = $group((new Expense)->byCategory());
                return ['head' => ['Category', 'Transactions', 'Total'], 'rows' => $rows, 'total' => array_sum(array_column($rows, 2))];
            case 'Savings summary':
                $rows = $group((new Saving)->byGoal());
                return ['head' => ['Goal', 'Deposits', 'Total'], 'rows' => $rows, 'total' => array_sum(array_column($rows, 2))];
            case 'Allowance summary':
                $rows = $group($this->rows('SELECT source AS label, COUNT(*) AS n, SUM(amount) AS total FROM allowance WHERE user_id = ? GROUP BY source ORDER BY total DESC', [USER_ID]));
                return ['head' => ['Source', 'Entries', 'Total'], 'rows' => $rows, 'total' => array_sum(array_column($rows, 2))];
            default:
                $r = $this->rows('SELECT m, SUM(a) a, SUM(x) x, SUM(s) s FROM (
                    SELECT DATE_FORMAT(date_received,"%Y-%m") m, amount a, 0 x, 0 s FROM allowance WHERE user_id = ?
                    UNION ALL SELECT DATE_FORMAT(expense_date,"%Y-%m"), 0, amount, 0 FROM expense WHERE user_id = ?
                    UNION ALL SELECT DATE_FORMAT(savings_date,"%Y-%m"), 0, 0, savings_amount FROM savings WHERE user_id = ?) t
                    GROUP BY m ORDER BY m DESC', [USER_ID, USER_ID, USER_ID]);
                $rows = array_map(fn($x) => [date('F Y', strtotime($x['m'] . '-01')), (float)$x['a'], (float)$x['x'], (float)$x['s'], $x['a'] - $x['x'] - $x['s']], $r);
                return ['head' => ['Month', 'Allowance', 'Expenses', 'Saved', 'Left over'], 'rows' => $rows, 'total' => null];
        }
    }
}
