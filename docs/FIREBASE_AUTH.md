# Firebase Authentication Implementation Guide

## Overview

This document describes the Firebase Authentication implementation in the Contatos application, following Clean Architecture and Domain-Driven Design principles.

## Architecture

### Layer Separation

```
┌─────────────────────────────────────────────────────┐
│           Presentation Layer                         │
│  (FirebaseAuthController, AuthMiddleware)           │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│          Application Layer                           │
│  (LoginUseCase, RegisterUseCase, DTOs)              │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│            Domain Layer                              │
│  (User Entity, Value Objects, Interfaces)           │
└──────────────────┬──────────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────────┐
│        Infrastructure Layer                          │
│  (FirebaseAuthService, FirestoreUserRepository)     │
└─────────────────────────────────────────────────────┘
```

## Components

### Domain Layer (`src/Domain/User/`)

**Value Objects:**
- `Email` - Validates and normalizes email addresses
- `UserId` - Represents Firebase UID
- `DisplayName` - User's display name with validation
- `UserRole` - Enum-like value object for roles (user, admin, premium)

**Entity:**
- `User` - Aggregate root with business logic for user management

**Repository Interface:**
- `UserRepositoryInterface` - Contract for user persistence

**Exceptions:**
- `InvalidEmailException`
- `InvalidCredentialsException`
- `UserNotFoundException`
- `UserAlreadyExistsException`

### Infrastructure Layer (`src/Infrastructure/Firebase/`)

**Services:**
- `FirebaseFactory` - Singleton factory for Firebase SDK instances
- `FirebaseAuthService` - Handles all Firebase Authentication operations
- `FirestoreUserRepository` - Implements UserRepositoryInterface using Firestore

**Key Methods:**

```php
// FirebaseAuthService
createUser(Email, password, DisplayName): string
signInWithEmailAndPassword(Email, password): array
verifyIdToken(string): array
setCustomClaims(UserId, roles): void
sendPasswordResetEmail(Email): void
```

### Application Layer (`src/Application/UseCase/User/`)

**Use Cases:**
- `RegisterUserUseCase` - Creates user in Firebase Auth + Firestore
- `LoginUserUseCase` - Authenticates and returns auth token
- `ResetPasswordUseCase` - Sends password reset email

**DTOs:**
- `RegisterUserInput`
- `LoginUserInput`
- `AuthResult` - Output containing uid, token, roles, etc.

### Presentation Layer (`src/Controllers/` & `src/Middleware/`)

**Controllers:**
- `FirebaseAuthController` - Handles HTTP requests for authentication

**Middleware:**
- `AuthMiddleware` - Validates JWT tokens or session-based auth
- `RoleMiddleware` - Enforces role-based access control

## Authentication Flow

### Registration Flow

```
1. User submits registration form
2. FirebaseAuthController validates input
3. Calls RegisterUserUseCase
4. Use case:
   a. Validates email/displayName via Value Objects
   b. Checks if user exists via repository
   c. Creates user in Firebase Auth (FirebaseAuthService)
   d. Creates User entity
   e. Saves to Firestore (FirestoreUserRepository)
   f. Sends email verification
   g. Signs in user to get token
5. Controller stores auth data in session
6. Redirects to dashboard
```

### Login Flow

```
1. User submits login form
2. FirebaseAuthController validates input
3. Calls LoginUserUseCase
4. Use case:
   a. Authenticates with Firebase (FirebaseAuthService)
   b. Gets user profile from Firestore
   c. Returns AuthResult with token and roles
5. Controller stores auth data in session
6. Redirects to dashboard
```

### Token Verification (AuthMiddleware)

```
1. Request arrives with Authorization: Bearer <token>
2. AuthMiddleware extracts token
3. Verifies token with FirebaseFactory.getAuth().verifyIdToken()
4. Extracts claims (uid, email, roles)
5. Stores in session for compatibility
6. Allows request to proceed
```

## Custom Claims (Roles)

### Available Roles

- **user** - Default role for all authenticated users
- **premium** - Users with premium subscription
- **admin** - Administrators with full access

### Setting Custom Claims

```php
$firebaseAuth->setCustomClaims(
    UserId::fromString($uid),
    [UserRole::user(), UserRole::premium()]
);
```

Custom claims are stored in Firebase Auth and included in JWT tokens. They're accessible via:

```php
$verifiedToken = $auth->verifyIdToken($token);
$roles = $verifiedToken->claims()->get('roles');
```

### Role-Based Access Control

Use `RoleMiddleware` to protect routes:

```php
// Require admin role
$router->get('/admin/users', 'AdminController@index', [
    RoleMiddleware::admin()
]);

// Require premium role
$router->get('/premium/features', 'PremiumController@index', [
    RoleMiddleware::premium()
]);

// Require any authenticated user
$router->get('/dashboard', 'DashboardController@index', [
    RoleMiddleware::user()
]);
```

## Firestore Data Structure

### Users Collection

```
/users/{uid}
├── email: string
├── displayName: string
├── roles: array<string>
├── photoURL: string | null
├── emailVerified: boolean
├── createdAt: timestamp
└── updatedAt: timestamp
```

**Security Rules:**

```javascript
match /users/{userId} {
  allow read: if request.auth.uid == userId;
  allow create: if request.auth.uid == userId;
  allow update: if request.auth.uid == userId;
  allow delete: if false; // Never allow user deletion from client
}
```

## Environment Configuration

Required environment variables:

