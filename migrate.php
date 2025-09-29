<?php

/**
 * Database Migration Script
 * Run this script to set up the new database schema
 */

require_once 'config.php';

try {
    $dsn = "mysql:host=" . DB_SERVER . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    
    echo "Connected to database: " . DB_NAME . "\n";
    
    // Read and execute migration file
    $migrationFile = __DIR__ . '/migrations/001_create_new_schema.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: {$migrationFile}");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(--|\/\*|SET|START)/', $statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed SQL statement\n";
            } catch (PDOException $e) {
                echo "⚠ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🎉 Database migration completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Set up your web server to point to the /public directory\n";
    echo "2. Access the application at your configured URL\n";
    echo "3. Create your first user account\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}