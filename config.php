<?php
$localConfigFile = __DIR__ . '/config.local.php';
$localConfig = is_file($localConfigFile) ? require $localConfigFile : [];
if (!is_array($localConfig)) {
    throw new RuntimeException('config.local.php must return a database settings array.');
}

$databaseSetting = static function (string $key, string $environment, string $default) use ($localConfig): string {
    $value = getenv($environment);
    if ($value === false) {
        $value = $localConfig[$key] ?? $default;
    }
    if (!is_string($value)) {
        throw new RuntimeException("Database setting '$key' must be a string.");
    }
    return $value;
};

define('DB_HOST', $databaseSetting('host', 'BUDGETWISE_DB_HOST', '127.0.0.1'));
define('DB_NAME', $databaseSetting('name', 'BUDGETWISE_DB_NAME', 'budgetwise'));
define('DB_USER', $databaseSetting('user', 'BUDGETWISE_DB_USER', 'root'));
define('DB_PASS', $databaseSetting('pass', 'BUDGETWISE_DB_PASS', ''));
const USER_ID = 1;