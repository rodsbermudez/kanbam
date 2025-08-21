<?= $this->include('partials/header') ?>

<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h1><?= isset($project) ? 'Editar Projeto' : 'Criar Novo Projeto' ?></h1>
            <hr>

            <?php if (session()->get('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= isset($project) ? site_url('admin/projects/' . $project->id) : site_url('admin/projects') ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($project)): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome do Projeto</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $project->name ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= old('description', $project->description ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="client_id" class="form-label">Cliente</label>
                    <select class="form-select" id="client_id" name="client_id">
                        <option value="">-- Nenhum cliente associado --</option>
                        <?php if (!empty($clients)): ?>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client->id ?>" <?= (isset($project) && $project->client_id == $client->id) ? 'selected' : '' ?>>
                                    <?= esc($client->name) ?> (<?= esc($client->tag) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('admin/projects') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>