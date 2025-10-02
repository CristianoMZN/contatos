<?php

namespace App\Core;

/**
 * Error Handler
 * Provides consistent error handling across the application
 */
class ErrorHandler
{
    /**
     * Handle exception and show user-friendly message
     */
    public static function handleException(\Exception $e, SessionManager $session = null): void
    {
        // Log the error
        error_log(sprintf(
            "[%s] Exception: %s in %s:%d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
        
        // Set user-friendly message
        if ($session) {
            $session->setFlash('error', 'Ocorreu um erro inesperado. Por favor, tente novamente.');
        }
    }
    
    /**
     * Handle error and show user-friendly message
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline, SessionManager $session = null): bool
    {
        // Log the error
        error_log(sprintf(
            "[%s] Error [%d]: %s in %s:%d",
            date('Y-m-d H:i:s'),
            $errno,
            $errstr,
            $errfile,
            $errline
        ));
        
        // Set user-friendly message for severe errors
        if ($session && in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $session->setFlash('error', 'Ocorreu um erro no sistema. Nossa equipe foi notificada.');
        }
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Show validation error messages
     */
    public static function showValidationErrors(array $errors, SessionManager $session): void
    {
        $message = implode('<br>', array_map('htmlspecialchars', $errors));
        $session->setFlash('error', $message);
    }
}
