<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">
                    📱 Sistema de Contatos
                </h1>
                <p class="lead mb-4">
                    Gerencie seus contatos de forma moderna, segura e organizada. 
                    Acesso a agenda pública de empresas e criação de contatos privados.
                </p>
                
                <div class="d-flex gap-3 flex-wrap">
                    <?php if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']): ?>
                        <a href="/register" class="btn btn-light btn-lg">
                            🚀 Começar Agora
                        </a>
                        <a href="/login" class="btn btn-outline-light btn-lg">
                            🔐 Entrar
                        </a>
                    <?php else: ?>
                        <a href="/dashboard" class="btn btn-light btn-lg">
                            📱 Meus Contatos
                        </a>
                        <a href="/contacts/create" class="btn btn-outline-light btn-lg">
                            ➕ Novo Contato
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4">
                            <div class="fs-1 mb-2">🔒</div>
                            <h5>Seguro</h5>
                            <small>Dados protegidos com criptografia</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4">
                            <div class="fs-1 mb-2">📱</div>
                            <h5>Responsivo</h5>
                            <small>Funciona em qualquer dispositivo</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4">
                            <div class="fs-1 mb-2">⚡</div>
                            <h5>Rápido</h5>
                            <small>Interface moderna e intuitiva</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-4">
                            <div class="fs-1 mb-2">🌍</div>
                            <h5>Público</h5>
                            <small>Empresas visíveis para todos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center mb-5">
            <h2>✨ Funcionalidades Avançadas</h2>
            <p class="text-muted">
                Um sistema completo para gerenciar todos os seus contatos
            </p>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-primary fs-1 mb-3">👤</div>
                    <h5>Contatos Pessoais</h5>
                    <p class="text-muted">
                        Mantenha seus contatos pessoais privados e organizados
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-primary fs-1 mb-3">🏢</div>
                    <h5>Empresas Públicas</h5>
                    <p class="text-muted">
                        Cadastre empresas e torne-as visíveis para todos os usuários
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-primary fs-1 mb-3">📞</div>
                    <h5>Múltiplos Contatos</h5>
                    <p class="text-muted">
                        Adicione vários telefones e emails por contato
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-primary fs-1 mb-3">🖼️</div>
                    <h5>Galeria de Imagens</h5>
                    <p class="text-muted">
                        Adicione fotos e organize uma galeria para cada contato
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-primary fs-1 mb-3">🔐</div>
                    <h5>Autenticação 2FA</h5>
                    <p class="text-muted">
                        Segurança extra com autenticação de dois fatores
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 text-center border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-primary fs-1 mb-3">🔍</div>
                    <h5>Busca Avançada</h5>
                    <p class="text-muted">
                        Encontre contatos rapidamente com filtros e categorias
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2>🏷️ Categorias de Empresas</h2>
                <p class="text-muted">
                    Encontre empresas organizadas por categoria
                </p>
            </div>
        </div>
        
        <div class="row g-3">
            <?php foreach (array_slice($categories, 0, 12) as $category): ?>
                <div class="col-md-3 col-6">
                    <a href="/contatos?category=<?= $category['id'] ?>" 
                       class="card text-decoration-none border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <?php if ($category['icon']): ?>
                                <div class="text-primary fs-4 mb-2">
                                    <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                </div>
                            <?php endif; ?>
                            <h6 class="card-title mb-1">
                                <?= htmlspecialchars($category['name']) ?>
                            </h6>
                            <small class="text-muted">
                                <?= $category['contact_count'] ?> empresa(s)
                            </small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="/contatos" class="btn btn-primary">
                Ver Todas as Empresas →
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Contacts Section -->
<?php if (!empty($recentContacts['data'])): ?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center mb-5">
            <h2>🆕 Empresas Recentes</h2>
            <p class="text-muted">
                Últimas empresas adicionadas à agenda pública
            </p>
        </div>
    </div>
    
    <div class="row g-4">
        <?php foreach (array_slice($recentContacts['data'], 0, 4) as $contact): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card contact-card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <?php if ($contact['main_image']): ?>
                            <img src="/uploads/contacts/<?= htmlspecialchars($contact['main_image']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($contact['name']) ?>">
                        <?php else: ?>
                            <div class="card-img-top d-flex align-items-center justify-content-center text-white fs-1">
                                🏢
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($contact['category_name']): ?>
                            <span class="badge bg-primary contact-badge">
                                <?= htmlspecialchars($contact['category_name']) ?>
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
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <a href="/contato/<?= htmlspecialchars($contact['slug']) ?>" 
                           class="btn btn-outline-primary btn-sm w-100">
                            Ver Detalhes →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="/contatos" class="btn btn-primary">
            Ver Todas as Empresas →
        </a>
    </div>
</div>
<?php endif; ?>

<!-- CTA Section -->
<?php if (!isset($_SESSION['user_authenticated']) || !$_SESSION['user_authenticated']): ?>
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2>🚀 Pronto para começar?</h2>
        <p class="lead mb-4">
            Crie sua conta gratuita e comece a organizar seus contatos hoje mesmo
        </p>
        
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="/register" class="btn btn-light btn-lg">
                📝 Criar Conta Grátis
            </a>
            <a href="/contatos" class="btn btn-outline-light btn-lg">
                👀 Ver Agenda Pública
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>