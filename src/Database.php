<?php
/** Single shared PDO connection (Singleton). */
final class Database
{
    private static ?PDO $pdo = null;
    private function __construct() {}

    public static function conn(): PDO
    {
        return self::$pdo ??= new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME),
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_EMULATE_PREPARES => false]
        );
    }

    public static function transaction(callable $operation): mixed
    {
        $pdo = self::conn();
        if ($pdo->inTransaction()) {
            throw new LogicException('Nested transactions are not supported.');
        }

        $pdo->beginTransaction();
        try {
            $result = $operation();
            $pdo->commit();
            return $result;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }
}
