<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Clientes</h1>
        <a href="<?= site_url('admin/clients/new') ?>" class="btn btn-success"><i class="bi bi-plus-lg"></i> Novo Cliente</a>
    </div>

    <!-- Formulário de Busca -->
    <form method="get" action="<?= site_url('admin/clients') ?>" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou tag..." value="<?= esc($search ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Buscar</button>
        </div>
    </form>

    <?php if (empty($clients)): ?>
        <div class="alert alert-info">
            Nenhum cliente encontrado.
            <?php if (!empty($search)): ?>
                <a href="<?= site_url('admin/clients') ?>" class="alert-link">Limpar busca.</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tag</th>
                        <th>Responsável</th>
                        <th>Email</th>
                        <th style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <tr class="clickable-row" data-href="<?= site_url('admin/clients/' . $client->id) ?>">
                            <td><?= esc($client->name) ?></td>
                            <td><span class="badge" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span></td>
                            <td><?= esc($client->responsible_name) ?></td>
                            <td><?= esc($client->responsible_email) ?></td>
                            <td>
                                <a href="<?= site_url('admin/clients/' . $client->id . '/edit') ?>" class="btn btn-sm btn-primary" title="Editar"><i class="bi bi-pencil"></i></a>
                                <a href="<?= site_url('admin/clients/' . $client->id . '/delete') ?>" class="btn btn-sm btn-danger" title="Remover" onclick="return confirm('Tem certeza que deseja remover este cliente?');"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer') ?>