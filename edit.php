<?php
include_once "config.php";
$id = $_POST['id'];
$name = $_POST['nome'];
$tel = $_POST['telefone'];
$email = $_POST['email'];
$sqlUpdate = "UPDATE cadastros SET nome='$name', telefone='$tel', email='$email' WHERE id='$id'";
if (mysqli_query($link, $sqlUpdate)){
    mysqli_close($link);
    echo "<script>
            alert('Cadastro Atualizado com Sucesso!');
            history.go(-2);
          </script>";
          
} else {
    mysqli_close($link);
    echo "Falha ao executar atualização";
} 
?>