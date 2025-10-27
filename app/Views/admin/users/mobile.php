<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Usuários</h1>
        <a href="<?= site_url('admin/users/new') ?>" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Novo</a>
    </div>

    <?php if (empty($users)): ?>
        <div class="alert alert-info text-center">
            Nenhum usuário encontrado.
        </div>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($users as $user): ?>
            <a href="<?= site_url('admin/users/' . $user->id) ?>" class="card text-decoration-none text-body shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            <?= user_icon($user, 40) ?>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0"><?= esc($user->name) ?></h5>
                            <p class="card-text small text-muted mb-1"><?= esc($user->email) ?></p>
                            <div>
                                <?php if ($user->is_admin): ?>
                                    <span class="badge bg-primary">Admin</span>
                                <?php endif; ?>
                                <span class="badge bg-secondary"><i class="bi bi-kanban"></i> <?= $project_counts[$user->id] ?? 0 ?></span>
                                <span class="badge bg-warning text-dark"><i class="bi bi-card-checklist"></i> <?= $open_task_counts[$user->id] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer') ?>