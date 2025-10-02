# GitHub Copilot Instructions - Contatos Project

## 🎯 Objetivo do Projeto

Sistema de gerenciamento de contatos em PHP com arquitetura MVC moderna, autenticação segura e interface responsiva com Bootstrap 5.

## 📋 Padrões de Código

### PHP Standards
- **Versão mínima**: PHP 7.4+
- **Coding Standard**: PSR-12
- **Autoloading**: PSR-4 (`App\` namespace)
- **Type hints**: Obrigatório para parâmetros e retornos
- **Métodos**: Máximo 50 linhas, extrair lógica complexa

### Arquitetura MVC
```
src/
├── Controllers/    # Lógica de controle
├── Models/         # Modelos de dados (PDO)
├── Views/          # Templates PHP
├── Core/           # Classes fundamentais
├── Middleware/     # Middlewares de rota
└── Services/       # Serviços reutilizáveis
```

## 🔒 Segurança (CRÍTICO)

### Banco de Dados
```php
// ✅ SEMPRE usar prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// ❌ NUNCA fazer concatenação direta
$query = "SELECT * FROM users WHERE id = '$id'"; // SQL INJECTION!
```

### Output
```php
// ✅ SEMPRE escapar output para HTML
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// ✅ Para arrays, não inserir HTML antes de escapar
$errors = ['Erro 1', 'Erro 2'];
$session->setFlash('error', $errors); // Array, não string HTML

// ❌ NUNCA enviar HTML não escapado
echo $userInput; // XSS VULNERABILITY!
```

### Formulários
```php
// ✅ SEMPRE validar CSRF token
if (!$this->session->validateCsrfToken($_POST['_token'])) {
    throw new Exception('Invalid CSRF token');
}

// ✅ SEMPRE incluir token no form
<input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?>">
```

### Senhas
```php
// ✅ Usar password_hash e password_verify
$hash = password_hash($password, PASSWORD_ARGON2ID);
if (password_verify($password, $hash)) { /* ok */ }

// ❌ NUNCA armazenar senhas em texto puro
$user->password = $password; // INSEGURO!
```

## 🎨 Views e Templates

### Flash Messages
```php
// ✅ Armazenar array de mensagens
$session->setFlash('error', ['Erro 1', 'Erro 2']);

// ✅ No template, iterar e escapar
foreach ($session->getFlash('error', []) as $error) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
}

// ❌ NUNCA armazenar HTML pré-formatado
$session->setFlash('error', '<br>'.implode('<br>', $errors)); // Double-escaping!
```

### Bootstrap Classes
- Use classes utilitárias do Bootstrap 5.3.3
- Prefira `d-flex`, `align-items-center`, `justify-content-between`
- Use responsividade: `col-md-6`, `d-none d-md-block`

## 🧪 Testes e Validação

### Antes de Commitar
```bash
# Validar composer.json
composer validate --strict

# Verificar sintaxe PHP
find src -name "*.php" -exec php -l {} \;

# Verificar padrões de código (se PHPStan instalado)
vendor/bin/phpstan analyse src
```

### Em PRs
- Sempre incluir screenshots de mudanças visuais
- Documentar breaking changes
- Listar dependências adicionadas
- Atualizar README se necessário

## 📦 Dependências e Composer

### Instalação Automatizada
```bash
# Para CI/CD e automação (SEM INTERAÇÃO)
composer install --no-interaction --no-dev --prefer-dist

# Para desenvolvimento local (COM INTERAÇÃO)
composer install
```

### Adicionar Nova Dependência
```bash
# Produção
composer require vendor/package

# Desenvolvimento
composer require --dev vendor/package

# Documentar no README.md
# Justificar a necessidade no PR
```

## 🚨 Tratamento de Erros

### Logging
```php
// ✅ Usar error_log para logs
error_log(sprintf(
    "[%s] %s in %s:%d",
    date('Y-m-d H:i:s'),
    $e->getMessage(),
    $e->getFile(),
    $e->getLine()
));

// ✅ Mensagem amigável para usuário
$session->setFlash('error', 'Ocorreu um erro. Tente novamente.');

// ❌ NUNCA expor stack traces
echo $e->getTraceAsString(); // EXPÕE ESTRUTURA!
```

### Páginas de Erro
- Sempre ter fallback inline HTML para erro 500
- Nunca assumir que arquivos de erro existem
- Redirecionar para home com flash message quando possível

## 🎯 Convenções de Nomes

### Classes
```php
// Controllers: Sufixo Controller
class ContactController extends Controller { }

