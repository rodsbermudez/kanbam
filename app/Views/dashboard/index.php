<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container-fluid mt-6 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">Dashboard</h1>
            <p class="text-muted">Bem-vindo(a) de volta, <?= esc(session()->get('user_name')) ?>!</p>
        </div>
    </div>

    <div class="row" data-bs-toggle="masonry">
        <!-- Card Próximas Tarefas -->
        <div class="mb-4" style="width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Próximas Tarefas (7 dias)</h5>
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
                                <?php if (!empty($task->client_tag)): ?>
                                    <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card Tarefas Atrasadas -->
        <div class="mb-4" style="width: 500px;">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Tarefas Atrasadas</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($overdue_tasks)): ?>
                        <div class="list-group-item">Nenhuma tarefa atrasada.</div>
                    <?php else: ?>
                        <?php foreach ($overdue_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-danger"><?= esc($task->title) ?></h6>
                                    <small class="text-danger">Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    Projeto: <?= esc($task->project_name) ?>
                                </p>
                                <?php if (!empty($task->client_tag)): ?>
                                    <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Card Meus Projetos -->
        <div class="mb-4" style="width: 500px;">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Meus Projetos</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($projects)): ?>
                        <div class="list-group-item">Nenhum projeto associado.</div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action">
                                <?= esc($project->name) ?>
                                <?php if (!empty($project->client_tag)): ?>
                                    <span class="badge float-end" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>