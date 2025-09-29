<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>
                <?php if ($viewType === 'my-contacts'): ?>
                    📱 Meus Contatos
                <?php else: ?>
                    🌍 Agenda Pública
                <?php endif; ?>
            </h1>
            <p class="text-muted">
                <?php if ($viewType === 'my-contacts'): ?>
                    Gerencie seus contatos pessoais e comerciais
                <?php else: ?>
                    Encontre empresas e serviços públicos
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <?php if ($viewType === 'my-contacts'): ?>
                <a href="/contacts/create" class="btn btn-primary">
                    ➕ Novo Contato
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="get" action="" class="d-flex">
                <input type="search" name="search" class="form-control me-2" 
                       placeholder="Buscar contatos..." 
                       value="<?= htmlspecialchars($search ?? '') ?>"
                       onkeyup="handleSearch(this)">
                <button type="submit" class="btn btn-outline-primary">
                    🔍 Buscar
                </button>
            </form>
        </div>
        
        <?php if ($viewType === 'public-contacts' && !empty($categories)): ?>
        <div class="col-md-4">
            <select name="category" class="form-select" onchange="filterByCategory(this.value)">
                <option value="">Todas as categorias</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" 
                            <?= ($selectedCategory == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistics -->
    <?php if (!empty($contacts['total'])): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                📊 Mostrando <?= $contacts['from'] ?? 1 ?> - <?= $contacts['to'] ?? count($contacts['data']) ?> 
                de <?= $contacts['total'] ?> contatos
                <?php if ($search): ?>
                    para "<strong><?= htmlspecialchars($search) ?></strong>"
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Contacts Grid -->
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
                                    <?php if ($contact['category_icon']): ?>
                                        <i class="<?= htmlspecialchars($contact['category_icon']) ?>"></i>
                                    <?php endif; ?>
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
                                    <?= htmlspecialchars(substr($contact['description'], 0, 100)) ?>
                                    <?= strlen($contact['description']) > 100 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    📅 <?= date('d/m/Y', strtotime($contact['created_at'])) ?>
                                </span>
                                <?php if ($viewType === 'public-contacts' && $contact['owner_name']): ?>
                                    <span>
                                        👤 <?= htmlspecialchars($contact['owner_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/contato/<?= htmlspecialchars($contact['slug']) ?>" 
                                   class="btn btn-primary btn-sm">
                                    👁️ Ver Detalhes
                                </a>
                                
                                <?php if ($viewType === 'my-contacts'): ?>
                                    <div class="btn-group" role="group">
                                        <a href="/contacts/<?= htmlspecialchars($contact['slug']) ?>/edit" 
                                           class="btn btn-outline-secondary btn-sm">
                                            ✏️
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="display-1 text-muted mb-3">
                        <?= $search ? '🔍' : ($viewType === 'my-contacts' ? '📱' : '🌍') ?>
                    </div>
                    <h3 class="text-muted">
                        <?php if ($search): ?>
                            Nenhum contato encontrado
                        <?php elseif ($viewType === 'my-contacts'): ?>
                            Você ainda não tem contatos
                        <?php else: ?>
                            Nenhum contato público encontrado
                        <?php endif; ?>
                    </h3>
                    <p class="text-muted">
                        <?php if ($search): ?>
                            Tente buscar com outros termos
                        <?php elseif ($viewType === 'my-contacts'): ?>
                            Comece criando seu primeiro contato
                        <?php else: ?>
                            Em breve teremos mais empresas disponíveis
                        <?php endif; ?>
                    </p>
                    
                    <?php if ($viewType === 'my-contacts'): ?>
                        <a href="/contacts/create" class="btn btn-primary">
                            ➕ Criar Primeiro Contato
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Loading Spinner for Infinite Scroll -->
    <div class="text-center py-4 loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="text-muted mt-2">Carregando mais contatos...</p>
    </div>

    <!-- Pagination (fallback if JS disabled) -->
    <?php if (!empty($contacts['last_page']) && $contacts['last_page'] > 1): ?>
    <nav aria-label="Navegação dos contatos" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=1<?= $search ? '&search=' . urlencode($search) : '' ?>">
                    ⏮️ Primeira
                </a>
            </li>
            
            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= max(1, $currentPage - 1) ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                    ⬅️ Anterior
                </a>
            </li>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($contacts['last_page'], $currentPage + 2); $i++): ?>
                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?= $currentPage >= $contacts['last_page'] ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= min($contacts['last_page'], $currentPage + 1) ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                    Próxima ➡️
                </a>
            </li>
            
            <li class="page-item <?= $currentPage >= $contacts['last_page'] ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $contacts['last_page'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                    Última ⏭️
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script>
function filterByCategory(categoryId) {
    const url = new URL(window.location);
    if (categoryId) {
        url.searchParams.set('category', categoryId);
    } else {
        url.searchParams.delete('category');
    }
    url.searchParams.delete('page'); // Reset pagination
    window.location = url.toString();
}

// Set initial page for infinite scroll
currentPage = <?= $currentPage ?>;
hasMorePages = <?= json_encode($contacts['current_page'] < $contacts['last_page']) ?>;
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>