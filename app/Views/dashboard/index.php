<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container-fluid mt-6 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Dashboard</h1>
            <?php if ($selected_client): ?>
                <p class="text-muted mb-0">
                    Exibindo dados para o cliente: 
                    <span class="badge fs-6" style="background-color: <?= esc($selected_client->color ?? '#6c757d') ?>;">
                        <?= esc($selected_client->name) ?>
                    </span>
                    <a href="<?= site_url('dashboard') ?>" class="ms-2 text-decoration-none" title="Limpar filtro">&times;</a>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Filtro de Cliente -->
        <div style="width: 350px;">
            <form id="clientFilterForm" method="get" action="<?= site_url('dashboard') ?>">
                <select id="client-select" name="client_id" placeholder="Filtrar por cliente...">
                    <!-- Options will be populated by TomSelect, but we can pre-fill for non-JS users -->
                </select>
            </form>
        </div>
    </div>

    <div class="row" data-masonry='{"percentPosition": true }'>

        <!-- Card: Próximas Entregas -->
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-check me-2"></i>Próximas Entregas (7 dias)</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($upcoming_tasks)): ?>
                        <div class="list-group-item">
                            <p class="text-muted mb-0 py-3">Nenhuma tarefa com entrega para os próximos 7 dias.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small class="text-warning"><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">Projeto: <?= esc($task->project_name) ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card: Tarefas em Atraso -->
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-x me-2"></i>Tarefas em Atraso</h5>
                </div>
                <div class="list-group list-group-flush">
                     <?php if (empty($overdue_tasks)): ?>
                        <div class="list-group-item">
                            <p class="text-muted mb-0 py-3">Nenhuma tarefa em atraso. Bom trabalho!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($overdue_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small class="text-danger"><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">Projeto: <?= esc($task->project_name) ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#client-select', {
        plugins: ['clear_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: [
            <?php foreach ($clients as $client): ?>
            {id: <?= $client->id ?>, name: '<?= esc($client->name, 'js') ?>'},
            <?php endforeach; ?>
        ],
        items: ['<?= $selected_client_id ?? '' ?>'],
        create: false,
        onChange: function(value) {
            // Submete o formulário quando um cliente é selecionado ou o filtro é limpo
            document.getElementById('clientFilterForm').submit();
        }
    });
});
</script>

<?= $this->include('partials/footer') ?>