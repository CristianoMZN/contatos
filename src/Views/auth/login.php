<?php include dirname(__DIR__) . '/layout/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3>🔐 Entrar</h3>
                        <p class="text-muted">Acesse sua conta de contatos</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post" action="/login">
                        <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($old_email ?? '') ?>" required>
                            <label for="email">Email</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <label for="password">Senha</label>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Lembrar de mim
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Entrar
                        </button>
                    </form>

                    <div class="text-center">
                        <p class="mb-2">
                            <a href="/forgot-password" class="text-decoration-none">
                                Esqueceu sua senha?
                            </a>
                        </p>
                        <p class="mb-0">
                            Não tem conta? 
                            <a href="/register" class="text-decoration-none">
                                Cadastre-se aqui
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="row mt-4 text-center">
                <div class="col-4">
                    <div class="p-3">
                        <div class="text-primary fs-3">🔒</div>
                        <small class="text-muted">Seguro</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3">
                        <div class="text-primary fs-3">📱</div>
                        <small class="text-muted">Responsivo</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3">
                        <div class="text-primary fs-3">⚡</div>
                        <small class="text-muted">Rápido</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/layout/footer.php'; ?>