<?php

return [
    'app_name' => 'Sistema de Contatos',
    'app_url' => 'http://localhost',
    'timezone' => 'America/Sao_Paulo',
    
    // Database settings will be loaded from config.php
    'database' => [
        'default' => 'mysql'
    ],
    
    // Session settings
    'session' => [
        'lifetime' => 7200, // 2 hours
        'name' => 'CONTATOS_SESSION'
    ],
    
    // Security settings
    'security' => [
        'password_algorithm' => PASSWORD_ARGON2ID,
        'csrf_token_name' => '_token'
    ],
    
    // Upload settings
    'uploads' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'contact_images_path' => '/uploads/contacts/'
    ],
    
    // Pagination
    'pagination' => [
        'per_page' => 12,
        'max_per_page' => 50
    ]
];