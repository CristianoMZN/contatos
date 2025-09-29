<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\RateLimitService;

/**
 * Authentication Controller
 * Handles user registration, login, logout, password reset
 */
class AuthController extends Controller
{
    private $userModel;
    private $rateLimitService;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->rateLimitService = new RateLimitService();
    }
    
    public function showLogin(): void
    {
        $this->requireGuest();
        
        $this->view('auth/login', [
            'title' => 'Login - Contatos',
            'csrf_token' => $this->session->generateCsrfToken(),
            'error' => $this->getError(),
            'old_email' => $this->old('email')
        ]);
    }
    
    public function login(): void
    {
        $this->requireGuest();
        
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF inválido');
            $this->redirect('/login');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Rate limiting
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!$this->rateLimitService->checkLimit($clientIp, 'login', 5, 900)) { // 5 attempts per 15 minutes
            $this->withError('Muitas tentativas de login. Tente novamente em 15 minutos.');
            $this->redirect('/login');
        }
        
        // Validate input
        if (empty($email) || empty($password)) {
            $this->withError('Email e senha são obrigatórios');
            $this->withOldInput();
            $this->redirect('/login');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            $this->rateLimitService->recordAttempt($clientIp, 'login');
            $this->withError('Credenciais inválidas');
            $this->withOldInput();
            $this->redirect('/login');
        }
        
        // Check if user is locked out
        if ($this->userModel->isLockedOut($user['id'])) {
            $this->withError('Conta temporariamente bloqueada devido a múltiplas tentativas de login');
            $this->redirect('/login');
        }
        
        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            $this->userModel->incrementLoginAttempts($user['id']);
            $this->rateLimitService->recordAttempt($clientIp, 'login');
            $this->withError('Credenciais inválidas');
            $this->withOldInput();
            $this->redirect('/login');
        }
        
        // Check if 2FA is enabled
        if ($user['two_factor_enabled']) {
            $this->session->set('2fa_user_id', $user['id']);
            $this->redirect('/2fa/verify');
        }
        
        // Success login
        $this->userModel->resetLoginAttempts($user['id']);
        $this->session->login($user['id'], [
            'name' => $user['name'],
            'email' => $user['email']
        ]);
        
        $this->clearOldInput();
        $this->redirect('/dashboard');
    }
    
    public function showRegister(): void
    {
        $this->requireGuest();
        
        $this->view('auth/register', [
            'title' => 'Cadastro - Contatos',
            'csrf_token' => $this->session->generateCsrfToken(),
            'error' => $this->getError(),
            'old_name' => $this->old('name'),
            'old_email' => $this->old('email')
        ]);
    }
    
    public function register(): void
    {
        $this->requireGuest();
        
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF inválido');
            $this->redirect('/register');
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';
        
        // Rate limiting
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!$this->rateLimitService->checkLimit($clientIp, 'register', 3, 3600)) { // 3 registrations per hour
            $this->withError('Muitas tentativas de cadastro. Tente novamente em 1 hora.');
            $this->redirect('/register');
        }
        
        // Validate input
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Nome deve ter pelo menos 2 caracteres';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email válido é obrigatório';
        }
        
        if (strlen($password) < 8) {
            $errors[] = 'Senha deve ter pelo menos 8 caracteres';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Confirmação de senha não confere';
        }
        
        // Check if email already exists
        if (empty($errors) && $this->userModel->findByEmail($email)) {
            $errors[] = 'Este email já está cadastrado';
        }
        
        if (!empty($errors)) {
            $this->withError(implode('<br>', $errors));
            $this->withOldInput();
            $this->redirect('/register');
        }
        
        // Create user
        try {
            $userId = $this->userModel->createUser([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            
            $this->clearOldInput();
            $this->withSuccess('Cadastro realizado com sucesso! Faça login para continuar.');
            $this->redirect('/login');
            
        } catch (\Exception $e) {
            $this->withError('Erro ao criar conta. Tente novamente.');
            $this->withOldInput();
            $this->redirect('/register');
        }
    }
    
    public function logout(): void
    {
        $this->requireAuth();
        $this->session->logout();
        $this->redirect('/');
    }
    
    public function showForgotPassword(): void
    {
        $this->requireGuest();
        
        $this->view('auth/forgot-password', [
            'title' => 'Recuperar Senha - Contatos',
            'csrf_token' => $this->session->generateCsrfToken(),
            'error' => $this->getError(),
            'success' => $this->getSuccess()
        ]);
    }
    
    public function forgotPassword(): void
    {
        $this->requireGuest();
        
        if (!$this->validateCsrf()) {
            $this->withError('Token CSRF inválido');
            $this->redirect('/forgot-password');
        }
        
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->withError('Email válido é obrigatório');
            $this->redirect('/forgot-password');
        }
        
        // Rate limiting
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!$this->rateLimitService->checkLimit($clientIp, 'forgot_password', 3, 3600)) {
            $this->withError('Muitas tentativas. Tente novamente em 1 hora.');
            $this->redirect('/forgot-password');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if ($user) {
            $token = $this->userModel->generateResetToken($user['id']);
            
            // In a real application, send email here
            // For now, we'll just show success message
            $this->rateLimitService->recordAttempt($clientIp, 'forgot_password');
        }
        
        // Always show success message for security
        $this->withSuccess('Se o email existir em nossa base, você receberá instruções para recuperar sua senha.');
        $this->redirect('/forgot-password');
    }
}