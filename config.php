<?php
// Definando constantes do banco

define('DB_SERVER','#HOSTNAME#');
define('DB_USER','#USUARIO#');
define('DB_PASSWORD','#SENHA#');
define('DB_NAME','#BANCO#');

// Faz a conexão com o banco

$link = mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_NAME);
// Testa a conexão com o banco.
if ($link === false ){
    die(" Erro de conexão com o banco de dados " . mysqli_connect_error());
}