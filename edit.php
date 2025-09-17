<?php
include_once "config.php";
$id = $_POST['id'];
$name = $_POST['nome'];
$tel = $_POST['telefone'];
$email = $_POST['email'];

try {
    // Using PDO with prepared statements for security
    $sqlUpdate = "UPDATE cadastros SET nome=:nome, telefone=:tel, email=:email WHERE id=:id";
    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->bindParam(':nome', $name, PDO::PARAM_STR);
    $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()){
        echo "<script>
                alert('Cadastro Atualizado com Sucesso!');
                history.go(-2);
              </script>";
    }
} catch (PDOException $e) {
    echo "Falha ao executar atualização: " . $e->getMessage();
} 
?>