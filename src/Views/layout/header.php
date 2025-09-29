<!doctype html>
<html lang="pt-br" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title><?= $title ?? 'Contatos' ?></title>
    
    <?php if (isset($seoData)): ?>
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= htmlspecialchars($seoData['description']) ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($seoData['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seoData['description']) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($seoData['image']) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($seoData['url']) ?>">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($seoData['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($seoData['description']) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($seoData['image']) ?>">
    
    <!-- JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= htmlspecialchars($seoData['title']) ?>",
        "description": "<?= htmlspecialchars($seoData['description']) ?>",
        "image": "<?= htmlspecialchars($seoData['image']) ?>",
        "url": "<?= htmlspecialchars($seoData['url']) ?>"
    }
    </script>
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        .theme-toggle-container {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1050;
        }
        
        .contact-card {
            transition: transform 0.2s;
            height: 100%;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
        }
        
        .contact-card .card-img-top {
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .contact-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }
        
        .category-icon {
            width: 24px;
            height: 24px;
        }
        
        .phone-link, .email-link {
            text-decoration: none;
        }
        
        .phone-link:hover, .email-link:hover {
            text-decoration: underline;
        }
        
        .loading-spinner {
            display: none;
        }
        
        .infinite-scroll-container {
            min-height: 200px;
        }
        
        /* Custom form styles */
        .form-floating > .form-control {
            height: calc(3.5rem + 2px);
        }
        
        .btn-whatsapp {
            background-color: #25d366;
            border-color: #25d366;
            color: white;
        }
        
        .btn-whatsapp:hover {
            background-color: #1da851;
            border-color: #1da851;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Theme Toggle Button -->
    <div class="theme-toggle-container">
        <button type="button" class="btn btn-outline-secondary btn-sm" id="theme-toggle">
            🌙 <span id="theme-text">Modo Escuro</span>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <strong>📱 Contatos</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/contatos">Agenda Pública</a>
                    </li>
                    <?php if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated']): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Meus Contatos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contacts/create">Novo Contato</a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated']): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/profile">Perfil</a></li>
                            <li><a class="dropdown-item" href="/settings">Configurações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="/logout" method="post" class="d-inline">
                                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                    <button type="submit" class="dropdown-item">Sair</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Cadastrar</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Main Content -->