<?php
/**
 * BaseModel.php
 * 
 * This base model initializes a PDO connection to the MySQL (MariaDB) database.
 * 
 * Expected Environment Variables:
 *   DB_HOST      - Hostname of the database server (default: 'mysql')
 *   DB_NAME      - Name of the database (default: 'grand_transmission_auto')
 *   DB_USER      - Database user (default: 'developer')
 *   DB_PASSWORD  - Database password (default: 'secret123')
 *   DB_CHARSET   - Character set (default: 'utf8mb4')
 */

class BaseModel
{
    // A static PDO connection shared across all instances
    protected static $pdo;

    public function __construct()
    {
        // Only initialize the connection if it hasn't been already.
        if (!self::$pdo) {
            // Retrieve environment variables (or use default values)
            $host    = getenv("DB_HOST") ?: 'mysql';
            $db      = getenv("DB_NAME") ?: 'grand_transmission_auto';
            $user    = getenv("DB_USER") ?: 'developer';
            $pass    = getenv("DB_PASSWORD") ?: 'secret123';
            $charset = getenv("DB_CHARSET") ?: 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
    }

    /**
     * Optionally, you can add a getter method to access the PDO connection.
     * This allows you to use $this->getPDO() instead of directly accessing the static property.
     */
    public function getPDO()
    {
        return self::$pdo;
    }
}
?>
