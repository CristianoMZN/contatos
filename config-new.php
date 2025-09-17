<?php
// Definando constantes do banco

define('DB_SERVER','#SERVIDOR#');
define('DB_USER','#USUARIO#');
// deixe em branco de não tiver
define('DB_PASSWORD','#SENHA#');
define('DB_NAME','#BANCODEDADOS#');

// Conexão PDO (nova implementação segura)
try {
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch(PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}
?>
