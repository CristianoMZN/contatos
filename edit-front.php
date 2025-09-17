<?php
include_once "header.php";
include_once "config.php";
$id = $_POST['id'];

?>
<title>Editar | Contatos</title>
</head>
<body class="container">
    <!-- Theme Toggle Button -->
    <div class="theme-toggle-container">
        <button type="button" class="btn btn-outline-secondary btn-sm" id="theme-toggle">
            🌙 <span id="theme-text">Modo Escuro</span>
        </button>
    </div>
<header>
    <div class="container">
    <h1>Editar Contato</h1>
    <br>
    </div>
</header>
<?php
if (!$id == 0){
    // Consulta usando PDO com prepared statements
    $sql = "SELECT * FROM cadastros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
  
?>  

    <div class="container"><form action='edit.php' method='post'>
    <input type='number' class='form-control' id='id' value='<?php echo $row['id']; ?>' name='id' required>
    <br>
    <input placeholder='Nome Completo' type='text' value='<?php echo $row['nome']; ?>' class='form-control' id='nome' name='nome' required>
    <br>
    <input placeholder='Telefone Ex: (11) 99999-9999' type='tel' value='<?php echo $row['telefone'];; ?>' class='form-control' id='telefone' name='telefone' required>
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
// Conexão PDO será fechada automaticamente
include_once "footer.php";

} else {
    echo "<script>
    alert('Não Conseguimos atender sua solicitação');
    history.back();
    </script>";
}
?>