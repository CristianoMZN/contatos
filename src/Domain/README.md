# Domain Layer - Tactical DDD Implementation

## 📋 Overview

This directory contains the **Domain Layer** of the Contatos application, implemented following **Domain-Driven Design (DDD) Tactical Patterns**.

The domain layer is the heart of the application, containing:
- **Pure business logic** with zero external dependencies
- **Entities** with encapsulated behavior
- **Value Objects** for immutable, self-validating values
- **Domain Services** for cross-entity logic
- **Repository Interfaces** for persistence abstraction
- **Domain Events** for decoupled communication
- **Domain Exceptions** for business rule violations

## 🏗️ Structure

```
Domain/
├── Shared/                      # Shared across all bounded contexts
│   ├── Entity/
│   │   └── AggregateRoot.php   # Base class for aggregate roots
│   ├── Event/
│   │   └── DomainEvent.php     # Event interface
│   ├── Exception/
│   │   ├── DomainException.php # Base domain exception
│   │   └── InvalidArgumentException.php
│   ├── ValueObject/
│   │   ├── Email.php           # Email with validation
│   │   ├── Phone.php           # Brazilian phone format
│   │   ├── Slug.php            # URL-friendly slugs
│   │   ├── Address.php         # Physical address
│   │   ├── Money.php           # Monetary values
│   │   └── GeoLocation.php     # GPS coordinates
│   └── Service/
│       └── SlugGenerator.php   # Slug generation service
│
├── Contact/                     # Contact bounded context
│   ├── Entity/
│   │   └── Contact.php         # Contact aggregate root
│   ├── ValueObject/
│   │   └── ContactId.php       # Contact identifier
│   ├── Repository/
│   │   └── ContactRepositoryInterface.php
│   ├── Service/
│   │   ├── GeoLocationService.php
│   │   └── ContactDuplicateChecker.php
│   ├── Event/
│   │   ├── ContactCreated.php
│   │   ├── ContactUpdated.php
│   │   └── ContactDeleted.php
│   └── Exception/
│       ├── ContactNotFoundException.php
│       └── DuplicateContactException.php
│
├── User/                        # User bounded context
│   ├── Entity/
│   │   └── User.php            # User aggregate root
│   ├── ValueObject/
│   │   └── UserId.php          # User identifier
│   ├── Repository/
│   │   └── UserRepositoryInterface.php
│   ├── Event/
│   │   └── UserRegistered.php
│   └── Exception/
│       └── UserNotFoundException.php
│
├── Category/                    # Category bounded context
│   ├── Entity/
│   │   └── Category.php        # Category aggregate root
│   ├── ValueObject/
│   │   └── CategoryId.php      # Category identifier
│   ├── Repository/
│   │   └── CategoryRepositoryInterface.php
│   ├── Event/
│   │   └── CategoryCreated.php
│   └── Exception/
│       └── CategoryNotFoundException.php
│
└── Subscription/                # Subscription bounded context
    ├── Entity/
    │   └── Subscription.php    # Subscription aggregate root
    ├── ValueObject/
    │   └── SubscriptionId.php  # Subscription identifier
    ├── Repository/
    │   └── SubscriptionRepositoryInterface.php
    └── Event/
        └── SubscriptionCreated.php
```

## 🎯 DDD Building Blocks

### 1. Entities

**Definition**: Objects defined by their identity, not their attributes.

**Characteristics**:
- ✅ Unique identifier (ID)
- ✅ Mutable state
- ✅ Lifecycle (created, modified, deleted)
- ✅ Encapsulated business logic
- ✅ Compared by identity

**Examples**:
- `Contact`: Represents a contact with email, phone, address
- `User`: Represents an authenticated user
- `Category`: Groups contacts
- `Subscription`: Premium membership

**Usage**:
```php
$contact = Contact::create(
    ContactId::generate(),
    UserId::fromString('user-123'),
    'João Silva',
    Email::fromString('joao@example.com')
);

$contact->markAsFavorite();
$contact->setLocation($geoLocation);
```

### 2. Value Objects

**Definition**: Objects defined by their attributes, not identity. Immutable.

