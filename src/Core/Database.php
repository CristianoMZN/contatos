<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Connection Manager
 * Singleton pattern for database connections
 */
class Database
{
    private static $instance = null;
    private $pdo = null;
    
    private function __construct()
    {
        $this->connect();
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config.php';
        
        if (file_exists($configPath)) {
            require_once $configPath;
            
            // Use the global $pdo from config.php if available
            global $pdo;
            if ($pdo !== null) {
                $this->pdo = $pdo;
                return;
            }
            
            // Fallback: try to create connection based on constants
            if (defined('DB_PATH')) {
                // SQLite configuration
                try {
                    $this->pdo = new PDO("sqlite:" . DB_PATH, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    $this->pdo->exec('PRAGMA foreign_keys = ON');
                } catch (PDOException $e) {
                    throw new \Exception("SQLite connection failed: " . $e->getMessage());
                }
            } elseif (defined('DB_SERVER')) {
                // MySQL configuration
                $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                try {
                    $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]);
                } catch (PDOException $e) {
                    throw new \Exception("MySQL connection failed: " . $e->getMessage());
                }
            } else {
                throw new \Exception("No database configuration found");
            }
        } else {
            throw new \Exception("Database configuration file not found");
        }
    }
    
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
    
    public function prepare(string $query): \PDOStatement
    {
        return $this->pdo->prepare($query);
    }
    
    public function execute(string $query, array $params = []): \PDOStatement
    {
        $stmt = $this->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
    
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    
    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    public function rollback(): bool
    {
        return $this->pdo->rollback();
    }
}