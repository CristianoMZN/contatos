<?php

/**
 * SQLite Database Migration Script
 * Run this script to set up the database schema for SQLite
 */

require_once 'config.php';

try {
    echo "Connected to SQLite database: " . DB_PATH . "\n";
    
    // Create users table for authentication
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        two_factor_secret VARCHAR(32),
        two_factor_enabled BOOLEAN DEFAULT 0,
        email_verified BOOLEAN DEFAULT 0,
        verification_token VARCHAR(64),
        reset_token VARCHAR(64),
        reset_token_expires TIMESTAMP NULL,
        last_login TIMESTAMP NULL,
        login_attempts INTEGER DEFAULT 0,
        lockout_until TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Created users table\n";
    
    // Create company categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS company_categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        icon VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Created company_categories table\n";
    
    // Insert default categories
    $pdo->exec("INSERT OR IGNORE INTO company_categories (name, slug, description, icon) VALUES
        ('Comércio', 'comercio', 'Estabelecimentos comerciais em geral', 'shop'),
        ('Mecânica', 'mecanica', 'Oficinas mecânicas e autopeças', 'wrench'),
        ('Eletrônica', 'eletronica', 'Lojas e assistências técnicas de eletrônicos', 'cpu'),
        ('Assistência Técnica', 'assistencia-tecnica', 'Serviços de reparo e manutenção', 'tools'),
        ('Contabilidade', 'contabilidade', 'Escritórios contábeis e fiscais', 'calculator'),
        ('Saúde', 'saude', 'Clínicas, hospitais e profissionais da saúde', 'heart-pulse'),
        ('Educação', 'educacao', 'Escolas, cursos e instituições de ensino', 'graduation-cap'),
        ('Alimentação', 'alimentacao', 'Restaurantes, lanchonetes e delivery', 'utensils'),
        ('Beleza', 'beleza', 'Salões de beleza, barbearias e estética', 'sparkles'),
        ('Construção Civil', 'construcao-civil', 'Construtoras, materiais de construção', 'hammer'),
        ('Tecnologia', 'tecnologia', 'Empresas de TI, desenvolvimento e consultoria', 'laptop'),
        ('Outros', 'outros', 'Demais categorias não especificadas', 'ellipsis-h')");
    echo "✓ Inserted default categories\n";
    
    // Create contacts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        type TEXT CHECK(type IN ('person', 'company')) NOT NULL DEFAULT 'person',
        category_id INTEGER NULL,
        name VARCHAR(200) NOT NULL,
        slug VARCHAR(250) NOT NULL UNIQUE,
        description TEXT,
        address TEXT,
        website VARCHAR(255),
        main_image VARCHAR(255),
        is_public BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES company_categories(id) ON DELETE SET NULL
    )");
    echo "✓ Created contacts table\n";
    
    // Create contact phones table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_phones (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        contact_id INTEGER NOT NULL,
        phone VARCHAR(20) NOT NULL,
        department VARCHAR(100),
        is_whatsapp BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
    )");
    echo "✓ Created contact_phones table\n";
    
    // Create contact emails table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_emails (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        contact_id INTEGER NOT NULL,
        email VARCHAR(150) NOT NULL,
        department VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
    )");
    echo "✓ Created contact_emails table\n";
    
    // Create contact images table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        contact_id INTEGER NOT NULL,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        file_size INTEGER NOT NULL,
        is_main BOOLEAN DEFAULT 0,
        alt_text VARCHAR(200),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
    )");
    echo "✓ Created contact_images table\n";
    
    // Create sessions table for secure session management
    $pdo->exec("CREATE TABLE IF NOT EXISTS sessions (
        id VARCHAR(128) PRIMARY KEY,
        user_id INTEGER,
        ip_address VARCHAR(45),
        user_agent TEXT,
        last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "✓ Created sessions table\n";
    
    // Create rate limiting table
    $pdo->exec("CREATE TABLE IF NOT EXISTS rate_limits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        identifier VARCHAR(100) NOT NULL,
        type VARCHAR(50) NOT NULL,
        attempts INTEGER DEFAULT 1,
        reset_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Created rate_limits table\n";
    
    // Create some sample data for testing
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', password_hash('123456', PASSWORD_DEFAULT)]);
    echo "✓ Created test user (email: test@example.com, password: 123456)\n";
    
    // Create some sample public contacts
    $userId = $pdo->lastInsertId();
    if ($userId) {
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO contacts (user_id, type, category_id, name, slug, description, is_public) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, 'company', 1, 'Loja ABC', 'loja-abc', 'Loja de produtos diversos', 1]);
        $contactId = $pdo->lastInsertId();
        
        if ($contactId) {
            // Add phone and email for the sample contact
            $pdo->prepare("INSERT OR IGNORE INTO contact_phones (contact_id, phone, is_whatsapp) VALUES (?, ?, ?)")
                ->execute([$contactId, '(11) 99999-9999', 1]);
            $pdo->prepare("INSERT OR IGNORE INTO contact_emails (contact_id, email) VALUES (?, ?)")
                ->execute([$contactId, 'contato@lojaabc.com']);
        }
        
        echo "✓ Created sample public contact\n";
    }
    
    echo "\n🎉 SQLite database migration completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Set up your web server to point to the /public directory\n";
    echo "2. Access the application at your configured URL\n";
    echo "3. Use test credentials: test@example.com / 123456\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}