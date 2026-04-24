<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<style>
    .report-html {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }
    .report-header {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .report-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .report-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .report-section {
        margin-bottom: 2rem;
    }
    .report-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .report-description {
        color: #6c757d;
        font-style: italic;
        margin-bottom: 1rem;
    }
    .report-list {
        list-style: disc;
        padding-left: 1.5rem;
    }
    .report-pendencies li {
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 0.25rem;
    }
    .report-footer {
        font-size: 0.85rem;
    }
</style>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= site_url('admin/maintenance') ?>" class="btn btn-sm btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <h1><?= esc($report->title) ?></h1>
            <p class="text-muted">
                <?php if ($report->client): ?>
                    <span class="badge" style="background-color: <?= esc($report->client->color ?? '#6c757d') ?>">
                        <?= esc($report->client->tag) ?>
                    </span>
                <?php endif; ?>
                <span class="ms-2"><?= date('d/m/Y H:i', strtotime($report->created_at)) ?></span>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/maintenance/' . $report->id . '/delete') ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este relatório?');">
                <i class="bi bi-trash"></i> Excluir
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body report-html">
            <?= $htmlContent ?>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>