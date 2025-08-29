<?= $this->include('partials/header') ?>

<style>
    body {
        background-color: #f8f9fa;
        padding-top: 60px; /* Espaço para o cabeçalho fixo */
    }
    .mobile-header {
        height: 60px;
        background-color: #212529;
        color: #fff;
        border-bottom: 1px solid #495057;
        z-index: 1030;
    }
    .offcanvas-header {
        background-color: #343a40;
        color: #fff;
        border-bottom: 1px solid #495057;
    }
    .offcanvas-body {
        background-color: #343a40;
        color: #adb5bd;
    }
    .project-list .list-group-item {
        background-color: transparent;
        border-color: rgba(255,255,255,0.1);
        color: #adb5bd;
        padding: .75rem 0;
        border-radius: 0;
    }
    .project-list .list-group-item.active {
        background-color: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
        padding-left: 1rem;
    }
    .sidebar-footer {
        border-top: 1px solid #495057;
        padding-top: 1rem;
        margin-top: auto; /* Empurra o rodapé para o final */
    }
    /* Estilos do cronograma (copiados de show.php) */
    .month-header { font-size: 1.5rem; font-weight: 300; color: var(--bs-primary); border-bottom: 1px solid #dee2e6; padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 1.5rem; }
    .month-header:first-of-type { margin-top: 0; }
    .week-card { margin-bottom: 1.5rem; background-color: #fff; }
    .week-card .list-group-item { display: flex; justify-content: space-between; align-items: center; }
    .current-week-highlight { border-color: var(--bs-primary); border-width: 2px; }
    .current-week-highlight .card-header { background-color: var(--bs-primary-bg-subtle); font-weight: bold; }
    .week-card .task-info { flex-grow: 1; }
    .week-card .task-meta { display: flex; align-items: center; gap: 1rem; min-width: 120px; justify-content: flex-end; }
</style>

<!-- Cabeçalho Fixo para Mobile -->
<header class="mobile-header fixed-top d-flex justify-content-between align-items-center px-3">
    <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="bi bi-list fs-4"></i>
    </button>
    <img src="<?= base_url('logo-patropi.svg') ?>" alt="Logo" style="height: 30px;">
    <div style="width: 40px;"></div> <!-- Espaçador para centralizar a logo -->
</header>

<!-- Menu Lateral (Off-canvas) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Navegação</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-3">
        <h6 class="text-light mb-3">Seus Projetos</h6>
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

        <div class="sidebar-footer mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Olá, <?= esc(session()->get('client_portal_client_name')) ?></span>
                <a href="<?= site_url('portal/logout') ?>" class="btn btn-sm btn-outline-secondary" title="Sair"><i class="bi bi-box-arrow-right"></i></a>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary w-100 dropdown-toggle" type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-palette-fill me-2"></i> Mudar Tema
                </button>
                <ul class="dropdown-menu dropdown-menu-dark w-100" aria-labelledby="themeDropdown" id="clientThemeSwitcher">
                    <li><h6 class="dropdown-header">Escolha um Tema</h6></li>
                    <li><a class="dropdown-item" href="#" data-theme="sandstone">Sandstone (Padrão)</a></li>
                    <li><a class="dropdown-item" href="#" data-theme="slate">Slate</a></li>
                    <li><a class="dropdown-item" href="#" data-theme="darkly">Darkly</a></li>
                    <li><a class="dropdown-item" href="#" data-theme="flatly">Flatly</a></li>
                    <li><a class="dropdown-item" href="#" data-theme="lumen">Lumen</a></li>
                    <li><a class="dropdown-item" href="#" data-theme="minty">Minty</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Conteúdo Principal -->
<main class="container py-4">
    <?php if (!$selected_project): ?>
        <div class="text-center mt-5">
            <h2 class="text-muted">Bem-vindo!</h2>
            <p>Toque no menu <i class="bi bi-list"></i> no canto superior para selecionar um projeto.</p>
        </div>
    <?php else: ?>
        <div class="mb-4">
            <h2><?= esc($selected_project->name) ?></h2>
            <p class="text-muted"><?= esc($selected_project->description) ?></p>
        </div>

        <!-- Arquivos Disponíveis -->
        <?php if (!empty($visible_files)): ?>
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Arquivos Disponíveis</h5></div>
            <div class="list-group list-group-flush">
                <?php foreach ($visible_files as $file): ?>
                    <a href="<?= site_url('portal/files/' . $file->id . '/download') ?>" 
                       target="<?= $file->item_type === 'link' ? '_blank' : '_self' ?>" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
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
        </div>
        <?php endif; ?>

        <!-- Cronograma -->
        <h4 class="mb-3">Cronograma de Entregas</h4>
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