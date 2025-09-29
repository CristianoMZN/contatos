-- Migration: Create new database schema for MVC contact system
-- Author: GitHub Copilot
-- Date: 2024

-- Drop existing tables if they exist (for fresh setup)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS contact_images;
DROP TABLE IF EXISTS contact_emails;
DROP TABLE IF EXISTS contact_phones;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS company_categories;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cadastros; -- Keep for migration
SET FOREIGN_KEY_CHECKS = 1;

-- Create users table for authentication
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    two_factor_secret VARCHAR(32),
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(64),
    reset_token VARCHAR(64),
    reset_token_expires TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    lockout_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_verification_token (verification_token),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create company categories table
CREATE TABLE company_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO company_categories (name, slug, description, icon) VALUES
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
('Outros', 'outros', 'Demais categorias não especificadas', 'ellipsis-h');

-- Create contacts table
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('person', 'company') NOT NULL DEFAULT 'person',
    category_id INT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(250) NOT NULL UNIQUE,
    description TEXT,
    address TEXT,
    website VARCHAR(255),
    main_image VARCHAR(255),
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES company_categories(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_category_id (category_id),
    INDEX idx_slug (slug),
    INDEX idx_is_public (is_public),
    INDEX idx_created_at (created_at),
    CONSTRAINT chk_public_company_only CHECK (
        (is_public = FALSE) OR (is_public = TRUE AND type = 'company')
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create contact phones table
CREATE TABLE contact_phones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    department VARCHAR(100),
    is_whatsapp BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    INDEX idx_contact_id (contact_id),
    INDEX idx_is_whatsapp (is_whatsapp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create contact emails table
CREATE TABLE contact_emails (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    INDEX idx_contact_id (contact_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create contact images table
CREATE TABLE contact_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    alt_text VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    INDEX idx_contact_id (contact_id),
    INDEX idx_is_main (is_main)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sessions table for secure session management
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    data TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create rate limiting table
CREATE TABLE rate_limits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    identifier VARCHAR(100) NOT NULL, -- IP address or user ID
    type VARCHAR(50) NOT NULL,        -- login, register, etc.
    attempts INT DEFAULT 1,
    reset_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rate_limit (identifier, type),
    INDEX idx_reset_at (reset_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;