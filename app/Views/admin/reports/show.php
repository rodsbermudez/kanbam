<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<style>
    .report-section {
        margin-bottom: 2.5rem;
    }
    .issue-card {
        border-left-width: 5px;
    }
    .issue-sev-Alta { border-left-color: var(--bs-danger); }
    .issue-sev-Média { border-left-color: var(--bs-warning); }
    .issue-sev-Baixa { border-left-color: var(--bs-info); }

    .code-block {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: .25rem;
        padding: 1rem;
        font-family: monospace;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .details-list li {
        word-break: break-all;
    }
</style>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Relatório de Análise de SEO</h1>
            <p class="text-muted mb-0">URL: <strong><?= esc($report->url) ?></strong> | Data da Análise: <?= date('d/m/Y H:i', strtotime($report->original_created_at)) ?></p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= site_url('admin/projects/' . $project_id . '?active_tab=reports') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Voltar ao Projeto</a>
            <form action="<?= site_url('admin/reports/' . $report->id . '/delete') ?>" method="post" onsubmit="return confirm('Tem certeza que deseja remover este relatório importado?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Remover Relatório</button>
            </form>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-lg-7">
            <!-- Análise de Títulos -->
            <?php if (isset($report_data->headingAnalysis)): ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Análise de Títulos (H1-H6)</h5></div>
                    <div class="card-body">
                        <p><?= esc($report_data->headingAnalysis->comment) ?></p>
                        <ul class="list-group list-group-horizontal-md">
                            <li class="list-group-item flex-fill">H1: <strong><?= $report_data->headingAnalysis->h1Count ?></strong></li>
                            <li class="list-group-item flex-fill">H2: <strong><?= $report_data->headingAnalysis->h2Count ?></strong></li>
                            <li class="list-group-item flex-fill">H3: <strong><?= $report_data->headingAnalysis->h3Count ?></strong></li>
                            <li class="list-group-item flex-fill">H4: <strong><?= $report_data->headingAnalysis->h4Count ?></strong></li>
                            <li class="list-group-item flex-fill">H5: <strong><?= $report_data->headingAnalysis->h5Count ?></strong></li>
                            <li class="list-group-item flex-fill">H6: <strong><?= $report_data->headingAnalysis->h6Count ?></strong></li>
                        </ul>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Análise de Palavra-chave -->
            <?php if (isset($report_data->keywordAnalysis)): ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Análise de Palavra-chave: "<?= esc($report_data->keywordAnalysis->targetKeyword) ?>"</h5></div>
                    <div class="card-body">
                        <p><?= esc($report_data->keywordAnalysis->comment) ?></p>
                        <ul class="list-group">
                            <li class="list-group-item">Presente no Título: <?= $report_data->keywordAnalysis->isPresentInTitle ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' ?></li>
                            <li class="list-group-item">Presente na Meta Descrição: <?= $report_data->keywordAnalysis->isPresentInMetaDescription ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' ?></li>
                            <li class="list-group-item">Presente no H1: <?= $report_data->keywordAnalysis->isPresentInH1 ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' ?></li>
                        </ul>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Análise de Imagens -->
            <?php if (isset($report_data->imageAnalysis)): ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Análise de Imagens</h5></div>
                    <div class="card-body">
                        <p><?= esc($report_data->imageAnalysis->comment) ?></p>
                        <ul class="list-group">
                            <li class="list-group-item">Total de Imagens: <strong><?= $report_data->imageAnalysis->totalImages ?></strong></li>
                            <li class="list-group-item">Com Atributo 'alt': <strong class="text-success"><?= $report_data->imageAnalysis->imagesWithAlt ?></strong></li>
                            <li class="list-group-item">Sem Atributo 'alt': <strong class="text-danger"><?= $report_data->imageAnalysis->imagesWithoutAlt ?></strong></li>
                        </ul>
                    </div>
                </div>
            </section>
            <?php endif; ?>

        </div>
        <div class="col-lg-5">
            <!-- Análise de Tecnologias -->
            <?php if (isset($report_data->integrationsAnalysis) || isset($report_data->otherIntegrationsAnalysis)): ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Tecnologias e Integrações</h5></div>
                    <div class="card-body">
                        <?php if (isset($report_data->integrationsAnalysis)): $ia = $report_data->integrationsAnalysis; ?>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">Pixel do Facebook: <?= $ia->facebookPixel->isDetected ? '<span class="badge bg-success">Detectado</span>' : '<span class="badge bg-secondary">Não</span>' ?></li>
                                <li class="list-group-item">Google Analytics: <?= $ia->googleAnalytics->isDetected ? '<span class="badge bg-success">Detectado</span>' : '<span class="badge bg-secondary">Não</span>' ?></li>
                                <li class="list-group-item">Google Tag Manager: <?= $ia->googleTagManager->isDetected ? '<span class="badge bg-success">Detectado</span>' : '<span class="badge bg-secondary">Não</span>' ?></li>
                                <li class="list-group-item">Google Search Console: <?= $ia->googleSearchConsole->isDetected ? '<span class="badge bg-success">Detectado</span>' : '<span class="badge bg-secondary">Não</span>' ?></li>
                            </ul>
                        <?php endif; ?>
                        <?php if (!empty($report_data->otherIntegrationsAnalysis)): ?>
                            <h6>Outras Tecnologias:</h6>
                            <ul class="list-group">
                            <?php foreach ($report_data->otherIntegrationsAnalysis as $tech): ?>
                                <li class="list-group-item"><?= esc($tech->name) ?> <span class="badge bg-info float-end"><?= esc($tech->category) ?></span></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Análise de Segurança e Cache -->
            <?php if (isset($report_data->securityAndCacheAnalysis)): $sca = $report_data->securityAndCacheAnalysis; ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Segurança e Cache</h5></div>
                    <div class="card-body">
                        <h6>Segurança</h6>
                        <p>Firewall Detectado: <?= $sca->security->isFirewallDetected ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>' ?></p>
                        <?php if (!empty($sca->security->detectedSystems)): ?>
                            <p>Sistemas: <?= implode(', ', array_map('esc', $sca->security->detectedSystems)) ?></p>
                        <?php endif; ?>
                        <hr>
                        <h6>Cache</h6>
                        <p>Cache Detectado: <?= $sca->caching->isCachingDetected ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-secondary">Não</span>' ?></p>
                        <?php if (!empty($sca->caching->details)): ?>
                            <p class="mb-1">Detalhes:</p>
                            <div class="code-block">
                                <?= implode('<br>', array_map('esc', $sca->caching->details)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Análise de Sitemap -->
            <?php if (isset($report_data->sitemapAnalysis)): $sa = $report_data->sitemapAnalysis; ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Análise de Sitemap</h5></div>
                    <div class="card-body">
                        <p>Sitemap Encontrado: <?= $sa->isFound ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' ?></p>
                        <?php if ($sa->isFound): ?>
                            <p><strong>Localização:</strong> <a href="<?= esc($sa->foundAt) ?>" target="_blank"><?= esc($sa->foundAt) ?></a></p>
                        <?php endif; ?>
                        <p class="text-muted small"><?= esc($sa->details) ?></p>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <!-- Análise de Links -->
            <?php if (isset($report_data->linkAnalysis)): $la = $report_data->linkAnalysis; ?>
            <section class="report-section">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Análise de Links</h5></div>
                    <div class="card-body">
                        <ul class="list-group details-list">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Links Internos
                                <span class="badge bg-primary rounded-pill"><?= $la->internal->totalCount ?></span>
                            </li>
                            <?php if (!empty($la->internal->links)): ?>
                                <?php foreach (array_slice($la->internal->links, 0, 3) as $link): ?>
                                    <li class="list-group-item text-muted small ps-4">&rarr; <?= esc($link) ?></li>
                                <?php endforeach; ?>
                                <?php if ($la->internal->totalCount > 3): ?>
                                    <li class="list-group-item text-muted small ps-4">... e mais <?= $la->internal->totalCount - 3 ?></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Links Externos
                                <span class="badge bg-primary rounded-pill"><?= $la->external->totalCount ?></span>
                            </li>
                             <?php if (!empty($la->external->links)): ?>
                                <?php foreach (array_slice($la->external->links, 0, 3) as $link): ?>
                                    <li class="list-group-item text-muted small ps-4">&rarr; <?= esc($link) ?></li>
                                <?php endforeach; ?>
                                <?php if ($la->external->totalCount > 3): ?>
                                    <li class="list-group-item text-muted small ps-4">... e mais <?= $la->external->totalCount - 3 ?></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </section>
            <?php endif; ?>

        </div>
    </div>

    <!-- Seção de Problemas Principais -->
    <?php if (!empty($report_data->issues)): ?>
    <section class="report-section">
        <h2 class="mb-3">Pontos de Melhoria Encontrados (<?= count($report_data->issues) ?>)</h2>
        <?php foreach ($report_data->issues as $issue): ?>
            <div class="card issue-card issue-sev-<?= esc($issue->severity) ?> mb-3">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0"><?= esc($issue->title) ?></h5>
                    <span class="badge text-bg-<?= strtolower(esc($issue->severity)) ?>"><?= esc($issue->severity) ?></span>
                </div>
                <div class="card-body">
                    <p><strong>Explicação:</strong> <?= esc($issue->explanation) ?></p>
                    
                    <?php if (!empty($issue->offendingElements)): ?>
                        <p class="mb-1"><strong>Exemplos Encontrados:</strong></p>
                        <div class="code-block mb-3">
                            <?php foreach ($issue->offendingElements as $element): ?>
                                <span><?= esc($element) ?></span><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <p class="mb-1"><strong>Solução Sugerida:</strong></p>
                    <div class="p-3 border rounded bg-light">
                        <?= $issue->solution ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

</main>

<?= $this->include('partials/footer') ?>