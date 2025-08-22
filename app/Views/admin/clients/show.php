<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <!-- Client Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="mb-0"><?= esc($client->name) ?></h1>
                <span class="badge fs-6" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span>
            </div>
            <p class="text-muted mb-0">Detalhes do cliente e resumo de atividades.</p>
        </div>
        <a href="<?= site_url('admin/clients/' . $client->id . '/edit') ?>" class="btn btn-primary">Editar Cliente</a>
    </div>

    <div class="row">
        <!-- Coluna da Esquerda: Informações e Projetos (40%) -->
        <div class="col-lg-5">
            <!-- Card: Informações do Cliente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-vcard me-2"></i>Informações de Contato</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($client->responsible_name)): ?>
                        <h6 class="card-subtitle mb-2 text-muted">Responsável</h6>
                        <p class="card-text"><?= esc($client->responsible_name) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($client->responsible_email)): ?>
                        <h6 class="card-subtitle mt-3 mb-2 text-muted">Email</h6>
                        <p class="card-text"><a href="mailto:<?= esc($client->responsible_email) ?>"><?= esc($client->responsible_email) ?></a></p>
                    <?php endif; ?>

                    <?php if (!empty($client->responsible_phone)): ?>
                        <h6 class="card-subtitle mt-3 mb-2 text-muted">Telefone</h6>
                        <p class="card-text"><?= esc($client->responsible_phone) ?></p>
                    <?php endif; ?>

                    <?php if (empty($client->responsible_name) && empty($client->responsible_email) && empty($client->responsible_phone)): ?>
                        <p class="text-muted mb-0">Nenhuma informação de contato do responsável cadastrada.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card: Projetos do Cliente -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-folder2-open me-2"></i>Projetos</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($projects)): ?>
                        <div class="list-group-item">
                            <p class="text-muted mb-0 py-3">Nenhum projeto associado a este cliente.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($project->name) ?></h6>
                                </div>
                                <p class="mb-1 text-muted small"><?= esc(character_limiter($project->description, 120)) ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Coluna da Direita: Tarefas (60%) -->
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