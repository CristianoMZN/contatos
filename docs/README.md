# Documentação do Sistema Contatos

Esta é a documentação oficial do projeto Contatos, hospedada via GitHub Pages.

## 🌐 Acesse Online

A documentação está disponível em: [https://cristianomzn.github.io/contatos/](https://cristianomzn.github.io/contatos/)

## 📄 Páginas Disponíveis

- **[index.html](index.html)** - Página inicial com visão geral do projeto
- **[installation.html](installation.html)** - Guia completo de instalação
- **[architecture.html](architecture.html)** - Documentação da arquitetura MVC
- **[api.html](api.html)** - Referência completa da API
- **[contributing.html](contributing.html)** - Guia para contribuidores

## 🛠️ Tecnologias Utilizadas

- **Bootstrap 5.3.3** - Framework CSS
- **Bootstrap Icons** - Ícones
- **CSS Customizado** - Estilos adicionais
- **JavaScript Vanilla** - Interatividade

## 📁 Estrutura

```
docs/
├── index.html              # Página inicial
├── installation.html       # Guia de instalação
├── architecture.html       # Arquitetura do sistema
├── api.html               # Documentação da API
├── contributing.html      # Guia de contribuição
├── README.md             # Este arquivo
└── assets/
    ├── css/
    │   └── style.css     # Estilos customizados
    ├── js/
    │   └── main.js       # JavaScript customizado
    └── img/
        └── (imagens)
```

## 🚀 Desenvolvimento Local

Para visualizar a documentação localmente:

### Opção 1: Python SimpleHTTPServer

```bash
cd docs
python3 -m http.server 8000
# Acesse http://localhost:8000
```

### Opção 2: PHP Built-in Server

```bash
cd docs
php -S localhost:8000
# Acesse http://localhost:8000
```

### Opção 3: Node.js http-server

```bash
npm install -g http-server
cd docs
http-server -p 8000
# Acesse http://localhost:8000
```

## ✏️ Como Contribuir

1. Edite os arquivos HTML conforme necessário
2. Mantenha o padrão visual e estrutural
3. Teste localmente antes de fazer commit
4. Siga as convenções de nomenclatura
5. Adicione screenshots na pasta `assets/img/` quando relevante

## 🎨 Customização

### Cores

As cores principais podem ser alteradas no arquivo `assets/css/style.css`:

```css
:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --info-color: #0dcaf0;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
}
```

### JavaScript

Funcionalidades interativas estão em `assets/js/main.js`:

- Smooth scroll
- Copy code to clipboard
- Active navigation
- Back to top button
- Search functionality

## 📝 Licença

A documentação segue a mesma licença do projeto principal (MIT).

## 🤝 Contribuidores

Contribuições são bem-vindas! Veja [contributing.html](contributing.html) para mais detalhes.

---

**Última atualização:** 2025-01-13
**Versão:** 1.0.0
