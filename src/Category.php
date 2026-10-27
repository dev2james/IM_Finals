<?php
class Category extends Model
{
    protected const TABLE = 'categories';
    protected const PK = 'category_id';
    protected const FIELDS = ['category_name', 'description'];

    public function validate(array $d): ?string
    {
        $n = trim($d['category_name'] ?? '');
        if ($n === '' || strlen($n) > 50) return 'Enter a category name (up to 50 characters).';
        return null;
    }

    public function all(): array { return $this->db->query('SELECT * FROM categories ORDER BY category_name')->fetchAll(); }

    public function usageCount(int $id): int { return (int)$this->scalar('SELECT COUNT(*) FROM expense WHERE category_id = ?', [$id]); }

    public function delete(int $id): bool
    {
        if ($this->usageCount($id) > 0) throw new RuntimeException('This category has expenses. Move or delete them first.');
        return parent::delete($id);
    }
}