**Characteristics**:
- ✅ No unique identifier
- ✅ Immutable (cannot change after creation)
- ✅ Self-validating
- ✅ Compared by value
- ✅ Can be shared between entities

**Examples**:
- `Email`: Validated email address
- `Phone`: Brazilian phone with formatting
- `Slug`: URL-friendly string
- `Address`: Complete address with coordinates
- `Money`: Monetary value with currency
- `GeoLocation`: GPS coordinates with distance calculations

**Usage**:
```php
$email = Email::fromString('user@example.com');
echo $email->domain(); // "example.com"

$phone = Phone::fromString('11987654321');
echo $phone->formatted(); // "+55 (11) 98765-4321"

$location = GeoLocation::fromCoordinates(-23.5505, -46.6333);
$distance = $location->distanceTo($otherLocation); // Distance in km
```

### 3. Aggregates

**Definition**: Cluster of entities and value objects treated as a single unit.

**Characteristics**:
- ✅ Has a root entity (Aggregate Root)
- ✅ Defines transaction boundary
- ✅ Enforces invariants
- ✅ Referenced by other aggregates via ID only

**Examples**:
- `Contact` aggregate: Contact + Address + GeoLocation
- `User` aggregate: User entity
- `Category` aggregate: Category entity
- `Subscription` aggregate: Subscription + Money

**Aggregate Rules**:
1. **External access only through root**
2. **References between aggregates by ID**
3. **One transaction per aggregate**
4. **Keep aggregates small and cohesive**

### 4. Repository Interfaces

**Definition**: Abstract persistence operations for aggregates.

**Characteristics**:
- ✅ Interface in Domain layer
- ✅ Implementation in Infrastructure layer
- ✅ Works like an in-memory collection
- ✅ Returns complete aggregates

**Examples**:
```php
interface ContactRepositoryInterface
{
    public function save(Contact $contact): void;
    public function findById(ContactId $id): ?Contact;
    public function findByUser(UserId $userId): array;
    public function delete(ContactId $id): void;
    public function nextIdentity(): ContactId;
}
```

### 5. Domain Services

**Definition**: Stateless services for logic that doesn't naturally belong to an entity.

**When to use**:
- ✅ Logic involves multiple entities
- ✅ Operation doesn't belong to any specific entity
- ✅ Part of the ubiquitous language

**Examples**:
- `GeoLocationService`: Find nearby contacts, calculate centers
- `ContactDuplicateChecker`: Detect duplicate contacts
- `SlugGenerator`: Generate unique slugs

**Usage**:
```php
$geoService = new GeoLocationService();
$nearby = $geoService->findNearby(
    $contacts,
    $centerLocation,
    $radiusKm = 10.0
);

$duplicateChecker = new ContactDuplicateChecker($repository);
if ($duplicateChecker->isDuplicateEmail($userId, $email)) {
    throw DuplicateContactException::withEmail($email);
}
```

### 6. Domain Events

**Definition**: Immutable objects representing something that happened in the domain.

**Characteristics**:
- ✅ Immutable
- ✅ Named in past tense (ContactCreated, not CreateContact)
- ✅ Contains relevant data
- ✅ Has ID and timestamp

**Examples**:
- `ContactCreated`: When a contact is created
- `ContactUpdated`: When a contact is modified
- `UserRegistered`: When a user signs up

**Usage**:
```php
// In entity
public static function create(...): self
{
    $contact = new self(...);
    $contact->recordEvent(new ContactCreated($id, $userId));
    return $contact;
}

// Events are released after persistence
$contact = Contact::create(...);
$repository->save($contact);

foreach ($contact->releaseEvents() as $event) {
    $eventBus->dispatch($event);
}
```

### 7. Domain Exceptions

**Definition**: Exceptions representing business rule violations.

**Examples**:
- `ContactNotFoundException`: Contact doesn't exist
- `DuplicateContactException`: Email already in use
- `InvalidArgumentException`: Invalid value provided

**Usage**:
```php
if (!$repository->exists($contactId)) {
    throw ContactNotFoundException::withId($contactId);
}

if ($duplicateChecker->isDuplicateEmail($userId, $email)) {
    throw DuplicateContactException::withEmail($email);
}
```

## 🎨 Design Principles

