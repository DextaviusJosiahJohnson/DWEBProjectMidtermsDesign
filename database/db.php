<?php
require_once __DIR__ . '/../config.php';

$dsn     = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$lockFile = __DIR__ . '/.installed';

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (\PDOException $e) {

    if ($e->getCode() == 1049 && !file_exists($lockFile)) {
        try {
            $pdo     = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, $options);
            $sqlFile = __DIR__ . '/smart_browser_state.sql';

            if (!file_exists($sqlFile)) {
                die("Installation Error: SQL file not found at $sqlFile");
            }

            $pdo->exec(file_get_contents($sqlFile));
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

            file_put_contents($lockFile, 'Installed: ' . date('Y-m-d H:i:s') . PHP_EOL);

        } catch (\PDOException $e2) {
            die("Auto-Install Failed: " . $e2->getMessage());
        }

    } elseif (file_exists($lockFile)) {
        die("Database connection failed. The installation lock exists — check your database server.");

    } else {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>