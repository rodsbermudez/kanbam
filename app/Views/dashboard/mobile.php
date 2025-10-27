<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2">Dashboard</h1>
            <p class="text-muted">Bem-vindo(a), <?= esc(session()->get('user_name')) ?>!</p>
        </div>
    </div>

    <div class="d-grid gap-3">
        <!-- Card de Tarefas Atrasadas -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0 h6"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tarefas Atrasadas</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($overdue_tasks)): ?>
                    <div class="p-3 text-center text-muted small">
                        Nenhuma tarefa em atraso.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($overdue_tasks as $task): ?>
                            <li class="list-group-item">
                                <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="text-decoration-none d-block">
                                    <strong><?= esc($task->title) ?></strong>
                                    <small class="d-block text-muted">
                                        Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?>
                                    </small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card de Próximas Tarefas -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 h6"><i class="bi bi-clock-history me-2"></i>Próximas Tarefas</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcoming_tasks)): ?>
                    <div class="p-3 text-center text-muted small">
                        Nenhuma tarefa com vencimento próximo.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <li class="list-group-item">
                                <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="text-decoration-none d-block">
                                    <strong><?= esc($task->title) ?></strong>
                                    <small class="d-block text-muted">
                                        Vence em: <?= date('d/m/Y', strtotime($task->due_date)) ?>
                                    </small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card de Meus Projetos -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 h6"><i class="bi bi-kanban me-2"></i>Meus Projetos Ativos</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($projects)): ?>
                    <div class="p-3 text-center text-muted small">
                        Você não está em nenhum projeto ativo.
                    </div>
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
</main>

<?= $this->include('partials/footer') ?>
