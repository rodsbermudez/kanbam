<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-plus"></i> Importar Relatório</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('admin/maintenance/store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="xml_file" class="form-label">Arquivo XML</label>
                            <select name="xml_file" id="xml_file" class="form-select" required>
                                <option value="">Selecione um arquivo...</option>
                                <?php if (!empty($xmlFiles)): ?>
                                    <?php foreach ($xmlFiles as $file): ?>
                                        <option value="<?= esc($file['name']) ?>">
                                            <?= esc($file['name']) ?> 
                                            (<?= date('d/m/Y H:i', $file['modified']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($xmlFiles)): ?>
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Nenhum arquivo XML encontrado na pasta. 
                                    Salve o XML em: <?= WRITEPATH ?>reports/
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value="">Selecione o cliente...</option>
                                <?php if (!empty($clients)): ?>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client->id ?>">
                                            <?= esc($client->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Título (opcional)</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Título será extraído do XML se vazio">
                            <div class="form-text">Se vazio, o título será extraído automaticamente do XML.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Salvar
                            </button>
                            <a href="<?= site_url('admin/maintenance') ?>" class="btn btn-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>