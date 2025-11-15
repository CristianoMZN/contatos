# contatos

Resumo curto
- Serviço simples para gerenciar contatos (CRUD).
- Objetivo: manter o projeto pequeno e testável, com padrões que facilitam contribuições e sugestões automáticas de IA.

Por que este README ajuda o Copilot a gerar PRs melhores
- Contexto claro, comandos para rodar e critérios de aceitação reduzem ambiguidade.
- Exemplos executáveis (requests, fixtures) ajudam a validar mudanças geradas automaticamente.

Conteúdo recomendado (já incluído neste repositório)
1. Visão Geral
- Stack: PHP (8.3+/8.4), Composer, PHPUnit
- Infraestrutura: Docker, Nginx, PHP-FPM, MariaDB
- CI/CD: GitHub Actions (build, push, deploy)
- Componentes: src/ (lógica), public/ (entry), tests/ (unit), .github/ (workflows)

2. Como rodar localmente

### Opção 1: Docker (Recomendado)
- Requisitos: Docker 20.10+, Docker Compose 2.0+
- Iniciar ambiente:
  ```bash
  cp .env.example .env
  docker-compose up -d
  ```
- Acessar: http://localhost:8080
- Ver guia completo: [DOCKER.md](DOCKER.md)

### Opção 2: PHP Nativo
- Requisitos: PHP 7.4+, Composer
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
- src/            -> classes e serviços
- tests/          -> testes PHPUnit
- .github/        -> templates e workflows

4. Contratos e Modelos de Dados
- Ex.: POST /contacts -> { "name": "João", "email":"a@b.com" } -> 201 + body com id
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