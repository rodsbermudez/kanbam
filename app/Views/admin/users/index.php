<?= $this->include('partials/header') ?>

<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Gerenciar Usuários</h1>
        <a href="<?= site_url('admin/users/new') ?>" class="btn btn-success">Novo Usuário</a>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th style="width: 60px;">Ícone</th>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Status</th>
                <th>Admin</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= user_icon($user) ?></td>
                <td><?= $user->id ?></td>
                <td><?= esc($user->name) ?></td>
                <td><?= esc($user->email) ?></td>
                <td><?= $user->is_active ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>' ?></td>
                <td><?= $user->is_admin ? '<span class="badge bg-primary">Sim</span>' : '<span class="badge bg-secondary">Não</span>' ?></td>
                <td>
                    <a href="<?= site_url('admin/users/' . $user->id . '/edit') ?>" class="btn btn-sm btn-primary">Editar</a>
                    <a href="<?= site_url('admin/users/' . $user->id . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este usuário?')">Remover</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?= $this->include('partials/footer') ?>