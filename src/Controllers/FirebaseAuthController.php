<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Application\UseCase\User\LoginUserUseCase;
use App\Application\UseCase\User\RegisterUserUseCase;
use App\Application\UseCase\User\ResetPasswordUseCase;
use App\Application\UseCase\User\DTO\LoginUserInput;
use App\Application\UseCase\User\DTO\RegisterUserInput;
use App\Infrastructure\Firebase\FirebaseFactory;
use App\Infrastructure\Firebase\Auth\FirebaseAuthService;
use App\Infrastructure\Firebase\Firestore\FirestoreUserRepository;
use App\Services\RateLimitService;
use App\Domain\User\Exception\InvalidCredentialsException;
use App\Domain\User\Exception\UserAlreadyExistsException;

/**
 * Firebase Auth Controller
 * Handles authentication using Firebase Authentication
 */
class FirebaseAuthController extends Controller
{
    private LoginUserUseCase $loginUseCase;
    private RegisterUserUseCase $registerUseCase;
    private ResetPasswordUseCase $resetPasswordUseCase;
    private RateLimitService $rateLimitService;

    public function __construct()
    {
        parent::__construct();

        // Initialize Firebase services
        $firebaseAuth = new FirebaseAuthService(FirebaseFactory::getAuth());
        $userRepository = new FirestoreUserRepository(FirebaseFactory::getFirestore());

        // Initialize use cases
        $this->loginUseCase = new LoginUserUseCase($firebaseAuth, $userRepository);
        $this->registerUseCase = new RegisterUserUseCase($firebaseAuth, $userRepository);
        $this->resetPasswordUseCase = new ResetPasswordUseCase($firebaseAuth);
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

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Rate limiting
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (!$this->rateLimitService->checkLimit($clientIp, 'login', 5, 900)) {
            $this->withError('Muitas tentativas de login. Tente novamente em 15 minutos.');
            $this->redirect('/login');
        }

        // Validate input
        if (empty($email) || empty($password)) {
            $this->withError('Email e senha são obrigatórios');
            $this->withOldInput();
            $this->redirect('/login');
        }

        try {
            // Execute login use case
            $input = new LoginUserInput($email, $password);
            $result = $this->loginUseCase->execute($input);

            // Store auth data in session
            $this->session->regenerateId();
            $this->session->set('user_id', $result->uid);
            $this->session->set('user_email', $result->email);
            $this->session->set('user_name', $result->displayName);
            $this->session->set('user_authenticated', true);
            $this->session->set('firebase_token', $result->token);
            $this->session->set('user_roles', $result->roles);

            $this->clearOldInput();
            $this->redirect('/dashboard');

        } catch (InvalidCredentialsException $e) {
            $this->rateLimitService->recordAttempt($clientIp, 'login');
            $this->withError('Email ou senha inválidos');
            $this->withOldInput();
            $this->redirect('/login');
        } catch (\Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $this->withError('Erro ao fazer login. Tente novamente.');
            $this->withOldInput();
            $this->redirect('/login');
        }
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
        if (!$this->rateLimitService->checkLimit($clientIp, 'register', 3, 3600)) {
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

        if (!empty($errors)) {
            $this->withError(implode('<br>', $errors));
            $this->withOldInput();
            $this->redirect('/register');
        }

        try {
            // Execute register use case
            $input = new RegisterUserInput($email, $password, $name);
            $result = $this->registerUseCase->execute($input);

            // Store auth data in session
            $this->session->regenerateId();
            $this->session->set('user_id', $result->uid);
            $this->session->set('user_email', $result->email);
            $this->session->set('user_name', $result->displayName);
            $this->session->set('user_authenticated', true);
            $this->session->set('firebase_token', $result->token);
            $this->session->set('user_roles', $result->roles);

            $this->clearOldInput();
            $this->withSuccess('Cadastro realizado com sucesso! Verifique seu email.');
            $this->redirect('/dashboard');

        } catch (UserAlreadyExistsException $e) {
            $this->rateLimitService->recordAttempt($clientIp, 'register');
            $this->withError('Este email já está cadastrado');
            $this->withOldInput();
            $this->redirect('/register');
        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
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

        try {
            // Execute reset password use case
            $this->resetPasswordUseCase->execute($email);
            $this->rateLimitService->recordAttempt($clientIp, 'forgot_password');

            // Always show success message for security
            $this->withSuccess('Se o email existir em nossa base, você receberá instruções para recuperar sua senha.');
            $this->redirect('/forgot-password');
        } catch (\Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            $this->withSuccess('Se o email existir em nossa base, você receberá instruções para recuperar sua senha.');
            $this->redirect('/forgot-password');
        }
    }
}
