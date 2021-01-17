
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
        $sql = "INSERT INTO cadastros (nome, telefone, email) VALUES ('$inputNome', '$inputTelefone', '$inputEmail')";
        if(mysqli_query($link, $sql)){
            mysqli_close($link);
            echo "<script>
                alert('Dados gravados com sucesso!');
                history.back();
             </script>";
        } else {
            echo "Erro, a gravação no banco de dados nao pode ser feita. <br>" . mysqli_error($link);
        }             mysqli_close($link);

    }

    // Definindo arquivo PHP



?>

