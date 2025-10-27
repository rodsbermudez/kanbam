<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <?= user_icon($user, 48) ?>
            <div>
                <h1 class="h2 mb-0"><?= esc($user->name) ?></h1>
                <p class="text-muted mb-0 small"><?= esc($user->email) ?></p>
            </div>
        </div>
        <?php if (session()->get('is_admin')): ?>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= site_url('admin/users/' . $user->id . '/edit') ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Editar</a>
                <form action="<?= site_url('admin/users/' . $user->id . '/delete') ?>" method="post" onsubmit="return confirm('Tem certeza que deseja remover este usuário?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm" title="Remover"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-grid gap-3">
        <!-- Tarefas Atrasadas -->
        <?php if (!empty($overdue_tasks)): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0 h6"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tarefas Atrasadas (<?= count($overdue_tasks) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($overdue_tasks as $task): ?>
                        <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                            <strong><?= esc($task->title) ?></strong>
                            <small class="d-block text-muted">Projeto: <?= esc($task->project_name) ?></small>
                        </a>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Próximas Tarefas -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 h6"><i class="bi bi-clock-history me-2"></i>Próximas Tarefas (<?= count($upcoming_tasks) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcoming_tasks)): ?>
                    <div class="p-3 text-center text-muted small">Nenhuma tarefa próxima.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <strong><?= esc($task->title) ?></strong>
                                <small class="d-block text-muted">Projeto: <?= esc($task->project_name) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Projetos Associados -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 h6"><i class="bi bi-kanban me-2"></i>Projetos Associados (<?= count($projects) ?>)</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($projects)): ?>
                    <div class="p-3 text-center text-muted small">Nenhum projeto associado.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <?= esc($project->name) ?>
                                <?php if (!empty($project->client_tag)): ?>
                                    <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-4 d-grid">
        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Voltar para a lista</a>
    </div>
</main>

<?= $this->include('partials/footer') ?>