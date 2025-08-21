<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h1><?= isset($client) ? 'Editar Cliente' : 'Criar Novo Cliente' ?></h1>
            <hr>

            <?php if (session()->get('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= isset($client) ? site_url('admin/clients/' . $client->id) : site_url('admin/clients') ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($client)): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label for="name" class="form-label">Nome do Cliente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $client->name ?? '') ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tag" class="form-label">Tag <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tag" name="tag" value="<?= old('tag', $client->tag ?? '') ?>" required>
                        <small class="form-text text-muted">Ex: CLIENTEABREV</small>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="color" class="form-label">Cor</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="<?= old('color', $client->color ?? '#6c757d') ?>" title="Escolha uma cor para o cliente">
                    </div>
                </div>

                <fieldset class="border p-3 my-3 rounded">
                    <legend class="float-none w-auto px-2 h6">Contato do Responsável (Opcional)</legend>
                    <div class="mb-3">
                        <label for="responsible_name" class="form-label">Nome do Responsável</label>
                        <input type="text" class="form-control" id="responsible_name" name="responsible_name" value="<?= old('responsible_name', $client->responsible_name ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="responsible_email" class="form-label">Email do Responsável</label>
                            <input type="email" class="form-control" id="responsible_email" name="responsible_email" value="<?= old('responsible_email', $client->responsible_email ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="responsible_phone" class="form-label">Telefone do Responsável</label>
                            <input type="text" class="form-control" id="responsible_phone" name="responsible_phone" value="<?= old('responsible_phone', $client->responsible_phone ?? '') ?>">
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('admin/clients') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>