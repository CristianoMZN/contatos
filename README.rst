================================
Instalação 
================================ 

Restauração do MYSQL
------------------------

- Instale o MYSQL 

- Restaure o backup do banco de dados a partir do arquivo .sql que está do diretório SQL usando o DBeaver, phpmyadmin, HeidiSQL ou algum outro Data base Tool. 
 

Edição do 'config.php'
------------------------

 
 

- Altere os valores fictícios que estão entre cerquilhas "#" do arquivo *config.php* por valores reais. 

- Obs. As cerquilhas devem ser removidas junto com o atributo fictício como o código exemplo a baixo mostra:

.. code-block:: 

    define('DB_SERVER','#HOSTNAME#');
    define('DB_USER','#USUARIO#'); 
    define('DB_PASSWORD','#SENHA#');
    define('DB_NAME','#BANCO#');
 
 
- Exemplo de uma configuração real:

.. code-block:: 

    define('DB_SERVER','localhost');
    define('DB_USER','root');
    define('DB_PASSWORD','123456789');
    define('DB_NAME','contatos');