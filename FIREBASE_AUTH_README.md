# Firebase Authentication - Quick Start

## Overview

The Contatos application now uses **Firebase Authentication** for secure, centralized user management. This implementation follows Clean Architecture and Domain-Driven Design principles.

## Key Features

✅ **Centralized Authentication** - Firebase manages all auth operations  
✅ **JWT Tokens** - Stateless authentication with Firebase ID tokens  
✅ **Custom Claims** - Role-based access control (user, admin, premium)  
✅ **Clean Architecture** - Properly separated domain, application, and infrastructure layers  
✅ **Type Safety** - Full PHP 8.1+ type hints and strict types  
✅ **Tested** - Comprehensive unit tests for domain layer

## Quick Start

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Firebase

Create a Firebase project at [console.firebase.google.com](https://console.firebase.google.com) and download the service account JSON.

Update `.env`:

```bash
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS=/path/to/firebase-adminsdk.json
FIREBASE_AUTH_ENABLED=true
```

### 3. Enable Authentication

In Firebase Console:
1. Go to Authentication > Sign-in method
2. Enable Email/Password provider

### 4. Run Tests

```bash
./vendor/bin/phpunit
```

## Usage Examples

### Registration

```php
use App\Application\UseCase\User\RegisterUserUseCase;
use App\Application\UseCase\User\DTO\RegisterUserInput;

$input = new RegisterUserInput(
    email: 'user@example.com',
    password: 'securePassword123',
    displayName: 'João Silva'
);

$result = $registerUseCase->execute($input);

// Returns: AuthResult with uid, token, roles, etc.
```

### Login

```php
use App\Application\UseCase\User\LoginUserUseCase;
use App\Application\UseCase\User\DTO\LoginUserInput;

$input = new LoginUserInput(
    email: 'user@example.com',
    password: 'securePassword123'
);

$result = $loginUseCase->execute($input);

// Store token in session or return to client
```

### Role-Based Access Control

Protect routes with role middleware:

```php
// Require admin role
$router->get('/admin/users', 'AdminController@index', [
    RoleMiddleware::admin()
]);

// Require premium role (admin also has access)
$router->get('/premium/features', 'PremiumController@index', [
    RoleMiddleware::premium()
]);
```

### JWT Token Authentication

Include token in API requests:

```bash
curl -H "Authorization: Bearer <firebase_token>" \
     https://api.contatos.example.com/dashboard
```

The `AuthMiddleware` automatically validates the token and extracts user data.

## Architecture

```
Domain Layer (Business Logic)
├── Value Objects (Email, UserId, DisplayName, UserRole)
├── Entity (User)
├── Repository Interface (UserRepositoryInterface)
└── Exceptions

Application Layer (Use Cases)
├── RegisterUserUseCase
├── LoginUserUseCase
├── ResetPasswordUseCase
└── DTOs (Input/Output)

Infrastructure Layer (Firebase Integration)
├── FirebaseFactory
├── FirebaseAuthService
└── FirestoreUserRepository

Presentation Layer (HTTP)
├── FirebaseAuthController
└── Middleware (AuthMiddleware, RoleMiddleware)
```

## Security Features

- ✅ **Rate Limiting** - 5 login attempts per 15 minutes
- ✅ **CSRF Protection** - All forms include CSRF tokens
- ✅ **Password Requirements** - Minimum 8 characters
- ✅ **Email Verification** - Optional email verification
- ✅ **JWT Validation** - All tokens verified by Firebase
- ✅ **Role Verification** - Custom claims cannot be modified by clients

## Custom Claims (Roles)

### Available Roles

- **user** - Default role for all authenticated users
- **premium** - Users with premium subscription
- **admin** - Administrators with full access

### Setting Roles

```php
use App\Infrastructure\Firebase\Auth\FirebaseAuthService;
use App\Domain\User\ValueObject\UserRole;

$firebaseAuth->setCustomClaims(
    UserId::fromString($uid),
    [UserRole::user(), UserRole::premium()]
);
```

Roles are stored in Firebase Auth and included in JWT tokens automatically.

## Testing

### Run All Tests

```bash
./vendor/bin/phpunit
```

### Run Specific Test Suite

```bash
# Domain layer tests
./vendor/bin/phpunit tests/Unit/Domain/

# Value object tests
./vendor/bin/phpunit tests/Unit/Domain/User/ValueObject/
```

### Code Coverage

```bash
./vendor/bin/phpunit --coverage-html coverage/
```

## Documentation

- 📖 **[FIREBASE_AUTH.md](docs/FIREBASE_AUTH.md)** - Complete authentication guide
- 📖 **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** - System architecture overview
- 📖 **[DDD_GUIDE.md](docs/DDD_GUIDE.md)** - Domain-Driven Design guide
- 📖 **[FIREBASE_SETUP.md](docs/FIREBASE_SETUP.md)** - Firebase setup instructions

## API Endpoints

### POST /register
Register a new user
- **Body:** `{ "name", "email", "password", "password_confirmation" }`
- **Returns:** User data with Firebase token

### POST /login
Authenticate user
- **Body:** `{ "email", "password" }`
- **Returns:** User data with Firebase token

### POST /logout
Logout user (clears session)

### POST /forgot-password
Request password reset
- **Body:** `{ "email" }`
- **Returns:** Success message

## Troubleshooting

### "Firebase credentials file not found"

Ensure `FIREBASE_CREDENTIALS` points to valid service account JSON.

### "Permission denied"

Check Firestore security rules. Ensure user is authenticated.

### "Invalid or expired token"

Token expired (1 hour default). User must re-authenticate.

## Migration from Legacy Auth

The new Firebase authentication works alongside the legacy auth system. New users automatically use Firebase, while existing users can be migrated gradually.

See [FIREBASE_AUTH.md](docs/FIREBASE_AUTH.md) for migration guide.

## Support

For issues or questions:
1. Check documentation in `docs/`
2. Review [Firebase docs](https://firebase.google.com/docs/auth)
3. Open an issue on GitHub

---

**Version:** 1.0.0  
**Last Updated:** 2026-01-14
