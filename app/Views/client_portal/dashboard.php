<?= $this->include('partials/header') ?>

<?php
    // Determina se a barra lateral de arquivos deve ser exibida
    $has_files = !empty($visible_files);
    // Determina se deve mostrar a barra lateral de relatórios (quando não há projeto selecionado)
    $show_reports_sidebar = empty($selected_project) && !empty($client_reports) && !$has_files;
    // Determina se alguma barra lateral do meio deve aparecer
    $has_sidebar = $has_files || $show_reports_sidebar;
?>

<style>
    body {
        display: grid;
        grid-template-columns: <?= $has_sidebar ? '280px 280px 1fr' : '280px 1fr' ?>;
        grid-template-rows: 100vh; /* Linha única com altura total */
        height: 100vh;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .client-sidebar {
        background-color: #343a40; /* Cor de fundo da sidebar */
        overflow-y: auto;
        border-right: 1px solid #495057;
        display: flex;
        flex-direction: column;
    }
    .client-files-sidebar {
        background-color: #f1f3f5; /* Um cinza um pouco mais claro que o fundo */
        border-right: 1px solid #dee2e6;
        padding: 1.5rem;
        overflow-y: auto;
    }
    .client-main {
        padding: 2rem;
        overflow-y: auto;
        background-color: #f8f9fa; /* Fundo mais claro para o conteúdo */
    }
    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #495057;
    }
    .sidebar-content {
        padding: 1.5rem;
        flex-grow: 1; /* Ocupa o espaço restante */
    }
    .sidebar-footer {
        padding: 1.5rem;
        border-top: 1px solid #495057;
        color: #adb5bd;
    }
    .project-list .list-group-item {
        background-color: transparent;
        border-color: rgba(255,255,255,0.1);
        color: #adb5bd;
        padding: .75rem 0 .75rem 1rem;
        border-radius: 0;
    }
    .project-list .list-group-item.active {
        background-color: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }
    /* Estilos do cronograma (copiados de show.php) */
    .month-header { font-size: 1.75rem; font-weight: 300; color: var(--bs-primary); border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; margin-top: 2rem; margin-bottom: 1.5rem; }
    .month-header:first-of-type { margin-top: 0; }
    .week-card { margin-bottom: 1.5rem; background-color: #fff; }
    .week-card .list-group-item { display: flex; justify-content: space-between; align-items: center; }
    .current-week-highlight { border-color: var(--bs-primary); border-width: 2px; }
    .current-week-highlight .card-header { background-color: var(--bs-primary-bg-subtle); font-weight: bold; }
    .week-card .task-info { flex-grow: 1; }
    .week-card .task-meta { display: flex; align-items: center; gap: 1rem; min-width: 120px; justify-content: flex-end; }
    
    /* Estilos para relatórios no portal */
    .report-html {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
    }
    .report-html .report-header {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .report-html .report-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .report-html .report-section {
        margin-bottom: 2rem;
    }
    .report-html .report-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .report-html .report-description {
        color: #6c757d;
        font-style: italic;
        margin-bottom: 1rem;
    }
    .report-html .report-list {
        list-style: disc;
        padding-left: 1.5rem;
    }
    .report-html .report-pendencies li {
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 0.25rem;
    }
</style>

<aside class="client-sidebar">
    <div class="sidebar-header">
        <img src="<?= base_url('logo-patropi.svg') ?>" alt="Logo" style="height: 40px;">
    </div>

    <div class="sidebar-content">
        <h5 class="text-light mb-3">Seus Projetos</h5>
        <?php if (empty($projects)): ?>
            <p class="text-muted">Nenhum projeto encontrado.</p>
        <?php else: ?>
            <div class="list-group list-group-flush project-list">
                <?php foreach ($projects as $project): ?>
                    <a href="<?= site_url('portal/dashboard/' . $project->id) ?>" 
                       class="list-group-item list-group-item-action <?= ($selected_project && $selected_project->id == $project->id) ? 'active' : '' ?>">
                        <?= esc($project->name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($client_reports)): ?>
    <div class="sidebar-content mt-3">
        <h5 class="text-light mb-3">
            <a href="<?= site_url('portal/reports/list') ?>" class="text-decoration-none text-light">
                <i class="bi bi-file-earmark-text me-2"></i>Manutenções
            </a>
        </h5>
    </div>
    <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <span>Olá, <?= esc(session()->get('client_portal_client_name')) ?></span>
            <?php if (session()->get('is_agency')): ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Trocar Cliente
                    </button>
                    <ul class="dropdown-menu">
                        <?php
                            $clientModel = new \App\Models\ClientModel();
                            $agencyClients = $clientModel->where('agency_id', session()->get('client_portal_agency_id'))
                                                    ->orderBy('name', 'ASC')
                                                    ->findAll();
                            foreach ($agencyClients as $c): ?>
                                <li><a class="dropdown-item <?= (session()->get('client_portal_client_id') == $c->id) ? 'active' : '' ?>" 
                                       href="<?= site_url('portal/switch-client/' . $c->id) ?>">
                                    <?= esc($c->name) ?> (<?= esc($c->tag) ?>)
                                </a></li>
                            <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <a href="<?= site_url('portal/logout') ?>" class="btn btn-sm btn-outline-secondary" title="Sair"><i class="bi bi-box-arrow-right"></i></a>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary w-100 dropdown-toggle" type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-palette-fill me-2"></i> Mudar Tema
            </button>
            <ul class="dropdown-menu dropdown-menu-dark w-100" aria-labelledby="themeDropdown" id="clientThemeSwitcher">
                <li><h6 class="dropdown-header">Escolha um Tema</h6></li>
                <li><a class="dropdown-item" href="#" data-theme="sandstone">Tema Claro</a></li>
                <li><a class="dropdown-item" href="#" data-theme="superhero">Tema Escuro</a></li>
            </ul>
        </div>
    </div>
</aside>

<?php if ($has_files): ?>
<aside class="client-files-sidebar">
    <h5 class="mb-3"><i class="bi bi-folder2-open me-2"></i>Arquivos Disponíveis</h5>
    <div class="list-group list-group-flush">
        <?php foreach ($visible_files as $file): ?>
            <a href="<?= site_url('portal/files/' . $file->id . '/download') ?>" 
               target="<?= $file->item_type === 'link' ? '_blank' : '_self' ?>" 
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0"
               style="background: transparent; border-color: rgba(0,0,0,0.05);">
                <div>
                    <strong class="d-block"><?= esc($file->title) ?></strong>
                    <?php if ($file->description): ?><small class="text-muted"><?= esc($file->description) ?></small><?php endif; ?>
                </div>
                <div class="text-end fs-5">
                    <i class="bi <?= $file->item_type === 'link' ? 'bi-box-arrow-up-right' : 'bi-download' ?>"></i>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</aside>
<?php elseif ($show_reports_sidebar): ?>
<aside class="client-files-sidebar">
    <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Manutenções</h5>
    <div class="list-group list-group-flush">
        <?php foreach ($client_reports as $report): ?>
            <a href="<?= site_url('portal/reports/' . $report->id) ?>" 
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0"
               style="background: transparent; border-color: rgba(0,0,0,0.05);">
                <div>
                    <strong class="d-block"><?= esc($report->title) ?></strong>
                    <small class="text-muted"><?= date('d/m/Y', strtotime($report->created_at)) ?></small>
                </div>
                <div class="text-end fs-5">
                    <i class="bi bi-chevron-right"></i>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</aside>
<?php endif; ?>

<main class="client-main">
    <?php if (!empty($selected_report)): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="<?= site_url('portal/reports/list') ?>" class="btn btn-sm btn-secondary mb-2">
                    <i class="bi bi-arrow-left"></i> Voltar para lista
                </a>
                <h2><?= esc($selected_report->title) ?></h2>
                <p class="text-muted"><?= date('d/m/Y H:i', strtotime($selected_report->created_at)) ?></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body report-html">
                <?= $report_html ?>
            </div>
        </div>
    <?php elseif (empty($selected_project)): ?>
        <div class="text-center mt-5">
            <h2 class="text-muted">Selecione um projeto na lista ao lado para ver o cronograma.</h2>
        </div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Cronograma: <?= esc($selected_project->name) ?></h2>
        </div>

        <p class="text-muted">Visão geral das tarefas do projeto agrupadas por semana de entrega.</p>

        <?php if (empty($weekly_schedule)): ?>
            <div class="alert alert-info mt-4">Nenhuma tarefa com data de entrega para exibir no cronograma.</div>
        <?php else: ?>
            <?php foreach ($weekly_schedule as $month): ?>
                <h3 class="month-header"><?= esc($month['label']) ?></h3>
                <?php foreach ($month['weeks'] as $week_key => $week): ?>
                    <?php $is_current_week = (isset($current_week_key) && $week_key === $current_week_key) ? 'current-week-highlight' : ''; ?>
                    <div class="card week-card <?= $is_current_week ?>">
                        <div class="card-header"><strong><?= esc($week['label']) ?></strong></div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($week['items'] as $task): ?>
                                <?php
                                    $status_colors = ['concluída' => 'bg-success', 'cancelada' => 'bg-danger', 'em desenvovimento' => 'bg-primary', 'ajustes' => 'bg-warning text-dark', 'aprovação' => 'bg-info text-dark', 'não iniciadas' => 'bg-light text-dark', 'default' => 'bg-secondary'];
                                    $status_class = $status_colors[$task->status] ?? $status_colors['default'];
                                ?>
                                <li class="list-group-item">
                                    <div class="task-info">
                                        <strong class="d-block"><?= esc($task->title) ?></strong>
                                        <small class="text-muted"><?= esc($task->description) ?></small>
                                    </div>
                                    <div class="task-meta">
                                        <span class="badge <?= $status_class ?>"><?= esc(ucfirst($task->status)) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?= $this->include('partials/footer_portal') ?>
