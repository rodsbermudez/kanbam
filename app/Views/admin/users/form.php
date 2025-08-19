<?= $this->include('partials/header') ?>

<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <h1><?= isset($user) ? 'Editar Usuário' : 'Criar Novo Usuário' ?></h1>
            <hr>

            <form action="<?= isset($user) ? site_url('admin/users/' . $user->id) : site_url('admin/users') ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($user)): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user->name ?? '') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="initials" class="form-label">Sigla (2 letras)</label>
                        <input type="text" class="form-control" id="initials" name="initials" value="<?= old('initials', $user->initials ?? '') ?>" maxlength="2">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="color" class="form-label">Cor do Ícone</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="<?= old('color', $user->color ?? '#6c757d') ?>" title="Escolha uma cor">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user->email ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <?php if (isset($user)): ?>
                        <div class="form-text">Deixe em branco para não alterar a senha.</div>
                    <?php endif; ?>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="is_admin" name="is_admin" <?= old('is_admin', $user->is_admin ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_admin">
                        É Administrador
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" <?= old('is_active', $user->is_active ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">
                        Usuário Ativo
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>
