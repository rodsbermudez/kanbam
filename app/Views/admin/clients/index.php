<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Clientes</h1>
        <a href="<?= site_url('admin/clients/new') ?>" class="btn btn-success">Novo Cliente</a>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Cliente</th>
                <th>Tag</th>
                <th>Responsável</th>
                <th>Email</th>
                <th>Telefone</th>
                <th style="width: 200px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="7" class="text-center">Nenhum cliente cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client->id ?></td>
                    <td><?= esc($client->name) ?></td>
                    <td><span class="badge" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span></td>
                    <td><?= esc($client->responsible_name) ?></td>
                    <td><?= esc($client->responsible_email) ?></td>
                    <td><?= esc($client->responsible_phone) ?></td>
                    <td>
                        <a href="<?= site_url('admin/clients/' . $client->id . '/edit') ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="<?= site_url('admin/clients/' . $client->id . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este cliente?')">Remover</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?= $this->include('partials/footer') ?>