// Models: Singular
class Contact extends Model { }

// Services: Sufixo Service
class EmailService { }

// Middleware: Sufixo Middleware
class AuthMiddleware { }
```

### Métodos
```php
// Ações de controller: verbo + nome
public function createContact() { }
public function updateContact($id) { }
public function deleteContact($id) { }

// Getters: get + nome
public function getName() { }

// Setters: set + nome
public function setName($name) { }

// Booleanos: is/has + adjetivo
public function isActive() { }
public function hasPermission() { }
```

### Rotas
```php
// RESTful quando possível
GET    /contacts           - index
GET    /contacts/create    - create form
POST   /contacts           - store
GET    /contacts/{id}      - show
GET    /contacts/{id}/edit - edit form
PUT    /contacts/{id}      - update
DELETE /contacts/{id}      - destroy

// URLs amigáveis para público
GET    /contato/{slug}     - public view
```

## 📝 Documentação

### Comentários
```php
// ✅ Comentar o "porquê", não o "o que"
// Checking CSRF because this endpoint allows file uploads
if (!$this->validateCsrf()) { }

// ❌ Comentários óbvios
// Get the user ID
$userId = $user->getId();
```

### PHPDoc
```php
/**
 * Create a new contact for the authenticated user
 * 
 * @param array $data Contact data (name, email, phone, etc)
 * @return Contact The created contact instance
 * @throws ValidationException If data is invalid
 * @throws DatabaseException If database operation fails
 */
public function createContact(array $data): Contact
{
    // Implementation
}
```

## 🔄 Git e Commits

### Mensagens de Commit
```
feat: Add dark mode toggle to navbar
fix: Resolve double-escaping in flash messages
refactor: Extract flash message rendering to helper
docs: Update README with installation instructions
chore: Update composer dependencies
```

### Arquivos a Ignorar em Reviews
- `composer.lock` (marcado como linguist-generated)
- `vendor/`
- Arquivos temporários de teste (`test-*.php`)

## ⚠️ Armadilhas Comuns

### 1. Double-Escaping
```php
// ❌ ERRADO
$html = htmlspecialchars($text);
echo htmlspecialchars($html); // &amp;lt;

// ✅ CORRETO
echo htmlspecialchars($text);
```

### 2. Arquivos Inexistentes
```php
// ❌ ERRADO
include 'view-that-might-not-exist.php';

// ✅ CORRETO
if (file_exists($viewPath)) {
    include $viewPath;
} else {
    // Fallback inline HTML
}
```

### 3. Strings vs Arrays em Flash
```php
// ❌ ERRADO
$session->setFlash('error', implode('<br>', $errors));

// ✅ CORRETO
$session->setFlash('error', $errors);
```

### 4. Lógica Complexa em Views
```php
// ❌ ERRADO (50+ linhas de lógica em view)
<?php
if ($condition1) {
    foreach ($items as $item) {
        // Complex processing...
    }
}
?>

// ✅ CORRETO (lógica em helper/controller)
<?php echo ViewHelper::renderItems($items); ?>
```

## 🌐 Internacionalização

- Mensagens em português brasileiro (pt-BR)
- Formatação de data: `d/m/Y H:i:s`
- Formatação de telefone: `(11) 98765-4321`
- Moeda: `R$ 1.234,56`

## 🚀 Performance

### Database
- Usar índices em colunas de busca
- Limitar queries com `LIMIT`
- Evitar `SELECT *`, especificar colunas
- Cache de queries frequentes quando apropriado

### Views
- Minimizar lógica em templates
- Usar componentes reutilizáveis
- Lazy loading de imagens: `loading="lazy"`

## ✅ Checklist para PRs

- [ ] Código segue PSR-12
- [ ] Type hints em todos os métodos
- [ ] Prepared statements para database
- [ ] Output escapado com htmlspecialchars()
- [ ] CSRF tokens em formulários
- [ ] Erros logados com error_log()
- [ ] Mensagens amigáveis para usuários
- [ ] Syntax check com `php -l`
- [ ] composer.json válido
- [ ] README atualizado (se necessário)
- [ ] Screenshots (se mudança visual)
- [ ] Testes manuais realizados

## 📚 Recursos

- [PSR-12](https://www.php-fig.org/psr/psr-12/)
- [Bootstrap 5.3 Docs](https://getbootstrap.com/docs/5.3/)
- [PHP PDO](https://www.php.net/manual/en/book.pdo.php)
- [OWASP PHP Security](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)

---

**Última atualização**: 2025-10-02
**Versão**: 1.0.0
