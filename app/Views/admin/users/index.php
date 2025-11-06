<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Usuários</h1>
        <a href="<?= site_url('admin/users/new') ?>" class="btn btn-success">Novo Usuário</a>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Email</th>
                <th>ID do Slack</th>
                <th class="text-center">Projetos</th>
                <th class="text-center">Tarefas Abertas</th>
                <th>Status</th>
                <th style="width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" class="text-center">Nenhum usuário cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr class="clickable-row" data-href="<?= site_url('admin/users/' . $user->id) ?>">
                    <td>
                        <div class="d-flex align-items-center">
                            <?= user_icon($user, 32) ?>
                            <div class="ms-2">
                                <strong class="d-block"><?= esc($user->name) ?></strong>
                                <small class="text-muted"><?= $user->is_admin ? 'Administrador' : 'Membro' ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= esc($user->email) ?></td>
                    <td class="align-middle"><?= esc($user->slack_user_id) ?></td>
                    <td class="text-center align-middle">
                        <span class="badge bg-info"><?= $project_counts[$user->id] ?? 0 ?></span>
                    </td>
                    <td class="text-center align-middle">
                        <span class="badge bg-warning text-dark"><?= $open_task_counts[$user->id] ?? 0 ?></span>
                    </td>
                    <td class="align-middle">
                        <?php if ($user->is_active): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td class="align-middle">
                        <a href="<?= site_url('admin/users/' . $user->id . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este usuário?')">Remover</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?= $this->include('partials/footer') ?>
