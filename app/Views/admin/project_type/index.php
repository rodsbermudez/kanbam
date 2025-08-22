<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tipos de Projeto para IA</h1>
        <a href="<?= site_url('admin/project-type/new') ?>" class="btn btn-success">Novo Tipo</a>
    </div>

    <p class="text-muted">Gerencie os modelos (prompts) que a Inteligência Artificial usará para gerar tarefas para diferentes tipos de projeto.</p>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th style="width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($types)): ?>
                <tr>
                    <td colspan="3" class="text-center">Nenhum tipo de projeto cadastrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($types as $type): ?>
                <tr class="clickable-row" data-href="<?= site_url('admin/project-type/' . $type->id . '/edit') ?>">
                    <td><?= $type->id ?></td>
                    <td><?= esc($type->name) ?></td>
                    <td>
                        <a href="<?= site_url('admin/project-type/' . $type->id . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este tipo de projeto?')">Remover</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?= $this->include('partials/footer') ?>