```bash
# Firebase Configuration
FIREBASE_PROJECT_ID=contatos-app
FIREBASE_CREDENTIALS=/path/to/firebase-adminsdk.json
FIREBASE_DATABASE_URL=https://contatos-app.firebaseio.com
FIREBASE_STORAGE_BUCKET=contatos-app.appspot.com

# Firebase Authentication
FIREBASE_AUTH_ENABLED=true
FIREBASE_API_KEY=your_web_api_key_here
FIREBASE_AUTH_DOMAIN=contatos-app.firebaseapp.com

# GCP
GCP_PROJECT_ID=contatos-app
GCP_LOCATION=southamerica-east1
GOOGLE_APPLICATION_CREDENTIALS=${FIREBASE_CREDENTIALS}
```

## Setup Instructions

### 1. Create Firebase Project

```bash
# Install Firebase CLI
npm install -g firebase-tools

# Login to Firebase
firebase login

# Create project
firebase projects:create contatos-app
```

### 2. Enable Firebase Authentication

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Select your project
3. Navigate to Authentication > Sign-in method
4. Enable Email/Password provider
5. (Optional) Enable Google Sign-In

### 3. Create Service Account

1. Go to Project Settings > Service Accounts
2. Click "Generate new private key"
3. Save as `config/firebase-adminsdk.json`
4. Set `FIREBASE_CREDENTIALS` environment variable

### 4. Setup Firestore

```bash
# Initialize Firestore
firebase firestore:init

# Deploy security rules
firebase deploy --only firestore:rules

# Deploy indexes
firebase deploy --only firestore:indexes
```

### 5. Install PHP Dependencies

```bash
composer install
```

### 6. Configure Environment

```bash
cp .env.example .env
# Edit .env with your Firebase credentials
```

## API Endpoints

### POST /register
Register a new user

**Request:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "securePassword123",
  "password_confirmation": "securePassword123"
}
```

**Response:**
```json
{
  "uid": "firebase_uid_here",
  "email": "joao@example.com",
  "displayName": "João Silva",
  "token": "firebase_jwt_token",
  "roles": ["user"],
  "emailVerified": false
}
```

### POST /login
Authenticate user

**Request:**
```json
{
  "email": "joao@example.com",
  "password": "securePassword123"
}
```

**Response:**
```json
{
  "uid": "firebase_uid_here",
  "email": "joao@example.com",
  "displayName": "João Silva",
  "token": "firebase_jwt_token",
  "roles": ["user", "premium"],
  "emailVerified": true
}
```

### POST /forgot-password
Request password reset

**Request:**
```json
{
  "email": "joao@example.com"
}
```

**Response:**
```json
{
  "message": "Se o email existir, você receberá instruções para recuperar sua senha."
}
```

### POST /logout
Logout user (clears session)

## Security Considerations

### 1. Token Validation
- All tokens are verified using Firebase Admin SDK
- Expired tokens are automatically rejected
- Invalid signatures are rejected

### 2. Rate Limiting
- Login: 5 attempts per 15 minutes per IP
- Registration: 3 attempts per hour per IP
- Password reset: 3 attempts per hour per IP

### 3. CSRF Protection
- All forms include CSRF token
- Tokens are validated on submission
- Tokens rotate on login/logout

### 4. Password Requirements
- Minimum 8 characters
- Enforced by Firebase Authentication

### 5. Role Verification
- Roles are stored in Firebase custom claims
- Cannot be modified by client
- Verified on every request via JWT

## Testing

### Unit Tests (Domain Layer)

```bash
./vendor/bin/phpunit tests/Unit/Domain/User/
```

Example test:

```php
class EmailTest extends TestCase
{
    public function test_creates_valid_email(): void
    {
        $email = Email::fromString('user@example.com');
        $this->assertEquals('user@example.com', $email->toString());
    }

    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidEmailException::class);
        Email::fromString('invalid-email');
    }
}
```

### Integration Tests (Infrastructure Layer)

```bash
./vendor/bin/phpunit tests/Integration/Infrastructure/Firebase/
```

Requires Firebase Emulator or test project.

## Troubleshooting

### Error: "Firebase credentials file not found"

**Solution:** Ensure `FIREBASE_CREDENTIALS` points to valid service account JSON file.

### Error: "Permission denied"

**Solution:** Check Firestore security rules. User must be authenticated and accessing their own data.

### Error: "Invalid or expired token"

**Solution:** Token may have expired (1 hour default). Re-authenticate user.

### Error: "Email already exists"

**Solution:** User is already registered. Use password reset or login instead.

## Migration from Legacy Auth

### Strategy

1. **Phase 1:** Deploy Firebase auth alongside legacy auth
2. **Phase 2:** New users use Firebase automatically
3. **Phase 3:** Existing users migrate on next login
4. **Phase 4:** Remove legacy auth after all users migrated

### Migration Script

```php
// Migrate existing user to Firebase
function migrateUserToFirebase(array $legacyUser): string
{
    $firebaseAuth = new FirebaseAuthService(FirebaseFactory::getAuth());
    $userRepository = new FirestoreUserRepository(FirebaseFactory::getFirestore());
    
    // Create in Firebase Auth
    $uid = $firebaseAuth->createUser(
        Email::fromString($legacyUser['email']),
        'temporary_password_' . uniqid(), // Force password reset
        DisplayName::fromString($legacyUser['name'])
    );
    
    // Create User entity
    $user = User::create(
        UserId::fromString($uid),
        Email::fromString($legacyUser['email']),
        DisplayName::fromString($legacyUser['name'])
    );
    
    // Save to Firestore
    $userRepository->save($user);
    
    // Send password reset email
    $firebaseAuth->sendPasswordResetEmail($user->email());
    
    return $uid;
}
```

## References

- [Firebase Authentication Documentation](https://firebase.google.com/docs/auth)
- [Clean Architecture by Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Domain-Driven Design](https://www.domainlanguage.com/ddd/)
- [Firebase PHP SDK](https://firebase-php.readthedocs.io/)

---

**Last Updated:** 2026-01-14  
**Version:** 1.0.0
