# Agenda de Contatos - PHP

Sistema de gerenciamento de contatos desenvolvido em PHP puro com interface Bootstrap.

## ✨ Novidades da Versão Atual

### **Atualizações Visuais e Técnicas (2024)**
- ✅ **Bootstrap 5.3.3**: Interface atualizada com a versão mais recente
- ✅ **Modo Escuro/Claro**: Alternância completa de temas com persistência
- ✅ **Migração para PDO**: Banco de dados mais seguro com prepared statements
- ✅ **Máscaras de Input**: Formatação automática para campos de telefone
- ✅ **Segurança Aprimorada**: XSS protection com htmlspecialchars

### **Recursos**
- 📱 Interface responsiva e moderna
- 🌙 Modo escuro/claro com botão switcher fixo
- 🔒 Segurança avançada com PDO e prepared statements
- 📞 Máscara automática para números de telefone: (11) 99999-9999
- 🔍 Sistema de busca por nome
- 📄 Paginação de resultados
- ✏️ CRUD completo (Criar, Ler, Atualizar, Deletar)

## Instalação 

### Restauração do MySQL

- Instale o MySQL 
- Restaure o backup do banco de dados a partir do arquivo .sql que está no diretório SQL usando o DBeaver, phpmyadmin, HeidiSQL ou algum outro Database Tool.

### Configuração do Banco de Dados

- Renomeie o arquivo *config-new.php* para *config.php* 
- Altere os valores fictícios que estão entre cerquilhas "#" do arquivo *config.php* por valores reais.

**Exemplo de configuração:**

```php
define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASSWORD','123456789');
define('DB_NAME','contatos');
```

## Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Frontend**: Bootstrap 5.3.3, JavaScript
- **Banco de Dados**: MySQL com PDO
- **Segurança**: Prepared Statements, XSS Protection

## Funcionalidades

### Interface do Usuário
- Tabela responsiva com listagem de contatos
- Modal para cadastro de novos contatos
- Formulário de edição com opção de exclusão
- Sistema de busca em tempo real
- Paginação automática (5 registros por página)

### Modo Escuro/Claro
- Botão switcher posicionado no canto superior direito
- Persistência da escolha no localStorage
- Transição suave entre temas
- Compatibilidade completa com todos os componentes Bootstrap

### Validações e Máscaras
- Máscara automática para telefone: `(11) 99999-9999`
- Validação de campos obrigatórios
- Filtro para aceitar apenas números e símbolos válidos em telefones

### Segurança
- Prepared statements em todas as operações SQL
- Proteção contra SQL Injection
- Proteção contra XSS com htmlspecialchars
- Tratamento de erros com try/catch

## Melhorias Implementadas

1. **Atualização do Bootstrap**: Migração da versão 5.0.0-beta1 para 5.3.3
2. **Implementação do Modo Escuro**: Sistema completo de alternância de temas
3. **Migração MySQLi → PDO**: Substituição completa por tecnologia mais segura
4. **Máscaras de Input**: Formatação automática para campos de telefone
5. **Melhorias de UX**: Interface mais limpa e intuitiva
6. **Documentação**: README atualizado com todas as funcionalidades
