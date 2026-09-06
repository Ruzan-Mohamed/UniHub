<?php
// ─── Database Configuration ────────────────────────────────────────────────
require_once __DIR__ . '/config.php';  // defines DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET


/**
 * Returns a singleton PDO connection.
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // In production, log to file instead of exposing error details
            error_log('Database connection failed: ' . $e->getMessage());
            die(json_encode(['error' => 'Database connection failed. Please try again later.']));
        }
    }

    return $pdo;
}
