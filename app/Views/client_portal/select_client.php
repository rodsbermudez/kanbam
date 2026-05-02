<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <h1>Selecionar Cliente</h1>
    <p class="text-muted">Escolha um cliente para visualizar seu portal:</p>
    <hr>

    <?php if (session()->get('error')): ?>
        <div class="alert alert-danger"><?= session()->get('error') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($clients as $client): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <span class="badge fs-6" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span>
                            <?= esc($client->name) ?>
                        </h5>
                        <p class="card-text text-muted small">
                            Responsável: <?= esc($client->responsible_name ?? '-') ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="<?= site_url('portal/switch-client/' . $client->id) ?>" class="btn btn-primary w-100">
                            Acessar Portal
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?= $this->include('partials/footer') ?>
