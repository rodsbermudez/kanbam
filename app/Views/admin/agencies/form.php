<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h1><?= isset($agency) ? 'Editar Agência' : 'Criar Nova Agência' ?></h1>
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

            <form action="<?= isset($agency) ? site_url('admin/agencies/' . $agency->id) : site_url('admin/agencies') ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($agency)): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome da Agência <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $agency->name ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="contact_name" class="form-label">Nome do Contato</label>
                    <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?= old('contact_name', $agency->contact_name ?? '') ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $agency->email ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= old('phone', $agency->phone ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('admin/agencies') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>
