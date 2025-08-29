<?= $this->include('partials/header') ?>

<style>
         .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background-color: #f8f9fa; /* bg-light */
        padding: 1rem; /* Garante espaçamento em telas pequenas */
    }
    .login-card {
        width: 100%;
        max-width: 400px;
    }
</style>

<div class="login-container">
    <div class="card login-card shadow-sm">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="<?= base_url('logo-patropi.svg') ?>" alt="Logo" style="height: 40px;">
                <h3 class="mt-3">Portal do Cliente</h3>
            </div>

            <?= $this->include('partials/toasts') ?>

            <form action="<?= site_url('portal/' . $token . '/login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha de Acesso</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('partials/footer_portal') ?>
