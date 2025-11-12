<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2">Dashboard</h1>
            <p class="text-muted">Bem-vindo(a), <?= esc(session()->get('name')) ?>!</p>
        </div>
    </div>
    <?php
    // Cria um mapa de IDs de projetos ativos para facilitar a filtragem das tarefas.
    $active_project_ids = array_flip(array_map(fn($p) => $p->id, $projects));

    // Filtra as tarefas atrasadas para incluir apenas as de projetos ativos.
    $active_overdue_tasks = array_filter($overdue_tasks, fn($task) => isset($active_project_ids[$task->project_id]));

    // Separação das tarefas atrasadas (já filtradas) em grupos.
    $overdue_completed_tasks = [];
    $overdue_with_client_tasks = [];
    $overdue_other_tasks = [];

    foreach ($active_overdue_tasks as $task) {
        if ($task->status === 'concluída') {
            $overdue_completed_tasks[] = $task;
        } elseif (in_array($task->status, ['com cliente', 'aprovação'])) {
            $overdue_with_client_tasks[] = $task;
        } else {
            $overdue_other_tasks[] = $task;
        }
    }

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

    <div class="d-grid gap-3">
        <!-- Card de Tarefas Atrasadas -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0 h6"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tarefas Atrasadas</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($overdue_completed_tasks) && empty($overdue_other_tasks)): ?>
                    <div class="p-3 text-center text-muted small">
                        Nenhuma tarefa atrasada (exceto "Com Cliente").
                    </div>
                <?php else: ?>
                    <!-- Grupo: Concluídas (Atrasadas) -->
                    <?php if (!empty($overdue_completed_tasks)): ?>
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-0 text-muted small">Concluídas (Atrasadas)</h6>
                        </div>
                        <ul class="list-group list-group-flush <?= !empty($overdue_other_tasks) ? 'border-bottom' : '' ?>"> 
                            <?php foreach ($overdue_completed_tasks as $task): ?>
                                <li class="list-group-item">
                                    <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="text-decoration-none d-block">
                                        <strong><?= esc($task->title) ?></strong>
                                        <small class="d-block text-muted">Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <!-- Grupo: Outras Tarefas Atrasadas -->
                    <?php if (!empty($overdue_other_tasks)): ?>
                        <div class="p-3 bg-light <?= !empty($overdue_completed_tasks) ? 'border-top' : '' ?>">
                            <h6 class="mb-0 text-muted small">Outras Tarefas Atrasadas</h6>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($overdue_other_tasks as $task): ?>
                                <li class="list-group-item">
                                    <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="text-decoration-none d-block">
                                        <strong><?= esc($task->title) ?></strong>
                                        <small class="d-block text-muted">Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card de Próximas Tarefas -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 h6"><i class="bi bi-clock-history me-2"></i>Próximas Tarefas</h5>
            </div>
            <div class="card-body p-0">
                <?php
                    // Filtra as próximas tarefas para incluir apenas as de projetos ativos.
                    $active_upcoming_tasks = array_filter($upcoming_tasks, fn($task) => isset($active_project_ids[$task->project_id]));
                ?>
                <?php if (empty($active_upcoming_tasks)): ?>
                    <div class="p-3 text-center text-muted small">
                        Nenhuma tarefa com vencimento próximo.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($active_upcoming_tasks as $task): ?>
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

        <!-- Card de Tarefas Atrasadas - Com Cliente -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 h6"><i class="bi bi-person-fill me-2"></i>Aguardando Cliente / Aprovação</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($overdue_with_client_tasks)): ?>
                    <div class="p-3 text-center text-muted small">
                        Nenhuma tarefa atrasada aguardando o cliente.
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($overdue_with_client_tasks as $task): ?>
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
