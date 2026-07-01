<?php
/**
 * Database connection singleton (PDO).
 * Adjust these credentials to match your XAMPP MySQL setup.
 */
class Database
{
    private static ?PDO $connection = null;

    private const HOST = '127.0.0.1';
    private const DB_NAME = 'pharmacare';
    private const USER = 'root';
    private const PASS = ''; // default XAMPP MySQL root password is empty
    private const CHARSET = 'utf8mb4';

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::CHARSET;
            try {
                self::$connection = new PDO($dsn, self::USER, self::PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Never leak DB credentials/details to the browser in production.
                error_log('DB connection failed: ' . $e->getMessage());
                die('Database connection failed. Check config/database.php and that MySQL is running in XAMPP.');
            }
        }
        return self::$connection;
    }
}