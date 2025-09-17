
<?php
    $inputNome = $_POST["campoNome"];
    $inputTelefone = $_POST["campoTelefone"];
    $inputEmail = $_POST["campoEmail"];
    
    if (strlen($inputNome) == 0 or strlen($inputTelefone) == 0 or strlen($inputEmail) == 0){
        echo "<script>
                alert('Preencha todos os campos');
                history.back();
             </script>";
    } else {
        require_once "config.php";
        
        try {
            // Using PDO with prepared statements for security
            $sql = "INSERT INTO cadastros (nome, telefone, email) VALUES (:nome, :telefone, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $inputNome, PDO::PARAM_STR);
            $stmt->bindParam(':telefone', $inputTelefone, PDO::PARAM_STR);
            $stmt->bindParam(':email', $inputEmail, PDO::PARAM_STR);
            
            if($stmt->execute()){
                echo "<script>
                    alert('Dados gravados com sucesso!');
                    history.back();
                 </script>";
            }
        } catch (PDOException $e) {
            echo "Erro, a gravação no banco de dados não pode ser feita. <br>" . $e->getMessage();
        }
    }
?>