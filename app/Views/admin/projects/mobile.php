<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Projetos</h1>
        <?php if (session()->get('is_admin')): ?>
        <a href="<?= site_url('admin/projects/new') ?>" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Novo</a>
        <?php endif; ?>
    </div>

    <!-- Filtro de Busca -->
    <form method="get" action="<?= site_url('admin/projects') ?>" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar..." value="<?= esc($search ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <!-- Abas de Navegação -->
    <ul class="nav nav-tabs nav-fill mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $current_status === 'active' ? 'active' : '' ?>" href="<?= site_url('admin/projects?status=active') ?>">
                Ativos <span class="badge bg-secondary"><?= $project_counts['active'] ?? 0 ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_status === 'concluded' ? 'active' : '' ?>" href="<?= site_url('admin/projects?status=concluded') ?>">
                Concluídos <span class="badge bg-secondary"><?= $project_counts['concluded'] ?? 0 ?></span>
            </a>
        </li>
    </ul>

    <?php if (empty($projects)): ?>
        <div class="alert alert-info text-center">
            Nenhum projeto encontrado.
        </div>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($projects as $project): ?>
            <?php $stats = $project_task_stats[$project->id] ?? null; ?>
            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="card text-decoration-none text-body shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">
                            <?php if ($stats && !empty($stats->overdue_tasks) && $stats->overdue_tasks > 0): ?>
                                <i class="bi bi-exclamation-triangle-fill text-danger me-1" title="<?= $stats->overdue_tasks ?> tarefa(s) em atraso!"></i>
                            <?php endif; ?>
                            <?= esc($project->name) ?>
                        </h5>
                        <?php if (!empty($project->client_tag)): ?>
                            <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="card-text small text-muted mb-2"><?= esc(character_limiter($project->description, 100)) ?></p>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <?php if (!empty($project_members[$project->id])): ?>
                                <?php foreach (array_slice($project_members[$project->id], 0, 4) as $member): ?>
                                    <div class="me-n2" title="<?= esc($member->name) ?>">
                                        <?= user_icon($member, 24) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                            $total = $stats->total_tasks ?? 0;
                            $completed = $stats->completed_tasks ?? 0;
                            $percentage = ($total > 0) ? ($completed / $total) * 100 : 0;
                        ?>
                        <span class="small text-muted"><?= $completed ?> / <?= $total ?></span>
                    </div>

                    <?php if ($total > 0): ?>
                        <div class="progress" style="height: 6px;" title="<?= round($percentage) ?>% concluído">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer') ?>