<?php
    include_once "config.php";
    $id = $_POST['id'];
    $sqlDel = "DELETE FROM cadastros WHERE id='$id'";
    if (mysqli_query($link, $sqlDel)){
        mysqli_close($link);
        echo "<script>
        alert('O registro foi removido com sucesso!');
        history.go(-2);
        </script>";
        
    }
