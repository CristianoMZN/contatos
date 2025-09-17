<?php
include_once "config.php";
$id = $_POST['id'];
$name = $_POST['nome'];
$tel = $_POST['telefone'];
$email = $_POST['email'];

// Atualização usando PDO com prepared statements
$sqlUpdate = "UPDATE cadastros SET nome=:nome, telefone=:telefone, email=:email WHERE id=:id";
try {
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->bindParam(':nome', $name);
    $stmt->bindParam(':telefone', $tel);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Cadastro Atualizado com Sucesso!');
                history.go(-2);
              </script>";
    }
} catch(PDOException $e) {
    echo "Falha ao executar atualização: " . $e->getMessage();
}
?>