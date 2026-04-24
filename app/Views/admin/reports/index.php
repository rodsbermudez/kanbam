<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Relatórios</h1>
            <p class="lead text-muted">Gerencie os relatórios de manutenção.</p>
        </div>
        <div>
            <a href="<?= site_url('admin/maintenance/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Importar Relatório
            </a>
        </div>
    </div>

    <!-- Filtro por cliente -->
    <form method="get" action="<?= site_url('admin/maintenance') ?>" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="client_id" class="form-select">
                    <option value="">Todos os clientes</option>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client->id ?>" <?= ($filter_client_id ?? null) == $client->id ? 'selected' : '' ?>>
                                <?= esc($client->name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <?php if (empty($reports)): ?>
        <div class="alert alert-info">
            Nenhum relatório encontrado.
            <a href="<?= site_url('admin/maintenance/create') ?>">Importar primeiro relatório.</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): ?>
                        <tr>
                            <td>
                                <?php if ($report->client): ?>
                                    <span class="badge" style="background-color: <?= esc($report->client->color ?? '#6c757d') ?>">
                                        <?= esc($report->client->tag) ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= esc($report->title) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($report->created_at)) ?></td>
                            <td>
                                <a href="<?= site_url('admin/maintenance/' . $report->id . '/view') ?>" class="btn btn-sm btn-primary" title="Ver Relatório">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="<?= site_url('admin/maintenance/' . $report->id . '/delete') ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este relatório?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer') ?>