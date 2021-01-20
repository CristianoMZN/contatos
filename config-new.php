<?php
// Definando constantes do banco

define('DB_SERVER','#SERVIDOR#');
define('DB_USER','#USUARIO#');
// deixe em branco de não tiver
define('DB_PASSWORD','#SENHA#');
define('DB_NAME','#BANCODEDADOS#');

// Faz a conexão com o banco

$link = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_NAME);
// Testa a conexão com o banco.
if ($link === false ){
    die(" Erro de conexão com o banco de dados " . mysqli_connect_error());
}
