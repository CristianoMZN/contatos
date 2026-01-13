# Como Ativar o GitHub Pages

Este guia explica como configurar o GitHub Pages para hospedar a documentação do projeto.

## 📋 Pré-requisitos

- Ter permissões de administrador no repositório
- Ter a pasta `/docs` com a documentação pronta (✅ já criado)

## 🚀 Passo a Passo

### 1. Acesse as Configurações do Repositório

1. Vá para o repositório: https://github.com/CristianoMZN/contatos
2. Clique em **Settings** (Configurações)

### 2. Configure o GitHub Pages

1. No menu lateral esquerdo, procure por **Pages** (na seção "Code and automation")
2. Clique em **Pages**

### 3. Configure a Source (Origem)

Na seção **Build and deployment**:

1. **Source**: Selecione `Deploy from a branch`
2. **Branch**: 
   - Selecione `main` (ou `master`, dependendo do seu branch principal)
   - Pasta: Selecione `/docs`
3. Clique em **Save**

### 4. Aguarde o Deploy

1. O GitHub irá processar a documentação (leva 1-2 minutos)
2. Uma mensagem aparecerá no topo dizendo: 
   ```
   Your site is live at https://cristianomzn.github.io/contatos/
   ```

### 5. Verifique a Publicação

1. Acesse: https://cristianomzn.github.io/contatos/
2. A página principal da documentação deve aparecer

## ⚙️ Configurações Opcionais

### Domínio Customizado (Opcional)

Se você tem um domínio próprio:

1. Em **Pages** > **Custom domain**
2. Digite seu domínio (ex: `docs.meusite.com`)
3. Clique em **Save**
4. Configure os registros DNS no seu provedor:
   ```
   Type: CNAME
   Name: docs
   Value: cristianomzn.github.io
   ```

### HTTPS

- O HTTPS é ativado automaticamente
- Marque a opção **Enforce HTTPS** para forçar HTTPS

## 🔄 Atualizações Automáticas

Sempre que você fizer push de alterações na pasta `/docs` do branch principal:

1. O GitHub automaticamente reconstrói o site
2. As mudanças ficam visíveis em 1-2 minutos

## 🐛 Solução de Problemas

### Site não carrega

1. Verifique se a branch e pasta estão corretas em Settings > Pages
2. Aguarde alguns minutos após salvar
3. Limpe o cache do navegador (Ctrl+Shift+R)

### Página 404

1. Certifique-se que `index.html` existe em `/docs`
2. Verifique se o arquivo `.nojekyll` está presente (para evitar processamento Jekyll)

### Estilos não carregam

1. Verifique se os caminhos das URLs são relativos (não absolutos)
2. Exemplo correto: `assets/css/style.css`
3. Exemplo errado: `/assets/css/style.css` (com barra inicial)

## 📊 Análise de Tráfego

Para acompanhar acessos:

1. Settings > Pages > **Traffic**
2. Veja estatísticas de visitantes e páginas mais acessadas

## 🎉 Pronto!

Sua documentação está agora online e acessível publicamente em:

**https://cristianomzn.github.io/contatos/**

Compartilhe o link no README.md do projeto!

---

## 📝 Atualizando o README Principal

Adicione um badge e link no README.md principal:

```markdown
# Contatos

[![Documentation](https://img.shields.io/badge/docs-GitHub%20Pages-blue)](https://cristianomzn.github.io/contatos/)

Sistema moderno de gerenciamento de contatos...

## 📚 Documentação

Documentação completa disponível em: [https://cristianomzn.github.io/contatos/](https://cristianomzn.github.io/contatos/)
```

---

**Última atualização:** 2025-01-13
**Autor:** GitHub Copilot
