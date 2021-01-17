Instalacao
================================
Restauração do MYSQL
---------------------
- Restaure o backup do banco de dados apartir do arquivo .sql que está do diretório SQL.
- Configurar o arquivo conf.php
- Substitua os valores entre cerquilha pelos falores reais:

.. code-block::

define('DB_SERVER','#HOSTNAME#');
define('DB_USER','#USUARIO#');
define('DB_PASSWORD','#SENHA#');
define('DB_NAME','#BANCO#');


*Exempo de uma configuração real:*

.. code-block::

define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASSWORD','123456789');
define('DB_NAME','contatos');
