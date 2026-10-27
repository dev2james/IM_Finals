<?php
/** Base class: every table class inherits create/read/update/delete from here. */
abstract class Model
{
    protected const TABLE = '';
    protected const PK = '';
    protected const FIELDS = [];
    protected PDO $db;

    public function __construct() { $this->db = Database::conn(); }

    /** Returns an error message, or null when the data is valid. */
    abstract public function validate(array $d): ?string;

    public function all(): array
    {
        return $this->db->query('SELECT * FROM ' . static::TABLE . ' ORDER BY ' . static::PK . ' DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM ' . static::TABLE . ' WHERE ' . static::PK . ' = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public function create(array $d): int
    {
        $d = $this->clean($d);
        $cols = implode(',', array_keys($d));
        $ph = implode(',', array_map(fn($k) => ":$k", array_keys($d)));
        $this->db->prepare('INSERT INTO ' . static::TABLE . " ($cols) VALUES ($ph)")->execute($d);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $d = $this->clean($d);
        unset($d['user_id']);
        $set = implode(',', array_map(fn($k) => "$k=:$k", array_keys($d)));
        $d['_id'] = $id;
        return $this->db->prepare('UPDATE ' . static::TABLE . " SET $set WHERE " . static::PK . ' = :_id')->execute($d);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM ' . static::TABLE . ' WHERE ' . static::PK . ' = ?')->execute([$id]);
    }

    protected function clean(array $d): array { return array_intersect_key($d, array_flip(static::FIELDS)); }

    protected function scalar(string $sql, array $p = []): mixed
    {
        $st = $this->db->prepare($sql);
        $st->execute($p);
        return $st->fetchColumn();
    }

    protected function rows(string $sql, array $p = []): array
    {
        $st = $this->db->prepare($sql);
        $st->execute($p);
        return $st->fetchAll();
    }

    protected function validDate(mixed $d): bool
    {
        $x = DateTime::createFromFormat('Y-m-d', (string)$d);
        return $x && $x->format('Y-m-d') === $d;
    }

    protected function validAmount(mixed $a): bool { return is_numeric($a) && (float)$a > 0 && (float)$a < 100000000; }
}
