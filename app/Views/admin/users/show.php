<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <!-- User Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <?= user_icon($user, 48) ?>
                <div>
                    <h1 class="mb-0"><?= esc($user->name) ?></h1>
                    <p class="text-muted mb-0"><?= esc($user->email) ?></p>
                </div>
            </div>
        </div>
        <a href="<?= site_url('admin/users/' . $user->id . '/edit') ?>" class="btn btn-primary">Editar Usuário</a>
    </div>

    <div class="row">
        <!-- Coluna da Esquerda: Informações e Projetos -->
        <div class="col-lg-5">
            <!-- Card: Informações do Usuário -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-vcard me-2"></i>Informações do Usuário</h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Nome:</strong> <?= esc($user->name) ?></p>
                    <p class="card-text"><strong>Email:</strong> <a href="mailto:<?= esc($user->email) ?>"><?= esc($user->email) ?></a></p>
                    <p class="card-text"><strong>Perfil:</strong> <?= $user->is_admin ? 'Administrador' : 'Membro' ?></p>
                    <p class="card-text"><strong>Status:</strong> 
                        <?php if ($user->is_active): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </p>
                    <p class="card-text"><small class="text-muted">Membro desde: <?= date('d/m/Y', strtotime($user->created_at)) ?></small></p>
                </div>
            </div>

            <!-- Card: Projetos do Usuário -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-folder2-open me-2"></i>Projetos Associados</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($projects)): ?>
                        <div class="list-group-item">
                            <p class="text-muted mb-0 py-3">Nenhum projeto associado a este usuário.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($project->name) ?></h6>
                                    <?php if (!empty($project->client_tag)): ?>
                                        <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1 text-muted small"><?= esc(character_limiter($project->description, 120)) ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Coluna da Direita: Tarefas -->
        <div class="col-lg-7">
            <!-- Card: Tarefas em Atraso (só aparece se houver) -->
            <?php if (!empty($overdue_tasks)): ?>
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-x me-2"></i>Tarefas em Atraso</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($overdue_tasks as $task): ?>
                        <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                <small class="text-danger"><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                            </div>
                            <p class="mb-1 text-muted small">Projeto: <?= esc($task->project_name) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Card: Próximas Entregas -->
            <div class="card">
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
    </div>
</main>

<?= $this->include('partials/footer') ?>