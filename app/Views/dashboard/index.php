<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container-fluid mt-6 px-4">
    <h1 class="mb-4">Dashboard</h1>

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

<?= $this->include('partials/footer') ?>