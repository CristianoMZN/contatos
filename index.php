
<?php
include_once "config.php";
include_once "header.php";
?>
<title>Contatos</title>
</header>

<body class='bg-dark text-light'>
<header>
    <h1 class='h-25 text-center'>Contatos</h1>
    <!-- Cadastro Novo Usuário Modal-->
    <div class='modal fade' id="novo-cadastro-modal" tabindex="-1" aria-labelledby="novo-cadastro-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novo-cadastro-modal-label">Cadastrar Novo Contato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <form action="create.php" method="post">
                        <input placeholder="Nome Completo" type="text" class="form-control" id="campoNome" name="campoNome" required>
                        <br>
                        <input placeholder="Telefone Ex 011 3333-3333" type="tel" class="form-control" id="campoTelefone" name="campoTelefone" required>
                        <br>
                        <input placeholder="Email" type="email" class="form-control" id="campoEmail" name="campoEmail" required>

                </div>
                <div class="modal-footer">
                    <button  type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</header>

<?php
// input do paginacao
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
// input do mecanismo de busca
if (isset($_GET['search'])) {
    $busca = $_GET['search'];
} else {
    $busca = "";
}
// numero de registros por pagina
$no_of_records_per_page = 5;
$offset = ($page-1) * $no_of_records_per_page;
$total_pages_sql = "SELECT COUNT(*) FROM cadastros";
$result = mysqli_query($link,$total_pages_sql);
$total_rows = mysqli_fetch_array($result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);
$sql = "SELECT * FROM cadastros WHERE nome LIKE '%$busca%' LIMIT $offset, $no_of_records_per_page";
$resultSql = mysqli_query($link, $sql);
?>

<div class="container">
    <!-- Formulário de Pesquisa -->
    <div class="d-flex justify-content-end bd-highlight input-group">
        <form class='' action="index.php" method="get">
            <input class='justify-content-end' name="search" type="text" class="form-control" aria-label="" aria-describedby="button-busca">
            <button class="btn btn-outline-primary justify-content-start" type="submit" id="button-busca"><?php if ($busca == ""){ echo "Buscar";} else { echo "Listagem Completa";} ?></button>
        </form>
</div>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novo-cadastro-modal">
        Novo Contato
    </button>


    <!-- Cabeçalho da Tabela -->
    <table class="table table-dark table-hover" style="margin-top: 20px;">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">Telefone</th>
            <th scope="col">email</th>
            <th scope="col">Editar</th>
        </tr>

<?php
while ($row = mysqli_fetch_assoc($resultSql)){

    echo  
        "<tr><td>" . $row["id"] . 
        "</td><td>" . $row["nome"] . 
        "</td><td>" . $row["telefone"] . 
        "</td><td>" . $row["email"] . 
        "</td><td>" . "<form action='edit-front.php' method='post'><button type='submit' class='btn btn-danger' name='id' value='" .  $row["id"] . "'>Editar</button>" . "</a></td></tr>";
}



?>
       
    </table>
    <!-- Menu de paginação -->
    <div class='position-relative '>
    <ul class='pagination justify-content-center'>
        <li class='page-item <?php if($page <= 1){ echo 'disabled'; } ?>'>
            <a class='page-link' href='<?php if($page > 1){ echo '?page=1'; } ?>'> << Primeira </a>
        </li>
        <li class='page-item <?php if($page <= 1){ echo 'disabled'; } ?>'>
            <a class='page-link' href="<?php if($page <= 1){ echo '#'; } else { echo "?page=".($page - 1); } ?>">< Anterior</a>
        </li>
        <li class='page-item <?php if($page >= $total_pages){ echo 'disabled'; } ?>'>
        <a class='page-link' href="<?php if($page >= $total_pages){ echo '#'; } else { echo "?page=".($page + 1); } ?>">Proxima ></a>
    </li>
    <li class='page-item <?php if($page >= $total_pages){ echo 'disabled'; } ?>'>
        <a class='page-link' href='?page=<?php echo $total_pages; ?>'> Última >></a></li>
    </ul>
    </div>
    </div>
<?php

mysqli_close($link);
include_once "footer.php";

