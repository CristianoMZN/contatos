# contatos

Resumo curto
- Serviço simples para gerenciar contatos (CRUD).
- Objetivo: manter o projeto pequeno e testável, com padrões que facilitam contribuições e sugestões automáticas de IA.

Por que este README ajuda o Copilot a gerar PRs melhores
- Contexto claro, comandos para rodar e critérios de aceitação reduzem ambiguidade.
- Exemplos executáveis (requests, fixtures) ajudam a validar mudanças geradas automaticamente.

Conteúdo recomendado (já incluído neste repositório)
1. Visão Geral
- Stack: PHP (7.4+ / 8.x), Composer, PHPUnit
- Componentes: src/ (lógica), public/ (entry), tests/ (unit), scripts/ (dev tools)

2. Como rodar localmente
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