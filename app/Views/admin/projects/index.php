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

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th style="width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= $project->id ?></td>
                <td>
                    <?php if (!empty($project->client_tag)): ?>
                        <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>
                </td>
                <td><?= esc($project->name) ?></td>
                <td><?= esc(character_limiter($project->description, 100)) ?></td>
                <td>
                    <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="btn btn-sm btn-primary">Ver Projeto</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?= $this->include('partials/footer') ?>