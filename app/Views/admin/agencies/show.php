<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= esc($agency->name) ?></h1>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/agencies/' . $agency->id . '/edit') ?>" class="btn btn-primary">Editar</a>
            <a href="<?= site_url('admin/agencies') ?>" class="btn btn-secondary">Voltar</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Dados da Agência -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dados da Agência</h5>
                </div>
                <div class="card-body">
                    <p><strong>Contato:</strong> <?= esc($agency->contact_name ?? '-') ?></p>
                    <p><strong>Email:</strong> <?= esc($agency->email ?? '-') ?></p>
                    <p><strong>Telefone:</strong> <?= esc($agency->phone ?? '-') ?></p>
                </div>
            </div>
        </div>
                <div class="card-body">
                    <p><strong>Contato:</strong> <?= esc($agency->contact_name ?? '-') ?></p>
                    <p><strong>Email:</strong> <?= esc($agency->email ?? '-') ?></p>
                    <p><strong>Telefone:</strong> <?= esc($agency->phone ?? '-') ?></p>
                </div>
            </div>
        </div>

        <!-- Clientes Vinculados -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Clientes Vinculados</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($linked_clients)): ?>
                        <p class="text-muted">Nenhum cliente vinculado.</p>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tag</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($linked_clients as $client): ?>
                                    <tr class="clickable-row" data-href="<?= site_url('admin/clients/' . $client->id) ?>">
                                        <td><?= esc($client->name) ?></td>
                                        <td><span class="badge" style="background-color: <?= esc($client->color ?? '#6c757d') ?>"><?= esc($client->tag) ?></span></td>
                                        <td>
                                            <a href="<?= site_url('admin/agencies/' . $agency->id . '/unlinkclient/' . $client->id) ?>" 
                                               class="btn btn-sm btn-warning"
                                               onclick="return confirm('Deseja desvincular este cliente?');">Desvincular</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vincular Novo Cliente -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Vincular Cliente</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($available_clients)): ?>
                        <p class="text-muted">Todos os clientes já estão vinculados à alguma agência.</p>
                    <?php else: ?>
                        <form action="<?= site_url('admin/agencies/' . $agency->id . '/linkclient') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <select name="client_id" class="form-select" required>
                                        <option value="">Selecione um cliente...</option>
                                        <?php foreach ($available_clients as $client): ?>
                                            <option value="<?= $client->id ?>"><?= esc($client->name) ?> (<?= esc($client->tag) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Vincular</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>
