<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container-xxl mt-6 px-4">
    <!-- Cabeçalho e Ações -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0"><?= esc($user->name) ?></h1>
            <p class="text-muted mb-0"><?= esc($user->email) ?> | <?= $user->is_admin ? '<span class="badge bg-info">Administrador</span>' : '<span class="badge bg-secondary">Usuário</span>' ?></p>
        </div>
        <?php if (session()->get('is_admin')): ?>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('admin/users/' . $user->id . '/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i>Editar</a>
            <form class="d-inline" action="<?= site_url('admin/users/' . $user->id . '/delete') ?>" method="post" onsubmit="return confirm('Tem certeza que deseja remover este usuário? Esta ação não pode ser desfeita.');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Remover</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <hr>

    <div class="row">
        <!-- Coluna de Tarefas -->
        <div class="col-lg-7 mb-4">
            <!-- Tarefas Próximas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Próximas Tarefas (<?= count($upcoming_tasks) ?>)</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($upcoming_tasks)): ?>
                        <div class="list-group-item">Nenhuma tarefa próxima.</div>
                    <?php else: ?>
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    Projeto: <?= esc($task->project_name) ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tarefas Atrasadas -->
            <?php if (!empty($overdue_tasks)): ?>
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Tarefas Atrasadas (<?= count($overdue_tasks) ?>)</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($overdue_tasks as $task): ?>
                        <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-danger"><?= esc($task->title) ?></h6>
                                <small class="text-danger">Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                            </div>
                            <p class="mb-1 text-muted small">
                                Projeto: <?= esc($task->project_name) ?>
                            </p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Coluna de Projetos -->
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Projetos Associados (<?= count($projects) ?>)</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($projects)): ?>
                        <div class="list-group-item">Este usuário não está associado a nenhum projeto.</div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($project->name) ?></h6>
                                        <?php if (!empty($project->description)): ?>
                                            <small class="text-muted d-block"><?= character_limiter(esc($project->description), 80) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($project->client_tag)): ?>
                                        <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Voltar para a lista</a>
    </div>
</main>

<?= $this->include('partials/footer') ?>