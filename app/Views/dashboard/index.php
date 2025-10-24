<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Dashboard</h1>
            <p class="lead">Bem-vindo(a) de volta, <?= esc(session()->get('user_name')) ?>!</p>
        </div>
    </div>

    <div class="row">
        <!-- Card Próximas Tarefas -->
        <?php
        // Mapeamento de status para cores de badge, para ser usado nos loops
        $status_colors = [
            'concluída'         => 'bg-success',
            'cancelada'         => 'bg-danger',
            'em desenvovimento' => 'bg-primary',
            'ajustes'           => 'bg-warning text-dark',
            'aprovação'         => 'bg-info text-dark',
            'não iniciadas'     => 'bg-light text-dark',
            'com cliente'       => 'bg-info text-dark',
            'aprovada'          => 'bg-success',
            'implementada'      => 'bg-success',
            'default'           => 'bg-secondary'
        ];
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Próximas Tarefas (7 dias)</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_tasks)): ?>
                        <div class="p-3 text-center text-muted">Nenhuma tarefa próxima.</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($upcoming_tasks as $task): ?>
                                <?php $status_class = $status_colors[$task->status] ?? $status_colors['default']; ?>
                                <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                        <small><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <p class="mb-0 text-muted small">
                                            Projeto: <?= esc($task->project_name) ?>
                                        </p>
                                        <div>
                                            <span class="badge <?= $status_class ?> me-1"><?= esc(ucfirst($task->status)) ?></span>
                                            <?php if (!empty($task->client_tag)): ?>
                                                <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card Tarefas Atrasadas -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tarefas Atrasadas</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($overdue_tasks)): ?>
                        <div class="p-3 text-center text-muted">Nenhuma tarefa atrasada.</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($overdue_tasks as $task): ?>
                                <?php $status_class = $status_colors[$task->status] ?? $status_colors['default']; ?>
                                <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-danger"><?= esc($task->title) ?></h6>
                                        <small class="text-danger">Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <p class="mb-0 text-muted small">
                                            Projeto: <?= esc($task->project_name) ?>
                                        </p>
                                        <div>
                                            <span class="badge <?= $status_class ?> me-1"><?= esc(ucfirst($task->status)) ?></span>
                                            <?php if (!empty($task->client_tag)): ?>
                                                <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card Meus Projetos -->
        <div class="col-md-12 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-kanban me-2"></i>Meus Projetos Ativos</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($projects)): ?>
                        <div class="p-3 text-center text-muted">Nenhum projeto associado.</div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($projects as $project): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="text-decoration-none">
                                        <?= esc($project->name) ?>
                                    </a>
                                    <?php if (!empty($project->client_tag)): ?>
                                        <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="<?= site_url('admin/projects') ?>" class="btn btn-sm btn-outline-secondary">Ver todos os projetos</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>