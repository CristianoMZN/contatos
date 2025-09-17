# Agenda de Contatos em PHP

Aplicação de agenda de contatos desenvolvida em PHP com Bootstrap 5 e banco de dados MySQL.

## Funcionalidades

- ✅ **CRUD Completo**: Criar, listar, editar e excluir contatos
- ✅ **Modo Escuro/Claro**: Alternador de tema com persistência via localStorage
- ✅ **Máscara de Telefone**: Formatação automática para números brasileiros
- ✅ **Paginação**: Listagem com navegação por páginas
- ✅ **Busca**: Sistema de busca por nome
- ✅ **Bootstrap 5.3.3**: Interface moderna e responsiva
- ✅ **PDO**: Conexão segura com prepared statements

## Instalação 

### Restauração do MYSQL

- Instale o MYSQL 

- Restaure o backup do banco de dados a partir do arquivo .sql que está do diretório SQL usando o DBeaver, phpmyadmin, HeidiSQL ou algum outro Data base Tool. 
 
### Edição do 'config.php'

- Renomeie o arquivo *config-new.php* para *config.php* 

- Altere os valores fictícios que estão entre cerquilhas "#" do arquivo *config.php* por valores reais. 

- Obs. As cerquilhas devem ser removidas junto com o atributo fictício como o código exemplo a baixo mostra:

```php
    define('DB_SERVER','#HOSTNAME#');
    define('DB_USER','#USUARIO#'); 
    define('DB_PASSWORD','#SENHA#');
    define('DB_NAME','#BANCO#');
```
 
- Exemplo de uma configuração real:

```php
    define('DB_SERVER','localhost');
    define('DB_USER','root');
    define('DB_PASSWORD','123456789');
    define('DB_NAME','contatos');
```

## Principais Melhorias Implementadas

### Atualização Técnica
- **Bootstrap**: Atualizado de v5.0.0-beta1 para v5.3.3 (estável)
- **PDO**: Migração completa de MySQLi para PDO com prepared statements
- **Segurança**: Implementação de htmlspecialchars() para prevenir XSS

### Funcionalidades Visuais
- **Modo Escuro**: Switcher de tema no canto superior direito
- **Máscaras de Input**: Formatação automática de telefones brasileiros (xx) xxxxx-xxxx
- **Interface Responsiva**: Melhor compatibilidade com dispositivos móveis

### Melhorias de UX
- **Persistência de Tema**: O modo escolhido é salvo no navegador
- **Validação**: Melhor tratamento de erros e validação de dados
- **Performance**: Queries otimizadas com paginação
