<?= $this->include('partials/header') ?>

<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Projetos</h1>
        <?php if (session()->get('is_admin')): ?>
        <a href="<?= site_url('admin/projects/new') ?>" class="btn btn-success">Novo Projeto</a>
        <?php endif; ?>
    </div>

    <!-- Filtro de Busca -->
    <form method="get" action="<?= site_url('admin/projects') ?>" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou descrição..." value="<?= esc($search ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit">Buscar</button>
        </div>
    </form>

    <!-- Abas de Navegação -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $current_status === 'active' ? 'active' : '' ?>" href="<?= site_url('admin/projects?status=active') ?>">
                Em Andamento <span class="badge bg-secondary"><?= $project_counts['active'] ?? 0 ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $current_status === 'concluded' ? 'active' : '' ?>" href="<?= site_url('admin/projects?status=concluded') ?>">
                Concluídos <span class="badge bg-secondary"><?= $project_counts['concluded'] ?? 0 ?></span>
            </a>
        </li>
    </ul>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th class="text-center" style="width: 15%;">Progresso</th>
                <th>Membros</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr>
                    <td colspan="6" class="text-center">Nenhum projeto encontrado.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($projects as $project): ?>
            <?php $stats = $project_task_stats[$project->id] ?? null; ?>
            <tr class="clickable-row" data-href="<?= site_url('admin/projects/' . $project->id) ?>">
                <td>
                    <?php if (!empty($project->client_tag)): ?>
                        <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($stats && !empty($stats->overdue_tasks) && $stats->overdue_tasks > 0): ?>
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2" title="<?= $stats->overdue_tasks ?> tarefa(s) em atraso!"></i>
                    <?php endif; ?>
                    <?= esc($project->name) ?>
                </td>
                <td><?= esc($project->description) ?></td>
                <td class="text-center align-middle">
                    <?php
                        $total = $stats->total_tasks ?? 0;
                        $completed = $stats->completed_tasks ?? 0;
                        $percentage = ($total > 0) ? ($completed / $total) * 100 : 0;
                    ?>
                    <?php if ($total > 0): ?>
                        <span class="small mb-1 d-block"><?= $completed ?> / <?= $total ?></span>
                        <div class="progress" style="height: 8px;" title="<?= round($percentage) ?>% concluído">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-secondary">0</span>
                    <?php endif; ?>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        <?php if (!empty($project_members[$project->id])): ?>
                            <?php foreach (array_slice($project_members[$project->id], 0, 4) as $member): // Limita a 4 ícones ?>
                                <div class="me-n2" title="<?= esc($member->name) ?>">
                                    <?= user_icon($member, 24) ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($project_members[$project->id]) > 4): ?>
                                <div class="user-icon-circle bg-secondary d-flex align-items-center justify-content-center" title="<?= count($project_members[$project->id]) - 4 ?> mais" style="width: 24px; height: 24px; font-size: 0.7rem; color: white;">
                                    +<?= count($project_members[$project->id]) - 4 ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?= $this->include('partials/footer') ?>