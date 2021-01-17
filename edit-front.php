<?php
include_once "header.php";
include_once "config.php";
$id = $_POST['id'];

?>
<title>Editar | Contatos</title>
</head>
<body class="container bg-dark text-light">
<header>
    <div class="container">
    <h1>Editar Contato</h1>
    <br>
    </div>
</header>
<?php
if (!$id == 0){
    $sql = "SELECT * FROM cadastros WHERE id = '$id'";
    $sqlResult = mysqli_query($link, $sql);
    $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
  
?>  

    <div class="container"><form action='edit.php' method='post'>
    <input type='number' class='form-control' id='id' value='<?php echo $row['id']; ?>' name='id' required>
    <br>
    <input placeholder='Nome Completo' type='text' value='<?php echo $row['nome']; ?>' class='form-control' id='nome' name='nome' required>
    <br>
    <input placeholder='Telefone Ex 011 3333-3333' type='tel' value='<?php echo $row['telefone'];; ?>' class='form-control' id='telefone' name='telefone' required>
    <br>
    <input placeholder='email@dominio.com' type='email' value='<?php echo $row['email'];; ?>' class='form-control' id='email' name='email' required>
    <br>
    <button class="btn btn-primary" type="submit">Enviar</button>
    </form>
    <br>
    <form action='del.php' method="post">
        <button class='btn btn-danger' type='submit' name='id' value='<?php echo $row['id']; ?>'>Deletar</button>
    </form>
    </div>
    </div>
    
<?php
mysqli_close($link);

} else {
    echo "<script>
    alert('Não Conseguimos atender sua solicitação');
    history.back();
    </script>";
}