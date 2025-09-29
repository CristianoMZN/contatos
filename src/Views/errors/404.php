<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="display-1 text-muted mb-4">
                🔍 404
            </div>
            <h1 class="h3 mb-3">Página não encontrada</h1>
            <p class="text-muted mb-4">
                A página que você está procurando não existe ou foi removida.
            </p>
            
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="/" class="btn btn-primary">
                    🏠 Página Inicial
                </a>
                <a href="/contatos" class="btn btn-outline-primary">
                    📱 Ver Contatos
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary">
                    ← Voltar
                </button>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>