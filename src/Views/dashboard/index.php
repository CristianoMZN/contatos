<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<div class="container mt-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>👋 Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>!</h1>
            <p class="text-muted">Gerencie seus contatos de forma fácil e organizada</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/contacts/create" class="btn btn-primary">
                ➕ Novo Contato
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <div class="fs-2 mb-2">📱</div>
                    <h3 class="card-title"><?= $stats['total'] ?></h3>
                    <p class="card-text">Total de Contatos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <div class="fs-2 mb-2">👤</div>
                    <h3 class="card-title"><?= $stats['personal'] ?></h3>
                    <p class="card-text">Pessoais</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <div class="fs-2 mb-2">🏢</div>
                    <h3 class="card-title"><?= $stats['business'] ?></h3>
                    <p class="card-text">Empresas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body">
                    <div class="fs-2 mb-2">🌍</div>
                    <h3 class="card-title"><?= $stats['public'] ?></h3>
                    <p class="card-text">Públicos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">⚡ Ações Rápidas</h5>
                    <div class="btn-group" role="group">
                        <a href="/contacts/create" class="btn btn-outline-primary">
                            ➕ Novo Contato
                        </a>
                        <a href="/contatos" class="btn btn-outline-info">
                            🌍 Agenda Pública
                        </a>
                        <a href="/profile" class="btn btn-outline-secondary">
                            👤 Meu Perfil
                        </a>
                        <a href="/settings" class="btn btn-outline-warning">
                            ⚙️ Configurações
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="get" action="/dashboard" class="d-flex">
                <input type="search" name="search" class="form-control me-2" 
                       placeholder="Buscar nos meus contatos..." 
                       value="<?= htmlspecialchars($search ?? '') ?>"
                       onkeyup="handleSearch(this)">
                <button type="submit" class="btn btn-outline-primary">
                    🔍 Buscar
                </button>
            </form>
        </div>
        <div class="col-md-4">
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="filter" id="all" checked>
                <label class="btn btn-outline-secondary" for="all">Todos</label>
                
                <input type="radio" class="btn-check" name="filter" id="personal">
                <label class="btn btn-outline-secondary" for="personal">Pessoais</label>
                
                <input type="radio" class="btn-check" name="filter" id="business">
                <label class="btn btn-outline-secondary" for="business">Empresas</label>
            </div>
        </div>
    </div>

    <!-- My Contacts Grid -->
    <div class="row" id="contacts-container">
        <?php if (!empty($contacts['data'])): ?>
            <?php foreach ($contacts['data'] as $contact): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card contact-card h-100">
                        <div class="position-relative">
                            <?php if ($contact['main_image']): ?>
                                <img src="/uploads/contacts/<?= htmlspecialchars($contact['main_image']) ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($contact['name']) ?>">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center text-white fs-1">
                                    <?= $contact['type'] === 'company' ? '🏢' : '👤' ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($contact['category_name']): ?>
                                <span class="badge bg-primary contact-badge">
                                    <?= htmlspecialchars($contact['category_name']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($contact['is_public']): ?>
                                <span class="badge bg-success position-absolute" style="top: 10px; right: 10px;">
                                    🌍 Público
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($contact['name']) ?></h5>
                            
                            <?php if ($contact['description']): ?>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(substr($contact['description'], 0, 80)) ?>
                                    <?= strlen($contact['description']) > 80 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    📅 <?= date('d/m/Y', strtotime($contact['created_at'])) ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <?= $contact['type'] === 'company' ? '🏢 Empresa' : '👤 Pessoal' ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/contato/<?= htmlspecialchars($contact['slug']) ?>" 
                                   class="btn btn-primary btn-sm">
                                    👁️ Ver
                                </a>
                                
                                <div class="btn-group" role="group">
                                    <a href="/contacts/<?= htmlspecialchars($contact['slug']) ?>/edit" 
                                       class="btn btn-outline-secondary btn-sm" title="Editar">
                                        ✏️
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="confirmDelete('<?= htmlspecialchars($contact['slug']) ?>', '<?= htmlspecialchars($contact['name']) ?>')"
                                            title="Excluir">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="display-1 text-muted mb-3">
                        <?= $search ? '🔍' : '📱' ?>
                    </div>
                    <h3 class="text-muted">
                        <?= $search ? 'Nenhum contato encontrado' : 'Você ainda não tem contatos' ?>
                    </h3>
                    <p class="text-muted mb-4">
                        <?= $search ? 'Tente buscar com outros termos' : 'Comece criando seu primeiro contato' ?>
                    </p>
                    
                    <a href="/contacts/create" class="btn btn-primary">
                        ➕ Criar Primeiro Contato
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Loading Spinner -->
    <div class="text-center py-4 loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="text-muted mt-2">Carregando mais contatos...</p>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🗑️ Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o contato <strong id="contactName"></strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="post" class="d-inline">
                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="btn btn-danger">Excluir Contato</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(slug, name) {
    document.getElementById('contactName').textContent = name;
    document.getElementById('deleteForm').action = `/contacts/${slug}/delete`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Set initial page for infinite scroll
currentPage = <?= $currentPage ?>;
hasMorePages = <?= json_encode($contacts['current_page'] < $contacts['last_page']) ?>;

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterRadios = document.querySelectorAll('input[name="filter"]');
    filterRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            filterContacts(this.id);
        });
    });
});

function filterContacts(type) {
    const cards = document.querySelectorAll('.contact-card');
    cards.forEach(card => {
        const cardType = card.querySelector('.badge.bg-light')?.textContent;
        const isPersonal = cardType?.includes('Pessoal');
        const isBusiness = cardType?.includes('Empresa');
        
        let show = false;
        switch(type) {
            case 'all':
                show = true;
                break;
            case 'personal':
                show = isPersonal;
                break;
            case 'business':
                show = isBusiness;
                break;
        }
        
        card.closest('.col-md-4').style.display = show ? 'block' : 'none';
    });
}
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>