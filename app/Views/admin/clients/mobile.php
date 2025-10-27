<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Clientes</h1>
        <a href="<?= site_url('admin/clients/new') ?>" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Novo</a>
    </div>

    <!-- Formulário de Busca -->
    <form method="get" action="<?= site_url('admin/clients') ?>" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou tag..." value="<?= esc($search ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <?php if (empty($clients)): ?>
        <div class="alert alert-info text-center">
            Nenhum cliente encontrado.
            <?php if (!empty($search)): ?>
                <a href="<?= site_url('admin/clients') ?>" class="alert-link">Limpar busca.</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($clients as $client): ?>
            <a href="<?= site_url('admin/clients/' . $client->id) ?>" class="card text-decoration-none text-body shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title mb-1"><?= esc($client->name) ?></h5>
                        <span class="badge" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span>
                    </div>
                    <?php if(!empty($client->responsible_name)): ?>
                        <p class="card-text small text-muted mb-0">
                            <i class="bi bi-person"></i> <?= esc($client->responsible_name) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer') ?>