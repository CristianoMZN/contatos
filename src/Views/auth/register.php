<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3>👤 Criar Conta</h3>
                        <p class="text-muted">Comece a organizar seus contatos</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post" action="/register">
                        <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($old_name ?? '') ?>" required>
                            <label for="name">Nome Completo</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($old_email ?? '') ?>" required>
                            <label for="email">Email</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="8" required>
                            <label for="password">Senha (mínimo 8 caracteres)</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                            <label for="password_confirmation">Confirmar Senha</label>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Concordo com os <a href="/terms" target="_blank">termos de uso</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Criar Conta
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-0">
                            Já tem conta? 
                            <a href="/login" class="text-decoration-none">
                                Entre aqui
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Benefits -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">✨ Benefícios da sua conta</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <span class="text-success">✓</span> 
                            Contatos privados e seguros
                        </li>
                        <li class="mb-2">
                            <span class="text-success">✓</span> 
                            Múltiplos telefones e emails por contato
                        </li>
                        <li class="mb-2">
                            <span class="text-success">✓</span> 
                            Galeria de imagens
                        </li>
                        <li class="mb-2">
                            <span class="text-success">✓</span> 
                            Contatos comerciais públicos
                        </li>
                        <li class="mb-0">
                            <span class="text-success">✓</span> 
                            Autenticação de dois fatores (2FA)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirmation');
    
    function validatePasswordMatch() {
        if (password.value !== confirmation.value) {
            confirmation.setCustomValidity('As senhas não coincidem');
        } else {
            confirmation.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswordMatch);
    confirmation.addEventListener('input', validatePasswordMatch);
});
</script>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>