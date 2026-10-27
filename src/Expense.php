<?php
class Expense extends Model
{
    protected const TABLE = 'expense';
    protected const PK = 'expense_id';
    protected const FIELDS = ['amount', 'description', 'expense_date', 'user_id', 'category_id'];

    public function validate(array $d): ?string
    {
        if (!$this->validAmount($d['amount'] ?? null))
            return 'Enter an amount greater than zero.';
        if (trim($d['description'] ?? '') === '' || strlen($d['description']) > 255)
            return 'Enter a description.';
        if (!(new Category)->find((int) ($d['category_id'] ?? 0)))
            return 'Choose a category.';
        if (!$this->validDate($d['expense_date'] ?? null))
            return 'Enter a valid date.';
        return null;
    }

    /** List expenses with optional search text, category and date range. */
    public function search(string $q = '', int $cat = 0, string $from = '', string $to = ''): array
    {
        $sql = 'SELECT e.*, c.category_name FROM expense e JOIN categories c ON c.category_id = e.category_id WHERE e.user_id = :u';
        $p = ['u' => USER_ID];
        if ($q !== '') {
            $sql .= ' AND (e.description LIKE :q1 OR c.category_name LIKE :q2)';
            $p['q1'] = $p['q2'] = "%$q%";
        }
        if ($cat > 0) {
            $sql .= ' AND e.category_id = :c';
            $p['c'] = $cat;
        }
        if ($from !== '') {
            $sql .= ' AND e.expense_date >= :f';
            $p['f'] = $from;
        }
        if ($to !== '') {
            $sql .= ' AND e.expense_date <= :t';
            $p['t'] = $to;
        }
        return $this->rows($sql . ' ORDER BY e.expense_date DESC, e.expense_id DESC', $p);
    }

    /** Total spent. Pass 'YYYY-MM' for a single month. */
    public function total(?string $ym = null): float
    {
        $sql = 'SELECT COALESCE(SUM(amount),0) FROM expense WHERE user_id = ?';
        $p = [USER_ID];
        if ($ym) {
            $sql .= ' AND DATE_FORMAT(expense_date,"%Y-%m") = ?';
            $p[] = $ym;
        }
        return (float) $this->scalar($sql, $p);
    }

    public function byCategory(?string $ym = null): array
    {
        $sql = 'SELECT c.category_name AS label, COUNT(*) AS n, SUM(e.amount) AS total FROM expense e
                JOIN categories c ON c.category_id = e.category_id WHERE e.user_id = ?';
        $p = [USER_ID];
        if ($ym) {
            $sql .= ' AND DATE_FORMAT(e.expense_date,"%Y-%m") = ?';
            $p[] = $ym;
        }
        return $this->rows($sql . ' GROUP BY c.category_id, c.category_name ORDER BY total DESC', $p);
    }
}
