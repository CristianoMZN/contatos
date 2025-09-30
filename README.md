# Sistema de Contatos MVC - PHP Moderno

Sistema completo de gerenciamento de contatos desenvolvido com arquitetura MVC, autenticação segura, e interface moderna responsiva.

## 🚀 Funcionalidades

### ✅ Autenticação e Segurança
- ✅ **Sistema de Login/Registro**: Autenticação completa por email e senha
- ✅ **Rate Limiting**: Proteção contra ataques de força bruta
- ✅ **Sessões Seguras**: Gerenciamento seguro de sessões com regeneração automática
- ✅ **Hash de Senhas**: Argon2ID para máxima segurança
- ✅ **Middleware de Autenticação**: Proteção de rotas privadas
- 🔄 **2FA**: Autenticação de dois fatores (em desenvolvimento)
- 🔄 **Recuperação de Senha**: Sistema de reset por email (em desenvolvimento)

### ✅ Gerenciamento de Contatos
- ✅ **Contatos Pessoais**: Privados, visíveis apenas para o proprietário
- ✅ **Contatos Comerciais**: Podem ser públicos ou privados
- ✅ **Múltiplos Telefones**: Telefones infinitos com flag WhatsApp e departamento
- ✅ **Múltiplos Emails**: Emails infinitos com departamento
- ✅ **Categorização**: Sistema de categorias para empresas
- ✅ **Galeria de Imagens**: Suporte a múltiplas imagens por contato
- ✅ **URLs Amigáveis**: Slug único para cada contato (/contato/{slug})
- ✅ **Controle de Propriedade**: Usuários só podem editar/deletar seus próprios contatos

### ✅ Interface e UX
- ✅ **Design Responsivo**: Bootstrap 5.3.3 com tema escuro/claro
- ✅ **Cards Modernos**: Visualização em cards com hover effects
- ✅ **Busca Avançada**: Sistema de busca com filtros
- ✅ **Paginação Infinita**: Infinite scroll via AJAX (estrutura pronta)
- ✅ **Máscaras de Input**: Formatação automática de telefones brasileiros
- ✅ **Dashboard**: Painel com estatísticas e ações rápidas

### ✅ Arquitetura e Tecnologia
- ✅ **MVC Completo**: Separação clara de responsabilidades
- ✅ **PSR-4 Autoloading**: Composer com namespaces organizados
- ✅ **Roteamento**: Sistema de rotas com middleware support
- ✅ **PDO**: Prepared statements para máxima segurança
- ✅ **Templates**: Sistema de views desacopladas
- ✅ **Migrações**: Scripts de migração do banco de dados

### 🔄 SEO e Performance (Em Desenvolvimento)
- 🔄 **Meta Tags**: Open Graph, Twitter Card, JSON-LD
- 🔄 **Sitemap.xml**: Geração automática para contatos públicos
- ✅ **URLs Limpas**: Sistema de roteamento com .htaccess

## 📁 Estrutura do Projeto

```
📦 contatos/
├── 📁 public/                 # Ponto de entrada web
│   ├── index.php             # Front controller
│   ├── .htaccess             # Configurações Apache
│   ├── 📁 css/               # Bootstrap CSS
│   └── 📁 js/                # Bootstrap JS
├── 📁 src/                   # Código fonte da aplicação
│   ├── 📁 Controllers/       # Controladores MVC
│   │   ├── AuthController.php
│   │   ├── ContactController.php
│   │   ├── HomeController.php
│   │   └── DashboardController.php
│   ├── 📁 Models/            # Modelos de dados
│   │   ├── User.php
│   │   ├── Contact.php
│   │   └── CompanyCategory.php
│   ├── 📁 Views/             # Templates e layouts
│   │   ├── 📁 layout/        # Layout base
│   │   ├── 📁 auth/          # Autenticação
│   │   ├── 📁 contacts/      # Contatos
│   │   ├── 📁 dashboard/     # Dashboard
│   │   ├── 📁 home/          # Página inicial
│   │   └── 📁 errors/        # Páginas de erro
│   ├── 📁 Core/              # Classes fundamentais
│   │   ├── App.php           # Container de aplicação
│   │   ├── Router.php        # Sistema de rotas
│   │   ├── Database.php      # Conexão PDO
│   │   ├── Controller.php    # Controlador base
│   │   ├── Model.php         # Modelo base
│   │   └── SessionManager.php
│   ├── 📁 Middleware/        # Middlewares
│   │   └── AuthMiddleware.php
│   └── 📁 Services/          # Serviços
│       └── RateLimitService.php
├── 📁 routes/                # Definições de rotas
│   └── web.php
├── 📁 config/                # Configurações
│   └── app.php
├── 📁 migrations/            # Scripts de migração
│   └── 001_create_new_schema.sql
├── 📁 uploads/               # Arquivos enviados
│   └── 📁 contacts/          # Imagens dos contatos
├── composer.json             # Dependências PHP
├── migrate.php               # Script de migração
└── README.md                 # Esta documentação
```

## 🛠️ Instalação e Configuração

### 1. Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Composer

### 2. Configuração do Banco de Dados
```bash
# Clone o repositório
git clone https://github.com/CristianoMZN/contatos.git
cd contatos

# Instale as dependências
composer install --no-interaction

# Configure o banco de dados
cp config-new.php config.php
# Edite config.php com suas credenciais do banco
```

### 3. Configuração do config.php
```php
define('DB_SERVER', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASSWORD', 'sua_senha');
define('DB_NAME', 'contatos');
```

