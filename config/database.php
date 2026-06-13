<?php
// Database Configuration

define('DB_HOST', getenv('MYSQLHOST') ?: '127.0.0.1');
define('DB_PORT', getenv('MYSQLPORT') ?: '3307');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'badminton_bracket');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a secure PDO database connection.
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Auto-import database schema on remote server (like Railway) if not initialized
        if (getenv('MYSQLHOST')) {
            try {
                $check = $pdo->query("SHOW TABLES LIKE 'users'");
                if ($check->rowCount() === 0) {
                    $sqlFile = dirname(__DIR__) . '/db.sql';
                    if (file_exists($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        // Strip CREATE DATABASE and USE statements so it imports into the allocated Railway DB name
                        $sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
                        $sql = preg_replace('/USE `?[a-zA-Z0-9_-]+`?;/i', '', $sql);
                        $pdo->exec($sql);
                    }
                }
            } catch (\Exception $ex) {
                // Ignore silent errors during initialization check to not break connection
            }
        }
        
        return $pdo;
    } catch (\PDOException $e) {
        // In a real production app, hide sensitive details. 
        // For development/debugging, outputting the message helps setup.
        die("Database connection failed: " . $e->getMessage());
    }
}