### 1. Dependency Rule
The Domain Layer has **ZERO dependencies** on external frameworks or infrastructure.

```
✅ ALLOWED:
- PHP standard library
- Other domain classes

❌ FORBIDDEN:
- Symfony components
- Doctrine ORM
- Firebase SDK
- Any infrastructure concerns
```

### 2. Encapsulation
Business logic is encapsulated within entities, not exposed through setters.

```php
// ❌ BAD (anemic model)
$contact->setIsFavorite(true);

// ✅ GOOD (rich domain model)
$contact->markAsFavorite();
```

### 3. Immutability
Value Objects are immutable - once created, they cannot change.

```php
// Value Objects use readonly properties
final readonly class Email
{
    private function __construct(private string $value) {}
    
    // No setters!
    public function value(): string { return $this->value; }
}
```

### 4. Self-Validation
Value Objects validate themselves on construction.

```php
final readonly class Email
{
    private function __construct(private string $value)
    {
        $this->validate(); // Validates on creation
    }
    
    private function validate(): void
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
    }
}
```

### 5. Ubiquitous Language
Code reflects the business language.

```php
// Domain concepts match business terminology
$contact->markAsFavorite();    // Not: setFavorite(true)
$contact->makePublic();         // Not: setIsPublic(true)
$subscription->hasExpired();    // Not: isValid()
```

## 📚 Usage Examples

### Creating a Contact

```php
// 1. Create Value Objects
$userId = UserId::fromString('user-123');
$email = Email::fromString('joao@example.com');
$phone = Phone::fromString('11987654321');

// 2. Create Entity
$contact = Contact::create(
    ContactId::generate(),
    $userId,
    'João Silva',
    $email,
    $phone,
    isPublic: false
);

// 3. Add business logic
$contact->markAsFavorite();
$contact->setLocation(
    GeoLocation::fromCoordinates(-23.5505, -46.6333)
);

// 4. Save (triggers events)
$repository->save($contact);
```

### Finding Nearby Contacts

```php
$userContacts = $repository->findByUser($userId);
$centerLocation = GeoLocation::fromCoordinates(-23.5505, -46.6333);

$geoService = new GeoLocationService();
$nearbyContacts = $geoService->findNearby(
    $userContacts,
    $centerLocation,
    $radiusKm = 5.0
);

foreach ($nearbyContacts as $result) {
    echo sprintf(
        "%s is %.2f km away\n",
        $result['contact']->name(),
        $result['distance']
    );
}
```

### Checking for Duplicates

```php
$duplicateChecker = new ContactDuplicateChecker($repository);

if ($duplicateChecker->isDuplicateEmail($userId, $email)) {
    throw DuplicateContactException::withEmail($email);
}

// Find similar contacts (fuzzy matching)
$similar = $duplicateChecker->findSimilar($userId, $name, $email);
if (!empty($similar)) {
    // Warn user about potential duplicates
}
```

## ✅ Testing

All domain classes are unit-testable without mocks or databases:

```php
class ContactTest extends TestCase
{
    public function test_marks_contact_as_favorite(): void
    {
        $contact = $this->createContact();
        
        $contact->markAsFavorite();
        
        $this->assertTrue($contact->isFavorite());
        $this->assertCount(2, $contact->releaseEvents()); // Created + Updated
    }
    
    public function test_making_private_removes_slug(): void
    {
        $contact = $this->createPublicContact();
        
        $contact->makePrivate();
        
        $this->assertFalse($contact->isPublic());
        $this->assertNull($contact->slug());
    }
}
```

Run tests:
```bash
./vendor/bin/phpunit tests/Unit/Domain/
```

## 🚀 Next Steps

After implementing the Domain Layer:

1. **Application Layer**: Use Cases that orchestrate domain logic
2. **Infrastructure Layer**: Repository implementations with Firestore
3. **Presentation Layer**: Controllers that use Application services

## 📖 References

- [Domain-Driven Design - Eric Evans](https://www.domainlanguage.com/ddd/)
- [Implementing Domain-Driven Design - Vaughn Vernon](https://vaughnvernon.com/)
- [DDD Reference](https://www.domainlanguage.com/ddd/reference/)

---

**Version**: 1.0.0  
**Last Updated**: 2026-01-14
