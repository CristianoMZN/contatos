<?php
    include_once "config.php";
    $id = $_POST['id'];
    
    // Exclusão usando PDO com prepared statements
    $sqlDel = "DELETE FROM cadastros WHERE id=:id";
    try {
        $stmt = $pdo->prepare($sqlDel);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo "<script>
            alert('O registro foi removido com sucesso!');
            history.go(-2);
            </script>";
        }
    } catch(PDOException $e) {
        echo "Erro ao deletar registro: " . $e->getMessage();
    }
?>