### 4. Execute a Migração
```bash
# Método 1: Script legado
php migrate.php

# Método 2: Usando Phinx (recomendado)
vendor/bin/phinx migrate
```

### 4.1. Gerenciamento de Migrations com Phinx
O projeto agora suporta migrations usando Phinx. Para criar uma nova migration:
```bash
# Criar nova migration
vendor/bin/phinx create NomeDaMigration

# Executar migrations pendentes
vendor/bin/phinx migrate

# Reverter última migration
vendor/bin/phinx rollback
```

Configuração do banco em `phinx.php` usando variáveis de ambiente:
- `DB_HOST` - Host do banco (padrão: localhost)
- `DB_NAME` - Nome do banco
- `DB_USER` - Usuário do banco
- `DB_PASSWORD` - Senha do banco
- `DB_PORT` - Porta do banco (padrão: 3306)

### 5. Configuração do Servidor Web

#### Apache
Configure o DocumentRoot para apontar para a pasta `/public`:
```apache
<VirtualHost *:80>
    DocumentRoot /caminho/para/contatos/public
    ServerName contatos.local
    
    <Directory /caminho/para/contatos/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name contatos.local;
    root /caminho/para/contatos/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🗄️ Estrutura do Banco de Dados

### Principais Tabelas:
- **users**: Usuários do sistema com autenticação 2FA
- **contacts**: Contatos com tipos (person/company) e visibilidade
- **contact_phones**: Múltiplos telefones por contato
- **contact_emails**: Múltiplos emails por contato
- **contact_images**: Galeria de imagens
- **company_categories**: Categorias para empresas
- **rate_limits**: Controle de rate limiting
- **sessions**: Sessões seguras

## 🔐 Recursos de Segurança

### Implementados:
- ✅ Hash de senhas com Argon2ID
- ✅ Prepared statements (PDO)
- ✅ Validação de entrada (XSS protection)
- ✅ Rate limiting para login/registro
- ✅ Sessões seguras com regeneração
- ✅ Middleware de autenticação
- ✅ Controle de propriedade de dados

### Em Desenvolvimento:
- 🔄 Tokens CSRF
- 🔄 Autenticação 2FA
- 🔄 Headers de segurança HTTP
- 🔄 Validação de upload de arquivos

## 🎨 Interface

### Recursos Visuais:
- **Tema Escuro/Claro**: Alternância com persistência
- **Cards Responsivos**: Design moderno com hover effects
- **Ícones Emoji**: Interface amigável e intuitiva
- **Bootstrap 5.3.3**: Framework CSS moderno
- **Máscaras de Input**: Formatação automática
- **Loading States**: Spinners e feedbacks visuais

## 📱 Uso da Aplicação

### Para Usuários:
1. **Registro**: Crie conta com nome, email e senha
2. **Login**: Acesse com suas credenciais
3. **Dashboard**: Visualize estatísticas e contatos
4. **Criar Contatos**: Adicione contatos pessoais ou empresariais
5. **Gerenciar**: Edite, visualize e organize seus contatos
6. **Agenda Pública**: Acesse empresas públicas de outros usuários

### Para Desenvolvedores:
- **Controllers**: Lógica de negócio organizada
- **Models**: Acesso a dados com métodos convenientes
- **Views**: Templates PHP com separação clara
- **Routes**: Sistema de roteamento flexível
- **Middleware**: Controle de acesso e validação
- **Error Handling**: Sistema de tratamento de erros com alertas amigáveis

## 🔧 Desenvolvimento

### Sistema de Mensagens Flash:
O sistema utiliza mensagens flash para exibir alertas ao usuário:
```php
// No controller
$session = $this->app->get('session');

// Mensagens de sucesso
$session->setFlash('success', 'Operação realizada com sucesso!');

// Mensagens de erro
$session->setFlash('error', 'Ocorreu um erro na operação.');

// Mensagens de aviso
$session->setFlash('warning', 'Atenção: verifique os dados informados.');

// Mensagens de informação
$session->setFlash('info', 'Informação importante para o usuário.');
```

As mensagens são exibidas automaticamente no header usando Bootstrap alerts.

### Tratamento de Erros:
```php
use App\Core\ErrorHandler;

// Tratar exceção
try {
    // código que pode gerar erro
} catch (Exception $e) {
    ErrorHandler::handleException($e, $session);
    // redirecionar ou exibir erro
}

// Mostrar erros de validação
$errors = ['Campo obrigatório', 'Email inválido'];
ErrorHandler::showValidationErrors($errors, $session);
```

### Adicionando Novas Funcionalidades:
1. **Controller**: Crie em `src/Controllers/`
2. **Model**: Crie em `src/Models/`
3. **View**: Crie em `src/Views/`
4. **Route**: Adicione em `routes/web.php`

### Exemplo de Nova Rota:
```php
$router->get('/minha-rota', 'MeuController@meuMetodo', ['Auth']);
```

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 🆕 Changelog

### v2.0.0 - Refatoração MVC (Atual)
- ✨ Arquitetura MVC completa
- ✨ Sistema de autenticação seguro
- ✨ Interface moderna responsiva
- ✨ Múltiplos contatos por usuário
- ✨ Categorização de empresas
- ✨ Dashboard com estatísticas
- ✨ Rate limiting e segurança

### v1.0.0 - Versão Original
- ✅ CRUD básico de contatos
- ✅ Paginação simples
- ✅ Bootstrap 5.3.3
- ✅ PDO básico

---

**Desenvolvido com ❤️ em PHP moderno**