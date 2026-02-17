<?php
$host = 'localhost';
$db   = 'smart_browser_state';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// 1. Try to connect normally
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 2. Catch "Unknown Database" error (Code 1049)
    if ($e->getCode() == 1049) {
        try {
            // Connect without selecting a DB to create it
            $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            
            // 3. Locate and Read the SQL file
            $sqlFile = __DIR__ . '/smart_browser_state.sql';
            if (!file_exists($sqlFile)) {
                die("Installation Error: SQL file not found at $sqlFile");
            }
            $sql = file_get_contents($sqlFile);

            // 4. Run the SQL to create DB and Tables
            $pdo->exec($sql);
            
            // 5. Reconnect to the new specific database
            $pdo = new PDO($dsn, $user, $pass, $options);
            
        } catch (\PDOException $e2) {
            die("Auto-Install Failed: " . $e2->getMessage());
        }
    } else {
        // If it's a different error (like wrong password), stop.
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>