<?php

namespace App\Core;

/**
 * Main Application Class
 * Handles application bootstrapping and core functionality
 */
class App
{
    private static $instance = null;
    private $container = [];
    
    private function __construct()
    {
        $this->loadConfig();
        $this->initializeServices();
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function loadConfig(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/app.php';
        if (file_exists($configPath)) {
            $this->container['config'] = require $configPath;
        } else {
            $this->container['config'] = [
                'app_name' => 'Contatos',
                'app_url' => 'http://localhost',
                'timezone' => 'America/Sao_Paulo'
            ];
        }
    }
    
    private function initializeServices(): void
    {
        // Initialize database connection
        $this->container['db'] = function() {
            return Database::getInstance();
        };
        
        // Initialize session manager
        $this->container['session'] = function() {
            return new SessionManager();
        };
        
        // Initialize router
        $this->container['router'] = function() {
            return new Router();
        };
    }
    
    public function get(string $key)
    {
        if (!isset($this->container[$key])) {
            throw new \Exception("Service '{$key}' not found in container");
        }
        
        $service = $this->container[$key];
        return is_callable($service) ? $service() : $service;
    }
    
    public function run(): void
    {
        try {
            // Start session
            $this->get('session')->start();
            
            // Handle routing
            $router = $this->get('router');
            $router->dispatch();
            
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function handleException(\Exception $e): void
    {
        // Log error (in production)
        error_log($e->getMessage());
        
        // Show error page
        http_response_code(500);
        require dirname(__DIR__, 2) . '/src/Views/errors/500.php';
    }
}