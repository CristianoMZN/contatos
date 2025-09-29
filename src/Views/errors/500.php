<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="display-1 text-danger mb-4">
                ⚠️ 500
            </div>
            <h1 class="h3 mb-3">Erro interno do servidor</h1>
            <p class="text-muted mb-4">
                Ocorreu um erro inesperado. Nossa equipe foi notificada e está trabalhando para resolver o problema.
            </p>
            
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="/" class="btn btn-primary">
                    🏠 Página Inicial
                </a>
                <button onclick="location.reload()" class="btn btn-outline-primary">
                    🔄 Tentar Novamente
                </button>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>