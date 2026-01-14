## contatos

Resumo curto
- Serviço simples para gerenciar contatos (CRUD).
- Objetivo: manter o projeto pequeno e testável, com padrões que facilitam contribuições e sugestões automáticas de IA.

Por que este README ajuda o Copilot a gerar PRs melhores
- Contexto claro, comandos para rodar e critérios de aceitação reduzem ambiguidade.
- Exemplos executáveis (requests, fixtures) ajudam a validar mudanças geradas automaticamente.

Conteúdo recomendado (já incluído neste repositório)
1. Visão Geral
- Stack: PHP (8.3+/8.4), Composer, PHPUnit, Symfony 7.x
- Infraestrutura: Docker, Nginx, PHP-FPM, **Firebase/Firestore** (banco de dados), Firebase Authentication
- CI/CD: GitHub Actions (CI local), Google Cloud Build (CD)
- Componentes: src/ (lógica), public/ (entry), tests/ (unit), .github/ (workflows)

2. Como rodar localmente

### Opção 1: Docker (Recomendado)
- Requisitos: Docker 20.10+, Docker Compose 2.0+
- Iniciar ambiente:
  ```bash
  cp .env.example .env
  # Configure Firebase credentials in .env
  docker-compose up -d
  ```
- Acessar: http://localhost:8080
- Ver guia completo: [DOCKER.md](DOCKER.md)

### Opção 2: PHP Nativo
- Requisitos: PHP 8.3+, Composer
- Instalar dependências:
  - composer install
- Rodar servidor local:
  - php -S 0.0.0.0:8000 -t public
- Testes:
  - composer test
- Lint / análises:
  - composer check-style
  - composer phpstan

3. Estrutura do Código
- public/         -> entry (index.php)
- src/            -> classes e serviços (em migração para Clean Architecture)
- tests/          -> testes PHPUnit
- .github/        -> templates e workflows

4. Contratos e Modelos de Dados
- Ex.: POST /contacts -> { "name": "João", "email":"a@b.com" } -> 201 + body com id
- Dados armazenados no **Firestore** (NoSQL)
- Inclua schemas/fixtures em tests/fixtures/ quando necessário

5. Regras de Código e Estilo
- Padrão: PSR-12
- Ferramentas: PHP_CodeSniffer, PHPStan, PHPUnit
- Commits: conventional commits (ex.: feat(contacts): add create endpoint)

6. Critérios de Aceitação para PRs
- Testes automatizados adicionados/atualizados para o comportamento
- Linter passando
- Documentação/README atualizados se necessário
- Checklist no template de PR preenchido

7. Como escrever prompts para Copilot
- Veja COPILOT_PROMPT.md para template de prompt e instruções práticas

## 📚 Documentação

Este projeto está em processo de refatoração para uma arquitetura moderna seguindo Clean Architecture + DDD Tático + Symfony 7.x + Firebase.

### Guias de Arquitetura

- **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** - Visão completa da arquitetura
  - Diagrama de camadas (Domain, Application, Infrastructure, Presentation)
  - Responsabilidades de cada camada
  - Estrutura de diretórios detalhada
  - Stack tecnológica completa (Symfony 7.x, Firebase, ASAAS)
  - Decisões arquiteturais (ADRs)
  - Estratégia de migração

- **[FIREBASE_SETUP.md](docs/FIREBASE_SETUP.md)** - Guia completo de integração Firebase
  - Configuração do Firebase Admin SDK
  - Estrutura de coleções do Firestore
  - Firebase Authentication (Email/Password, Google Sign-In)
  - Firebase Storage para upload de imagens
  - GCP Secret Manager para gerenciamento de secrets
  - Security Rules (Firestore e Storage)
  - Exemplos práticos de queries e código

- **[DDD_GUIDE.md](docs/DDD_GUIDE.md)** - Domain-Driven Design Tático
  - Entities vs Value Objects
  - Aggregates e boundaries
  - Repository Pattern
  - Domain Services
  - Domain Events
  - Specifications
  - Exemplos completos com User, Contact, Category

- **[LAYERS_FLOW.md](docs/LAYERS_FLOW.md)** - Fluxos entre camadas
  - Fluxo de criação de contato (passo a passo)
  - Fluxo de autenticação Firebase
  - Fluxo de busca com filtros
  - Fluxo de busca geolocalizada
  - Fluxo de upload de foto
  - Fluxo de eventos de domínio
  - Fluxo de pagamento (ASAAS)
  - Diagramas de sequência detalhados

### Outros Documentos

- **[DOCKER.md](DOCKER.md)** - Guia Docker e containerização
- **[QUICK_START.md](QUICK_START.md)** - Início rápido
- **[RECOMMENDATIONS.md](RECOMMENDATIONS.md)** - Recomendações gerais
- **[docs/GITHUB_PAGES_SETUP.md](docs/GITHUB_PAGES_SETUP.md)** - Configuração do GitHub Pages
- **[ASAAS_INTEGRATION.md](docs/ASAAS_INTEGRATION.md)** - Integração de billing/assinaturas com ASAAS
