<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <h1>Gerenciar Agências</h1>
    <hr>

    <?php if (session()->get('success')): ?>
        <div class="alert alert-success"><?= session()->get('success') ?></div>
    <?php endif; ?>

    <?php if (session()->get('error')): ?>
        <div class="alert alert-danger"><?= session()->get('error') ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <form class="d-flex gap-2" method="get" action="<?= site_url('admin/agencies') ?>">
                <input type="text" class="form-control" name="search" placeholder="Buscar agência..." value="<?= esc($search ?? '') ?>">
                <button class="btn btn-outline-secondary">Buscar</button>
            </form>
        </div>
        <?php if (session()->get('is_admin')): ?>
            <a href="<?= site_url('admin/agencies/new') ?>" class="btn btn-primary">Nova Agência</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Contato</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($agencies)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Nenhuma agência cadastrada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agencies as $agency): ?>
                            <tr>
                                <td><a href="<?= site_url('admin/agencies/' . $agency->id) ?>"><?= esc($agency->name) ?></a></td>
                                <td><?= esc($agency->contact_name ?? '-') ?></td>
                                <td><?= esc($agency->email ?? '-') ?></td>
                                <td><?= esc($agency->phone ?? '-') ?></td>
                                <td>
                                    <a href="<?= site_url('admin/agencies/' . $agency->id . '/edit') ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="<?= site_url('admin/agencies/' . $agency->id . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover esta agência?');">Remover</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>
