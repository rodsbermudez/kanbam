<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<style>
#column-width-selector .dropdown-item.active {
    color: #000 !important;
}
/* Estilos para o Cronograma Semanal */
.month-header {
    font-size: 1.75rem;
    font-weight: 300;
    color: var(--bs-primary);
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-top: 2rem;
    margin-bottom: 1.5rem;
}
.month-header:first-of-type {
    margin-top: 0;
}
.week-card {
    margin-bottom: 1.5rem;
}
.week-card .list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}
.current-week-highlight {
    border-color: var(--bs-primary);
    border-width: 2px;
}
.current-week-highlight .card-header {
    background-color: var(--bs-primary-bg-subtle);
    font-weight: bold;
}
.week-card .task-info {
    flex-grow: 1;
}
.week-card .task-meta {
    display: flex;
    align-items: center;
    cursor: pointer;
}
.week-card .task-info {
    flex-grow: 1;
}
.week-card .task-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 150px; /* Alinha os avatares e datas */
    justify-content: flex-end;
}
.task-entry {
    display: block;
    padding: 0.2rem 0.4rem;
    margin-bottom: 0.25rem;
    border-radius: 0.25rem;
    background-color: var(--bs-primary-bg-subtle);
    border-left: 3px solid var(--bs-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
#document-list .list-group-item-action {
    cursor: grab;
}
.kanban-layout-simplified .kanban-column[data-status="com cliente"],
.kanban-layout-simplified .kanban-column[data-status="ajustes"],
.kanban-layout-simplified .kanban-column[data-status="aprovada"],
.kanban-layout-simplified .kanban-column[data-status="implementada"] {
    display: none;
}
.dropdown-item.active {
    font-weight: bold;
    background-color: var(--bs-primary-bg-subtle);
}
</style>

<main class="container-fluid mt-6 px-4">

    <!-- Cabeçalho e Ações -->
    <div class="content-constrained">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div style="width: 45%;">
                <?php if (!empty($project->client_tag)): ?>
                    <div class="mb-1">
                        <span class="badge fs-6" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h1 class="mb-0"><?= esc($project->name) ?></h1>
                    <?php if (isset($project->status) && $project->status === 'concluded'): ?>
                        <span class="badge bg-success fs-6">Concluído</span>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-0"><?= esc($project->description) ?></p>
            </div>
            <div id="main-actions" class="d-flex align-items-center justify-content-end gap-3" style="width: 55%;">
                <!-- Ações do Quadro -->
                <div id="board-actions-group" class="btn-group" role="group" aria-label="Ações do Quadro">
                    <button type="button" class="btn btn-secondary" id="multiSelectBtn"><i class="bi bi-check2-square"></i> Selecionar Múltiplos</button>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Largura das Colunas">
                            <i class="bi bi-columns-gap"></i>
                        </button>
                        <ul class="dropdown-menu" id="column-width-selector">
                            <li><h6 class="dropdown-header">Largura das Colunas</h6></li>
                            <li><a class="dropdown-item" href="#" data-width="compact">Compacta</a></li>
                            <li><a class="dropdown-item" href="#" data-width="normal">Normal</a></li>
                            <li><a class="dropdown-item" href="#" data-width="large">Grande</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="layout-toggle-simplified" data-layout="simplified">Layout Simplificado</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Ações de Tarefas -->
                <div class="btn-group" role="group" aria-label="Ações de Tarefas">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal"><i class="bi bi-plus-lg"></i> Nova Tarefa</button>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-magic"></i> Gerar com IA
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach($project_types as $type): ?>
                                <li><a class="dropdown-item" href="#" 
                                    data-bs-toggle="modal" data-bs-target="#aiTaskModal"
                                    data-type-id="<?= $type->id ?>"
                                    data-label-description="<?= esc($type->label_description, 'attr') ?>"
                                    data-placeholder-description="<?= esc($type->placeholder_description, 'attr') ?>"
                                    data-label-items="<?= esc($type->label_items, 'attr') ?>"
                                    data-placeholder-items="<?= esc($type->placeholder_items, 'attr') ?>"
                                    ><?= esc($type->name) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!-- Ações do Projeto -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill me-1"></i> Ações
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <?php if (session()->get('is_admin')): ?>
                            <a class="dropdown-item" href="<?= site_url('admin/projects/' . $project->id . '/edit') ?>"><i class="bi bi-pencil-square me-2"></i>Editar</a>
                        </li>
                        <li>
                            <?php
                                $isActive = isset($project->status) && $project->status === 'active';
                                $toggleText = $isActive ? 'Concluir' : 'Reativar';
                                $toggleClass = $isActive ? '' : 'text-success fw-bold';
                                $toggleIcon = $isActive ? 'bi-check-circle' : 'bi-arrow-clockwise';
                            ?>
                            <form action="<?= site_url('admin/projects/' . $project->id . '/toggle-status') ?>" method="post" id="toggleStatusForm" class="d-none"><?= csrf_field() ?></form>
                            <a class="dropdown-item <?= $toggleClass ?>" href="#" onclick="event.preventDefault(); document.getElementById('toggleStatusForm').submit();">
                                <i class="bi <?= $toggleIcon ?> me-2"></i><?= $toggleText ?>
                            </a>
                            <?php endif; ?>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#postponeProjectModal"><i class="bi bi-calendar-plus me-2"></i>Adiar Início do Projeto</a>
                        </li>
                        <?php if (session()->get('is_admin')): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= site_url('admin/projects/' . $project->id . '/delete') ?>" onclick="return confirm('Tem certeza que deseja remover este projeto? Esta ação não pode ser desfeita.')"><i class="bi bi-trash me-2"></i>Remover</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div id="bulk-actions" class="d-none align-items-center gap-2">
                <strong id="selectionCount">0 tarefas selecionadas</strong>
                <button type="button" class="btn btn-primary" id="bulkEditBtn" disabled><i class="bi bi-pencil-square"></i> Editar Selecionadas</button>
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled><i class="bi bi-trash"></i> Remover Selecionadas</button>
                <button type="button" class="btn btn-secondary" id="cancelSelectBtn">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Abas de Navegação -->
    <ul class="nav nav-tabs mb-4 justify-content-center" id="projectTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'board' ? 'active' : '' ?>" id="board-tab" data-bs-toggle="tab" data-bs-target="#board-tab-pane" type="button" role="tab" aria-controls="board-tab-pane" aria-selected="<?= $active_tab === 'board' ? 'true' : 'false' ?>">Quadro</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'members' ? 'active' : '' ?>" id="members-tab" data-bs-toggle="tab" data-bs-target="#members-tab-pane" type="button" role="tab" aria-controls="members-tab-pane" aria-selected="<?= $active_tab === 'members' ? 'true' : 'false' ?>">Membros</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'documents' ? 'active' : '' ?>" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button" role="tab" aria-controls="documents-tab-pane" aria-selected="<?= $active_tab === 'documents' ? 'true' : 'false' ?>">Documentos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'files' ? 'active' : '' ?>" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-tab-pane" type="button" role="tab" aria-controls="files-tab-pane" aria-selected="<?= $active_tab === 'files' ? 'true' : 'false' ?>">Arquivos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'reports' ? 'active' : '' ?>" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports-tab-pane" type="button" role="tab" aria-controls="reports-tab-pane" aria-selected="<?= $active_tab === 'reports' ? 'true' : 'false' ?>">Relatórios</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'timeline' ? 'active' : '' ?>" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline-tab-pane" type="button" role="tab" aria-controls="timeline-tab-pane" aria-selected="<?= $active_tab === 'timeline' ? 'true' : 'false' ?>">Cronograma</button>
        </li>
    </ul>

    <!-- Conteúdo das Abas -->
    <div class="tab-content" id="projectTabContent">
        <!-- Aba Quadro Kanban -->
        <div class="tab-pane fade <?= $active_tab === 'board' ? 'show active' : '' ?>" id="board-tab-pane" role="tabpanel" aria-labelledby="board-tab" tabindex="0">
            <div class="kanban-wrapper">
                <button id="kanban-scroll-left" class="btn kanban-nav-btn kanban-nav-left"><i class="bi bi-arrow-left-circle-fill fs-2"></i></button>
                
                <div class="kanban-board-container" id="kanban-container">
                    <div class="kanban-board <?= ($project->kanban_layout ?? 'normal') === 'simplified' ? 'kanban-layout-simplified' : '' ?>">
                        <?php foreach ($statuses as $status): ?>
                            <div class="kanban-column" data-status="<?= esc($status) ?>">
                                <div class="kanban-column-title" data-status-title="<?= esc($status) ?>"><?= esc($status) ?></div>
                                <div class="kanban-cards" data-status="<?= esc($status) ?>">
                                    <?php if (!empty($tasks[$status])): ?>
                                        <?php foreach ($tasks[$status] as $task): ?>
                                            <?php
                                                $cardClass = '';
                                                if (!empty($task->due_date)) {
                                                    try {
                                                        $dueDate = new \DateTime($task->due_date);
                                                        $today = new \DateTime('today');
                                                        
                                                        if ($dueDate < $today) { // A data de entrega já passou
                                                            $cardClass = 'card-danger';
                                                        } else {
                                                            $interval = $today->diff($dueDate);
                                                            if ($interval->days <= 3) { // Faltam 3 dias ou menos
                                                                $cardClass = 'card-warning';
                                                            } else {
                                                                $cardClass = 'card-info'; // Card azul para tarefas sem urgência
                                                            }
                                                        }
                                                    } catch (Exception $e) {
                                                        // Em caso de data inválida, não faz nada
                                                    }
                                                } else {
                                                    $cardClass = 'bg-light'; // Fundo light para tarefas sem data
                                                }
                                            ?>
                                            <div class="kanban-card <?= $cardClass ?>" data-task-id="<?= $task->id ?>">                                                
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="card-title fw-bold pe-2 mb-0"><?= esc($task->title) ?></h6>
                                                    
                                                    <!-- Dropdown Menu -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end bg-light">
                                                            <li><a class="dropdown-item edit-task-btn text-dark" href="#" data-task-id="<?= $task->id ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                                            <li><a class="dropdown-item add-note-btn text-dark" href="#" data-task-id="<?= $task->id ?>"><i class="bi bi-journal-plus me-2"></i>Criar Nota</a></li>
                                                            <li><a class="dropdown-item delete-task-btn text-dark" href="#" data-task-id="<?= $task->id ?>" data-task-title="<?= esc($task->title) ?>"><i class="bi bi-trash me-2"></i>Remover</a></li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <?php if (!empty($task->description)): ?>
                                                    <p class="card-text mt-2 small"><?= nl2br(esc($task->description)) ?></p>
                                                <?php endif; ?>

                                                <!-- Seção de Notas -->
                                                <?php if (!empty($notes_by_task_id[$task->id])): ?>
                                                <div class="kanban-card-notes">
                                                    <?php foreach (array_slice($notes_by_task_id[$task->id], -2) as $note): // Mostra apenas as últimas 2 notas ?>
                                                    <div class="note-item">
                                                        <div class="note-text">
                                                            <?= esc(trim($note->note)) ?>
                                                        </div>
                                                        <div class="note-meta d-flex justify-content-between align-items-center">
                                                            <small><?= esc($note->name) ?> - <?= date('d/m H:i', strtotime($note->created_at)) ?></small>
                                                            <?php if (session()->get('is_admin') || session()->get('user_id') == $note->user_id): ?>
                                                                <a href="#" class="delete-note-btn" data-note-id="<?= $note->id ?>" data-note-text="<?= esc(character_limiter($note->note, 50)) ?>" title="Remover nota">Remover</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php endif; ?>

                                                <div class="kanban-card-footer d-flex justify-content-between align-items-center">
                                                    <?php if (!empty($task->user_id) && isset($users_by_id[$task->user_id])): ?>
                                                        <?= user_icon($users_by_id[$task->user_id], 24) ?>
                                                    <?php else: ?>
                                                        <div style="width: 24px; height: 24px;"></div> <!-- Placeholder para manter o alinhamento -->
                                                    <?php endif; ?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <?php if (!empty($project->client_tag)): ?>
                                                            <span class="badge" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
                                                        <?php endif; ?>
                                                        <small class="text-muted">
                                                            <?php if (!empty($task->due_date)): ?>
                                                                <?= date('d/m/Y', strtotime($task->due_date)) ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button id="kanban-scroll-right" class="btn kanban-nav-btn kanban-nav-right"><i class="bi bi-arrow-right-circle-fill fs-2"></i></button>
            </div>
        </div>

        <!-- Aba Documentos -->
        <div class="tab-pane fade content-constrained <?= $active_tab === 'documents' ? 'show active' : '' ?>" id="documents-tab-pane" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">
            <div class="row">
                <!-- Menu Lateral de Documentos -->
                <div class="col-md-3">
                    <div class="d-grid mb-3">
                        <button class="btn btn-success" id="addDocumentBtn"><i class="bi bi-plus-lg"></i> Nova Página</button>
                    </div>
                    <div class="list-group" id="document-list">
                        <?php if (empty($documents)): ?>
                            <span class="list-group-item text-muted">Nenhum documento criado.</span>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <a href="#" class="list-group-item list-group-item-action" data-doc-id="<?= $doc->id ?>">
                                    <?= esc($doc->title) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Área de Conteúdo do Documento -->
                <div class="col-md-9">
                    <div id="document-view" class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 id="document-title" class="mb-0">Selecione um documento</h4>
                            <div id="document-actions" class="d-none">
                                <button class="btn btn-sm btn-primary" id="editDocumentBtn"><i class="bi bi-pencil"></i> Editar</button>
                                <button class="btn btn-sm btn-danger" id="deleteDocumentBtn"><i class="bi bi-trash"></i> Remover</button>
                            </div>
                        </div>
                        <div class="card-body" id="document-content" style="min-height: 60vh;">
                            <p class="text-muted">Selecione um documento no menu à esquerda para visualizar seu conteúdo aqui.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba Arquivos -->
        <div class="tab-pane fade content-constrained <?= $active_tab === 'files' ? 'show active' : '' ?>" id="files-tab-pane" role="tabpanel" aria-labelledby="files-tab" tabindex="0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Arquivos e Links</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFileModal"><i class="bi bi-upload me-2"></i>Novo Arquivo</button>
            </div>

            <?php
                // Lista de extensões que podem ser abertas diretamente no navegador
                $viewableExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'txt', 'md', 'webp'];
            ?>
            <?php if (empty($project_files)): ?>
                <div class="alert alert-info">Nenhum arquivo foi enviado para este projeto ainda.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Tamanho</th>
                                <th>Enviado por</th>
                                <th class="text-center" style="width: 150px;">Visível ao Cliente</th>
                                <th>Data</th>
                                <th style="width: 180px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($project_files as $item): ?>
                            <tr>
                                <td class="align-middle">
                                    <strong><?= esc($item->title) ?></strong>
                                    <?php if ($item->item_type === 'link'): ?>
                                        <i class="bi bi-link-45deg text-muted ms-1" title="Link Externo"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><small><?= esc($item->description) ?></small></td>
                                <td class="text-center align-middle">
                                    <?php if ($item->item_type === 'file'): ?>
                                        <span class="badge bg-secondary"><?= esc(strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION))) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-info">LINK</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($item->item_type === 'file'): ?>
                                        <?php
                                            $size = $item->file_size;
                                            if ($size < 1024) { echo $size . ' B'; }
                                            elseif ($size < 1048576) { echo round($size / 1024, 1) . ' KB'; }
                                            else { echo round($size / 1048576, 1) . ' MB'; }
                                        ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><?= esc($item->uploader_name) ?></td>
                                <td class="text-center align-middle">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input file-visibility-toggle" type="checkbox" role="switch" 
                                               data-file-id="<?= $item->id ?>" <?= isset($item->is_visible_to_client) && $item->is_visible_to_client ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td class="align-middle"><?= date('d/m/Y H:i', strtotime($item->created_at)) ?></td>
                                <td class="align-middle">
                                    <?php if ($item->item_type === 'file'): ?>
                                        <?php
                                            $extension = strtolower(pathinfo($item->file_name, PATHINFO_EXTENSION));
                                            if (in_array($extension, $viewableExtensions)):
                                        ?>
                                            <a href="<?= site_url('admin/files/' . $item->id . '/view') ?>" class="btn btn-sm btn-info" title="Ver" target="_blank"><i class="bi bi-eye"></i></a>
                                        <?php endif; ?>
                                        <a href="<?= site_url('admin/files/' . $item->id . '/download') ?>" class="btn btn-sm btn-primary" title="Baixar"><i class="bi bi-download"></i></a>
                                    <?php else: ?>
                                        <a href="<?= esc($item->external_url) ?>" class="btn btn-sm btn-success" title="Abrir Link" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i></a>
                                    <?php endif; ?>
                                    <form action="<?= site_url('admin/files/' . $item->id . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este item?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Aba Relatórios -->
        <div class="tab-pane fade content-constrained <?= $active_tab === 'reports' ? 'show active' : '' ?>" id="reports-tab-pane" role="tabpanel" aria-labelledby="reports-tab" tabindex="0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Relatórios de SEO Importados</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importReportModal"><i class="bi bi-cloud-download me-2"></i>Importar Relatório</button>
            </div>

            <?php if (empty($imported_reports)): ?>
                <div class="alert alert-info">Nenhum relatório foi importado para este projeto ainda.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>URL Analisada</th>
                                <th>Palavra-chave Foco</th>
                                <th>Tecnologias</th>
                                <th>Data da Análise</th>
                                <th>Importado por</th>
                                <th style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($imported_reports as $report): ?>
                            <tr>
                                <td class="align-middle"><strong><?= esc($report->url) ?></strong></td>
                                <td class="align-middle"><?= esc($report->target_keyword ?: 'N/A') ?></td>
                                <td class="align-middle"><?= esc($report->tech_stack ?: 'N/A') ?></td>
                                <td class="align-middle"><?= date('d/m/Y H:i', strtotime($report->original_created_at)) ?></td>
                                <td class="align-middle"><?= esc($report->importer_name) ?></td>
                                <td class="align-middle">
                                    <a href="<?= site_url('admin/reports/' . $report->id) ?>" class="btn btn-sm btn-primary" title="Visualizar"><i class="bi bi-eye"></i></a>
                                    <form action="<?= site_url('admin/reports/' . $report->id . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este relatório importado?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Aba Cronograma -->
        <div class="tab-pane fade content-constrained <?= $active_tab === 'timeline' ? 'show active' : '' ?>" id="timeline-tab-pane" role="tabpanel" aria-labelledby="timeline-tab" tabindex="0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Cronograma Semanal de Entregas</h2>
                <?php if (session()->get('is_admin')): ?>
                <div class="form-check form-switch fs-5">
                    <input class="form-check-input" type="checkbox" role="switch" id="projectVisibilityToggle" data-project-id="<?= $project->id ?>" <?= !isset($project->is_visible_to_client) || $project->is_visible_to_client ? 'checked' : '' ?>>
                    <label class="form-check-label" for="projectVisibilityToggle">Visível no Portal do Cliente</label>
                </div>
                <?php endif; ?>
            </div>
            <p class="text-muted">Visão geral das tarefas do projeto agrupadas por semana de entrega.</p>

            <?php if (empty($weekly_schedule)): ?>
                <div class="alert alert-info mt-4">Nenhuma tarefa com data de entrega para exibir no cronograma.</div>
            <?php else: ?>
                <?php foreach ($weekly_schedule as $month): ?>
                    <h3 class="month-header"><?= esc($month['label']) ?></h3>
                    <?php foreach ($month['weeks'] as $week_key => $week): ?>
                        <?php
                            $is_current_week = (isset($current_week_key) && $week_key === $current_week_key) ? 'current-week-highlight' : '';
                        ?>
                        <div class="card week-card <?= $is_current_week ?>">
                            <div class="card-header">
                                <strong><?= esc($week['label']) ?></strong>
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($week['items'] as $task): ?>
                                    <?php
                                        // Mapeamento de status para cores de badge
                                        $status_colors = [
                                            'concluída'         => 'bg-success',
                                            'cancelada'         => 'bg-danger',
                                            'em desenvovimento' => 'bg-primary',
                                            'ajustes'           => 'bg-warning text-dark',
                                            'aprovação'         => 'bg-info text-dark',
                                            'não iniciadas'     => 'bg-light text-dark',
                                            'default'           => 'bg-secondary'
                                        ];
                                        $status_class = $status_colors[$task->status] ?? $status_colors['default'];
                                    ?>
                                    <li class="list-group-item clickable-task" data-task-id="<?= $task->id ?>">
                                        <div class="task-info">
                                            <strong class="d-block"><?= esc($task->title) ?></strong>
                                            <small class="text-muted"><?= esc($task->description) ?></small>
                                        </div>
                                        <div class="task-meta">
                                            <span class="badge <?= $status_class ?>"><?= esc(ucfirst($task->status)) ?></span>
                                            <?php if (!empty($task->user_id) && isset($users_by_id[$task->user_id])): ?>
                                                <div title="Atribuído a <?= esc($users_by_id[$task->user_id]->name) ?>">
                                                    <?= user_icon($users_by_id[$task->user_id], 24) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- Aba Membros -->
        <div class="tab-pane fade content-constrained <?= $active_tab === 'members' ? 'show active' : '' ?>" id="members-tab-pane" role="tabpanel" aria-labelledby="members-tab" tabindex="0">
            <div class="row">
                <!-- Coluna de Membros Atuais -->
                <div class="col-md-7">
                    <h2>Membros do Projeto</h2>
                    <?php if (empty($assigned_users)): ?>
                        <p>Ainda não há membros neste projeto.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th style="width: 120px;">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assigned_users as $user): ?>
                                <tr>
                                    <td><?= esc($user->name) ?></td>
                                    <td><?= esc($user->email) ?></td>
                                    <td>
                                        <form action="<?= site_url('admin/projects/' . $project->id . '/users/' . $user->id . '/remove') ?>" method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este membro do projeto?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="POST"> <!-- Mantém a consistência -->
                                            <button type="submit" class="btn btn-sm btn-warning">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Coluna para Adicionar Membros -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Adicionar Membro</h5>
                            <form action="<?= site_url('admin/projects/' . $project->id . '/users') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Selecione um Usuário</label>
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">-- Disponíveis --</option>
                                        <?php foreach ($available_users as $user): ?>
                                            <option value="<?= $user->id ?>"><?= esc($user->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Adicionar ao Projeto</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('admin/projects/modal') ?>

<!-- Incluindo a biblioteca SortableJS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<!-- Incluindo o editor TinyMCE via CDN -->
<script src="https://cdn.tiny.cloud/1/q9ozbuo864vsu5yzdpyz9t3kpdfz4pqhum364ther9lt1iu8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
/**
 * Busca os dados de uma tarefa e abre o modal de edição.
 * @param {string} taskId O ID da tarefa.
 */
function openEditTaskModal(taskId) {
    const editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    const editTaskForm = document.getElementById('editTaskForm');

    fetch(`<?= site_url('admin/tasks/') ?>${taskId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const task = data.task;
            editTaskForm.action = `<?= site_url('admin/tasks/') ?>${task.id}/update`;
            document.getElementById('edit_title').value = task.title;
            document.getElementById('edit_description').value = task.description || '';
            document.getElementById('edit_status').value = task.status;
            document.getElementById('edit_user_id').value = task.user_id || '';
            document.getElementById('edit_due_date').value = task.due_date || '';
            editTaskModal.show();
        } else {
            showToast(data.message || 'Tarefa não encontrada.', 'danger');
        }
    }).catch(err => showToast('Erro ao buscar dados da tarefa.', 'danger'));
}

document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;
    const container = document.getElementById('kanban-container');
    const scrollLeftBtn = document.getElementById('kanban-scroll-left');
    const scrollRightBtn = document.getElementById('kanban-scroll-right');

    // --- Lógica para visibilidade das Ações do Quadro e Setas de Navegação ---
    const boardActionsGroup = document.getElementById('board-actions-group');
    const projectTabs = document.querySelectorAll('#projectTab .nav-link');
    const initialActiveTabId = document.querySelector('#projectTab .nav-link.active')?.id;

    function updateArrowVisibility() {
        if (container && scrollLeftBtn && scrollRightBtn) {
            // Um pequeno delay para garantir que o DOM está visível e as dimensões calculadas
            setTimeout(() => {
                if (container.offsetParent === null) return; // Não faz nada se o container estiver oculto
                scrollLeftBtn.style.display = container.scrollLeft <= 0 ? 'none' : 'flex';
                const maxScrollLeft = container.scrollWidth - container.clientWidth;
                scrollRightBtn.style.display = container.scrollLeft >= maxScrollLeft - 1 ? 'none' : 'flex';
            }, 50);
        }
    }

    function updateBoardContext(activeTabId) {
        if (boardActionsGroup) {
            const isBoardTab = activeTabId === 'board-tab';
            boardActionsGroup.style.display = isBoardTab ? 'inline-flex' : 'none';
            if (isBoardTab) {
                updateArrowVisibility();
            }
        }
    }

    // Define o estado inicial ao carregar a página
    if (initialActiveTabId) {
        updateBoardContext(initialActiveTabId);
    }

    // Adiciona os listeners para a troca de abas
    projectTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', event => updateBoardContext(event.target.id));
    });

    if (container && scrollLeftBtn && scrollRightBtn) {

        // --- Lógica para Largura das Colunas e Scroll ---
        const kanbanBoard = document.querySelector('.kanban-board');
        const columnWidthSelector = document.getElementById('column-width-selector');
        const layoutToggleSimplified = document.getElementById('layout-toggle-simplified');
        let scrollAmount = 316; // Default for compact

        const widthSettings = {
            compact: { width: 300, scroll: 316 }, // 300 + 16 (1rem gap)
            normal:  { width: 450, scroll: 466 }, // 450 + 16
            large:   { width: 600, scroll: 616 }  // 600 + 16
        };

        function applyColumnWidth(widthName = 'compact') {
            if (!kanbanBoard) return;

            Object.keys(widthSettings).forEach(w => kanbanBoard.classList.remove(`kanban-width-${w}`));
            kanbanBoard.classList.add(`kanban-width-${widthName}`);
            scrollAmount = widthSettings[widthName].scroll;
            updateArrowVisibility();
            localStorage.setItem('kanban_column_width', widthName);

            if (columnWidthSelector) {
                columnWidthSelector.querySelectorAll('.dropdown-item').forEach(item => {
                    item.classList.toggle('active', item.dataset.width === widthName);
                });
            }
        }

        if (columnWidthSelector) {
            columnWidthSelector.addEventListener('click', function(e) {
                e.preventDefault();
                const widthItem = e.target.closest('[data-width]');
                if (widthItem) {
                    applyColumnWidth(widthItem.dataset.width);
                }
            });
        }

        // --- Lógica para o Layout Simplificado ---
        function applyKanbanLayout(layoutName = 'normal') {
            if (!kanbanBoard) return;

            const isSimplified = layoutName === 'simplified';
            kanbanBoard.classList.toggle('kanban-layout-simplified', isSimplified);
            
            if (layoutToggleSimplified) {
                layoutToggleSimplified.classList.toggle('active', isSimplified);
                if (isSimplified) {
                    layoutToggleSimplified.innerHTML = '<i class="bi bi-check-lg me-2"></i>Layout Simplificado';
                } else {
                    layoutToggleSimplified.textContent = 'Layout Simplificado';
                }
            }
            updateArrowVisibility(); // Recalcula as setas
        }

        if (layoutToggleSimplified) {
            layoutToggleSimplified.addEventListener('click', function(e) {
                e.preventDefault();
                const currentLayout = kanbanBoard.classList.contains('kanban-layout-simplified') ? 'normal' : 'simplified';
                applyKanbanLayout(currentLayout);

                // Salva a preferência no banco
                fetch(`<?= site_url('admin/projects/' . $project->id . '/kanban-settings') ?>`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ layout: currentLayout })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Erro desconhecido ao salvar o layout.', 'danger');
                    }
                })
                .catch(error => {
                    showToast('Erro de comunicação com o servidor ao salvar o layout.', 'danger');
                    console.error('Erro ao salvar layout do Kanban:', error);
                });
            });
        }

        scrollLeftBtn.addEventListener('click', () => container.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
        scrollRightBtn.addEventListener('click', () => container.scrollBy({ left: scrollAmount, behavior: 'smooth' }));

        container.addEventListener('scroll', updateArrowVisibility);

        // Apply saved width on page load
        const savedWidth = localStorage.getItem('kanban_column_width') || 'compact';
        applyColumnWidth(savedWidth);
        
        // Apply saved layout on page load
        const savedLayout = '<?= esc($project->kanban_layout ?? 'normal', 'js') ?>';
        applyKanbanLayout(savedLayout);
    }

    // Lógica para o formulário de IA
    const aiForm = document.getElementById('aiTaskForm');
    const submitButton = document.getElementById('aiSubmitButton');
    const spinner = submitButton.querySelector('.spinner-border');
    const errorAlert = document.getElementById('ai-error-alert');

    const aiReviewModalEl = document.getElementById('aiReviewModal');
    const aiReviewModal = new bootstrap.Modal(aiReviewModalEl);
    const aiReviewList = document.getElementById('ai-review-list');
    const aiApproveButton = document.getElementById('aiApproveButton');

    const aiTaskModalEl = document.getElementById('aiTaskModal');
    if (aiTaskModalEl) {
        aiTaskModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Botão que acionou o modal
            if (!button) return;

            const typeId = button.dataset.typeId;
            
            // Preenche os campos do formulário com base nos dados do tipo de projeto
            document.getElementById('project_type_id').value = typeId;

            // Pré-preenche a descrição do projeto
            document.getElementById('project_description').value = button.dataset.projectDescription || '';

            // Atualiza os rótulos e placeholders
            const labelDesc = document.getElementById('ai-label-description');
            const labelItems = document.getElementById('ai-label-items');
            const placeholderDesc = document.getElementById('project_description');
            const placeholderItems = document.getElementById('project_pages');

            if(labelDesc) labelDesc.textContent = button.dataset.labelDescription || 'Descrição Detalhada';
            if(labelItems) labelItems.textContent = button.dataset.labelItems || 'Itens/Páginas';
        });
    }
    if (aiForm) {
        aiForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Mostra o spinner e desabilita o botão
            spinner.classList.remove('d-none');
            submitButton.disabled = true;
            errorAlert.classList.add('d-none');

            const formData = new FormData(aiForm);
            const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;

            fetch(aiForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => {
                // Verifica se a resposta foi bem-sucedida (status 2xx)
                if (!response.ok) {
                    // Se não for, lê a resposta como texto e lança um erro com o conteúdo
                    return response.text().then(text => { throw new Error(text || `Erro ${response.status}`) });
                }
                // Se foi bem-sucedida, processa como JSON
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // 1. Esconde o modal de geração
                    const generationModal = bootstrap.Modal.getInstance(document.getElementById('aiTaskModal'));
                    generationModal.hide();

                    // 2. Preenche o modal de revisão com as tarefas
                    aiReviewList.innerHTML = ''; // Limpa a lista anterior
                    if (data.tasks && data.tasks.length > 0) {
                        data.tasks.forEach(task => {
                            // Escapa aspas para usar em atributos HTML de forma segura
                            const safeTitle = task.title.replace(/"/g, '&quot;');
                            const safeDescription = (task.description || '').replace(/"/g, '&quot;');

                            const taskHtml = `
                                <div class="list-group-item ai-task-review-item" data-title="${safeTitle}" data-description="${safeDescription}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">${task.title}</h6>
                                            <p class="mb-1 small text-muted">${task.description || 'Sem descrição.'}</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2" style="min-width: 200px;">
                                            <input type="date" class="form-control form-control-sm ai-task-due-date" value="${task.due_date || ''}">
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-ai-task-btn" title="Remover esta tarefa">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>`;
                            aiReviewList.insertAdjacentHTML('beforeend', taskHtml);
                        });
                        aiReviewModal.show(); // 3. Mostra o modal de revisão
                    }
                } else {
                    errorAlert.textContent = data.message || 'Ocorreu um erro desconhecido.';
                    errorAlert.classList.remove('d-none');
                }
            })
            .catch(error => {
                // Exibe o erro capturado, que pode ser um erro de rede ou o texto da resposta de erro
                errorAlert.innerHTML = error.message; // Usamos innerHTML para renderizar o <pre> do debug
                errorAlert.classList.remove('d-none');
            })
            .finally(() => {
                // Esconde o spinner e reabilita o botão
                spinner.classList.add('d-none');
                submitButton.disabled = false;
            });
        });
    }

    // Delegação de evento para os botões de excluir no modal de revisão
    if (aiReviewList) {
        aiReviewList.addEventListener('click', function(e) {
            const deleteButton = e.target.closest('.delete-ai-task-btn');
            if (deleteButton) {
                deleteButton.closest('.ai-task-review-item').remove();
            }
        });
    }

    if (aiApproveButton) {
        aiApproveButton.addEventListener('click', function() {
            const approveSpinner = this.querySelector('.spinner-border');
            const reviewErrorAlert = document.getElementById('ai-review-error-alert');

            approveSpinner.classList.remove('d-none');
            this.disabled = true;
            reviewErrorAlert.classList.add('d-none');

            // Recupera as tarefas da lista de revisão, lendo os valores atuais
            const tasksToSave = Array.from(aiReviewList.querySelectorAll('.ai-task-review-item')).map(item => {
                return {
                    title: item.dataset.title,
                    description: item.dataset.description,
                    due_date: item.querySelector('.ai-task-due-date').value,
                    project_id: <?= $project->id ?>,
                    status: 'não iniciadas'
                };
            });

            const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;

            fetch(`<?= site_url('admin/projects/' . $project->id . '/tasks/save-ai') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ tasks: tasksToSave })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    reviewErrorAlert.textContent = data.message || 'Erro ao salvar as tarefas.';
                    reviewErrorAlert.classList.remove('d-none');
                }
            })
            .catch(error => {
                reviewErrorAlert.textContent = 'Erro de comunicação com o servidor.';
                reviewErrorAlert.classList.remove('d-none');
            })
            .finally(() => {
                approveSpinner.classList.add('d-none');
                this.disabled = false;
            });
        });
    }

    // --- Lógica para Editar e Remover Tarefas ---
    const kanbanContainer = document.getElementById('kanban-container');
    const editTaskModalEl = document.getElementById('editTaskModal');
    const editTaskModal = new bootstrap.Modal(editTaskModalEl);
    const editTaskForm = document.getElementById('editTaskForm');

    const deleteTaskModalEl = document.getElementById('deleteTaskModal');
    const deleteTaskModal = new bootstrap.Modal(deleteTaskModalEl);
    const deleteTaskForm = document.getElementById('deleteTaskForm');
    const deleteTaskText = document.getElementById('deleteTaskConfirmationText');

    if (kanbanContainer) {
        kanbanContainer.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-task-btn');
            const deleteBtn = e.target.closest('.delete-task-btn');
            const addNoteBtn = e.target.closest('.add-note-btn');
            const deleteNoteBtn = e.target.closest('.delete-note-btn');

            if (editBtn) {
                e.preventDefault();
                const taskId = editBtn.dataset.taskId;
                // Chama a função reutilizável para abrir o modal
                openEditTaskModal(taskId);
            }

            if (deleteBtn) {
                e.preventDefault();
                const taskId = deleteBtn.dataset.taskId;
                const taskTitle = deleteBtn.dataset.taskTitle;

                deleteTaskForm.action = `<?= site_url('admin/tasks/') ?>${taskId}/delete`;
                deleteTaskText.innerHTML = `Tem certeza que deseja remover a tarefa: <strong>"${taskTitle}"</strong>?`;
                
                deleteTaskModal.show();
            }

            if (addNoteBtn) {
                e.preventDefault();
                const taskId = addNoteBtn.dataset.taskId;
                
                // Configura o formulário do modal de nota
                addNoteForm.action = `<?= site_url('admin/tasks/') ?>${taskId}/notes`;
                noteTextarea.value = ''; // Limpa o campo
                charCounter.textContent = '0 / 180'; // Reseta o contador
                
                addNoteModal.show();
            }

            if (deleteNoteBtn) {
                e.preventDefault();
                const noteId = deleteNoteBtn.dataset.noteId;
                const noteText = deleteNoteBtn.dataset.noteText;

                deleteNoteForm.action = `<?= site_url('admin/notes/') ?>${noteId}/delete`;
                
                if (deleteNoteConfirmationText) {
                    deleteNoteConfirmationText.textContent = `"${noteText}"`;
                }
                
                deleteNoteModal.show();
            }
        });
    }

    // --- Lógica para Clicar nas Tarefas do Cronograma Semanal ---
    const timelineTabPane = document.getElementById('timeline-tab-pane');
    if (timelineTabPane) {
        timelineTabPane.addEventListener('click', function(e) {
            const taskItem = e.target.closest('.clickable-task');
            if (taskItem && taskItem.dataset.taskId) {
                openEditTaskModal(taskItem.dataset.taskId);
            }
        });
    }

    // --- Lógica para o Modal de Notas ---
    const addNoteModalEl = document.getElementById('addNoteModal');
    const addNoteModal = new bootstrap.Modal(addNoteModalEl);
    const addNoteForm = document.getElementById('addNoteForm');
    const noteTextarea = document.getElementById('note_text');
    const charCounter = document.getElementById('charCounter');

    const deleteNoteModalEl = document.getElementById('deleteNoteModal');
    const deleteNoteModal = new bootstrap.Modal(deleteNoteModalEl);
    const deleteNoteForm = document.getElementById('deleteNoteForm');
    const deleteNoteConfirmationText = document.getElementById('deleteNoteConfirmationText');

    if (noteTextarea) {
        noteTextarea.addEventListener('input', () => {
            const count = noteTextarea.value.length;
            charCounter.textContent = `${count} / 180`;
        });
    }

    // --- Lógica para Seleção Múltipla e Ações em Massa ---
    let isSelectionModeActive = false;
    let selectedTaskIds = [];
    let sortableInstances = [];

    const multiSelectBtn = document.getElementById('multiSelectBtn');
    const cancelSelectBtn = document.getElementById('cancelSelectBtn');
    const mainActions = document.getElementById('main-actions');
    const bulkActions = document.getElementById('bulk-actions');
    const selectionCount = document.getElementById('selectionCount');
    const bulkEditBtn = document.getElementById('bulkEditBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const kanbanBoardContainer = document.getElementById('kanban-container');

    const bulkEditModalEl = document.getElementById('bulkEditModal');
    const bulkEditModal = new bootstrap.Modal(bulkEditModalEl);
    const bulkEditForm = document.getElementById('bulkEditForm');

    function toggleSelectionMode(active) {
        isSelectionModeActive = active;
        mainActions.classList.toggle('d-none', active);
        bulkActions.classList.toggle('d-none', !active);
        kanbanBoardContainer.classList.toggle('selection-mode-active', active);

        // Desabilita/Habilita o drag-and-drop
        sortableInstances.forEach(sortable => sortable.option('disabled', active));

        if (!active) {
            // Limpa a seleção ao sair do modo
            document.querySelectorAll('.kanban-card.selected').forEach(card => card.classList.remove('selected'));
            selectedTaskIds = [];
            updateBulkActionBar();
        }
    }

    function updateBulkActionBar() {
        const count = selectedTaskIds.length;
        selectionCount.textContent = `${count} tarefa${count !== 1 ? 's' : ''} selecionada${count !== 1 ? 's' : ''}`;
        bulkEditBtn.disabled = count === 0;
        bulkDeleteBtn.disabled = count === 0;
    }

    multiSelectBtn.addEventListener('click', () => toggleSelectionMode(true));
    cancelSelectBtn.addEventListener('click', () => toggleSelectionMode(false));

    kanbanBoardContainer.addEventListener('click', (e) => {
        if (!isSelectionModeActive) return;

        const card = e.target.closest('.kanban-card');
        if (card) {
            e.preventDefault(); // Previne qualquer outra ação, como drag
            const taskId = card.dataset.taskId;
            card.classList.toggle('selected');

            if (selectedTaskIds.includes(taskId)) {
                selectedTaskIds = selectedTaskIds.filter(id => id !== taskId);
            } else {
                selectedTaskIds.push(taskId);
            }
            updateBulkActionBar();
        }
    });

    // Ação de Remover em Massa
    bulkDeleteBtn.addEventListener('click', () => {
        if (selectedTaskIds.length === 0) return;

        deleteTaskText.innerHTML = `Tem certeza que deseja remover as <strong>${selectedTaskIds.length} tarefas</strong> selecionadas? Esta ação não pode ser desfeita.`;
        
        // Configura o formulário para enviar os IDs
        deleteTaskForm.action = `<?= site_url('admin/tasks/bulk-delete') ?>`;
        // Limpa inputs antigos
        deleteTaskForm.querySelectorAll('input[name="task_ids[]"]').forEach(input => input.remove());
        // Adiciona os novos
        selectedTaskIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'task_ids[]';
            input.value = id;
            deleteTaskForm.appendChild(input);
        });

        deleteTaskModal.show();
    });

    // Ação de Editar em Massa
    bulkEditBtn.addEventListener('click', () => {
        if (selectedTaskIds.length > 0) {
            bulkEditModal.show();
        }
    });

    bulkEditForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(bulkEditForm);
        const data = {
            task_ids: selectedTaskIds,
            user_id: formData.get('user_id'),
            due_date: formData.get('due_date')
        };

        fetch(bulkEditForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                showToast(result.message || 'Erro ao atualizar tarefas.', 'danger');
            }
        })
        .catch(err => showToast('Erro de comunicação.', 'danger'));
    });

    /**
     * Exibe uma notificação (toast) na tela.
     * @param {string} message A mensagem a ser exibida.
     * @param {string} type 'success' (verde) ou 'danger' (vermelho).
     */
    function showToast(message, type = 'success') { 
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) return;

        const toastId = 'toast-dynamic-' + Date.now();
        const bgClass = type === 'danger' ? 'text-bg-danger' : 'text-bg-success';

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Insere a nova notificação no contêiner existente
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId), { delay: 3500 }); // Usa o mesmo delay do footer
        toast.show();
    }

    // --- Lógica do Drag and Drop com SortableJS ---
    const kanbanColumns = document.querySelectorAll('.kanban-cards');

    kanbanColumns.forEach(column => {
        const sortable = new Sortable(column, {
            group: 'kanban', // Permite arrastar entre colunas com o mesmo grupo
            animation: 150,
            ghostClass: 'bg-info-subtle', // Classe para o "fantasma" do card

            // Evento disparado ao soltar um card
            onEnd: function (evt) {
                const taskId = evt.item.getAttribute('data-task-id');
                const newStatus = evt.to.getAttribute('data-status');
                
                // Pega a nova ordem dos cards na coluna de destino
                const taskIdsInColumn = Array.from(evt.to.children).map(card => card.getAttribute('data-task-id'));

                // Prepara os dados para enviar ao backend
                const data = {
                    taskId: taskId,
                    newStatus: newStatus,
                    order: taskIdsInColumn
                };

                // Envia a atualização para o servidor
                fetch(`<?= site_url('admin/tasks/update-board') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        showToast('Erro de comunicação. A página será recarregada para garantir a consistência.', 'danger');
                        setTimeout(() => location.reload(), 3000);
                        throw new Error(`Server responded with status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Ocorreu um erro ao salvar. A página será recarregada.', 'danger');
                        setTimeout(() => location.reload(), 3000);
                    }
                })
                .catch(error => {
                    console.error('Erro no Drag-and-Drop:', error);
                });
            }
        });
        sortableInstances.push(sortable); // Guarda a instância para poder desabilitá-la
    });

    // --- Lógica para a Aba de Documentos ---
    const projectId = <?= $project->id ?>;

    // Elementos da UI
    const documentList = document.getElementById('document-list');
    const documentTitle = document.getElementById('document-title');
    const documentContent = document.getElementById('document-content');
    const documentActions = document.getElementById('document-actions');
    const addDocumentBtn = document.getElementById('addDocumentBtn');
    const editDocumentBtn = document.getElementById('editDocumentBtn');
    const deleteDocumentBtn = document.getElementById('deleteDocumentBtn');

    // Elementos do Modal
    const docEditModalEl = document.getElementById('documentEditModal');
    const docEditModal = new bootstrap.Modal(docEditModalEl);
    const docEditForm = document.getElementById('documentEditForm');
    const docEditModalLabel = document.getElementById('documentEditModalLabel');
    const docIdInput = document.getElementById('document_id');
    const docTitleInput = document.getElementById('document_edit_title');
    const docErrorAlert = document.getElementById('document-error-alert');
    const saveDocBtn = document.getElementById('saveDocumentBtn');

    let currentDocId = null;
    const docToSelect = <?= json_encode($select_doc_id ?? null) ?>;

    // Inicializa o editor TinyMCE
    tinymce.init({
        selector: '#document_edit_content',
        plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
        menubar: 'file edit view insert format tools table help',
        toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
        height: '500px',
    });

    // Função para carregar um documento
    function loadDocument(docId) {
        fetch(`<?= site_url('documents/') ?>${docId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentDocId = data.document.id;
                documentTitle.textContent = data.document.title;
                documentContent.innerHTML = data.document.content || '<p class="text-muted">Este documento ainda não tem conteúdo.</p>';
                documentActions.classList.remove('d-none');

                // Ativa o item selecionado na lista
                document.querySelectorAll('#document-list .list-group-item-action').forEach(item => {
                    item.classList.toggle('active', item.dataset.docId == docId);
                });
            } else {
                showToast(data.message || 'Erro ao carregar documento.', 'danger');
            }
        });
    }

    // Evento de clique na lista de documentos
    documentList.addEventListener('click', function(e) {
        e.preventDefault();
        const link = e.target.closest('.list-group-item-action');
        if (link) {
            loadDocument(link.dataset.docId);
        }
    });

    // Abrir modal para ADICIONAR
    addDocumentBtn.addEventListener('click', function() {
        docEditForm.reset();
        docIdInput.value = '';
        tinymce.get('document_edit_content').setContent('');
        docEditModalLabel.textContent = 'Criar Nova Página';
        docErrorAlert.classList.add('d-none');
        docEditModal.show();
    });

    // Abrir modal para EDITAR
    editDocumentBtn.addEventListener('click', function() {
        if (!currentDocId) return;
        fetch(`<?= site_url('documents/') ?>${currentDocId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                docIdInput.value = data.document.id;
                docTitleInput.value = data.document.title;
                tinymce.get('document_edit_content').setContent(data.document.content || '');
                docEditModalLabel.textContent = 'Editar Documento';
                docErrorAlert.classList.add('d-none');
                docEditModal.show();
            }
        });
    });

    // Submeter formulário de criação/edição
    docEditForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const docId = docIdInput.value;
        const isCreating = !docId;
        const url = isCreating ? `<?= site_url('projects/') ?>${projectId}/documents` : `<?= site_url('documents/') ?>${docId}/update`;

        const data = {
            title: docTitleInput.value,
            content: tinymce.get('document_edit_content').getContent()
        };

        saveDocBtn.disabled = true;
        saveDocBtn.querySelector('.spinner-border').classList.remove('d-none');

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                const savedDocId = result.new_id || result.id;
                const redirectUrl = `<?= site_url('admin/projects/' . $project->id) ?>?active_tab=documents&select_doc=${savedDocId}`;
                window.location.href = redirectUrl;
            } else {
                const errorMsg = Object.values(result.errors || {gen: 'Ocorreu um erro.'}).join('<br>');
                docErrorAlert.innerHTML = errorMsg;
                docErrorAlert.classList.remove('d-none');
            }
        }).finally(() => {
            saveDocBtn.disabled = false;
            saveDocBtn.querySelector('.spinner-border').classList.add('d-none');
        });
    });

    // Remover documento
    deleteDocumentBtn.addEventListener('click', function() {
        if (!currentDocId) return;
        if (confirm('Tem certeza que deseja remover este documento?')) {
            fetch(`<?= site_url('documents/') ?>${currentDocId}/delete`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.message || 'Erro ao remover.', 'danger');
                }
            });
        }
    });

    // Ao carregar a página, se houver um documento para selecionar, carrega-o.
    if (docToSelect) {
        loadDocument(docToSelect);
    }

    // --- Lógica para Reordenar Documentos ---
    if (documentList) {
        new Sortable(documentList, {
            animation: 150,
            ghostClass: 'bg-info-subtle',
            handle: '.list-group-item-action', // Define o handle para o próprio item
            onEnd: function (evt) {
                const docIds = Array.from(evt.to.children).map(item => item.dataset.docId);
                
                fetch(`<?= site_url('admin/projects/') ?>${projectId}/documents/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken // csrfToken is already defined in this file
                    },
                    body: JSON.stringify({ doc_ids: docIds })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Erro ao salvar a ordem.', 'danger');
                    }
                })
                .catch(err => showToast('Erro de comunicação ao reordenar.', 'danger'));
            }
        });
    }

    // --- Lógica para o Modal de Importação de Relatórios ---
    const importReportModalEl = document.getElementById('importReportModal');
    if (importReportModalEl) {
        const reportsContainer = document.getElementById('available-reports-container');
        const submitBtn = document.getElementById('importReportSubmitBtn');
        const errorAlert = document.getElementById('import-error-alert');

        importReportModalEl.addEventListener('show.bs.modal', function() {
            // Reseta o estado
            reportsContainer.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>`;
            submitBtn.disabled = true;
            errorAlert.classList.add('d-none');

            // Busca os relatórios disponíveis
            fetch(`<?= site_url('admin/reports/available/' . $project->id) ?>`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    reportsContainer.innerHTML = ''; // Limpa o spinner
                    if (data.reports.length > 0) {
                        data.reports.forEach(report => {
                            const date = new Date(report.created_at).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                            const reportHtml = `
                                <label class="list-group-item d-flex gap-3">
                                    <input class="form-check-input flex-shrink-0" type="radio" name="report_id" value="${report.id}">
                                    <span class="pt-1 form-checked-content">
                                        <strong>${report.url}</strong>
                                        <small class="d-block text-muted">
                                            Palavra-chave: ${report.target_keyword || 'Nenhuma'} | Criado em: ${date}
                                        </small>
                                    </span>
                                </label>
                            `;
                            reportsContainer.insertAdjacentHTML('beforeend', reportHtml);
                        });
                    } else {
                        reportsContainer.innerHTML = '<div class="alert alert-info mb-0">Nenhum novo relatório disponível para importação.</div>';
                    }
                } else {
                    errorAlert.textContent = data.message || 'Erro ao buscar relatórios.';
                    errorAlert.classList.remove('d-none');
                    reportsContainer.innerHTML = '';
                }
            });
        });

        // Habilita o botão de submit quando um rádio é selecionado
        reportsContainer.addEventListener('change', function(e) {
            if (e.target.type === 'radio' && e.target.name === 'report_id') {
                submitBtn.disabled = false;
            }
        });
    }

    // --- Lógica para o Gráfico de Gantt (Tooltips) ---
    const ganttTooltips = document.querySelectorAll('#timeline-tab-pane [data-bs-toggle="tooltip"]');
    if (ganttTooltips.length > 0) {
        [...ganttTooltips].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // --- Lógica para o Toggle de Visibilidade no Portal do Cliente ---
    const visibilityToggle = document.getElementById('projectVisibilityToggle');
    if (visibilityToggle) {
        visibilityToggle.addEventListener('change', function() {
            const projectId = this.dataset.projectId;
            const isVisible = this.checked;

            fetch(`<?= site_url('admin/projects/') ?>${projectId}/toggle-client-visibility`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const message = data.new_visibility ? 'Projeto agora está visível no portal do cliente.' : 'Projeto agora está oculto no portal do cliente.';
                    showToast(message, 'success');
                } else {
                    this.checked = !isVisible; // Reverte em caso de erro
                    showToast(data.message || 'Erro ao alterar a visibilidade.', 'danger');
                }
            }).catch(err => {
                this.checked = !isVisible;
                showToast('Erro de comunicação. Tente novamente.', 'danger');
            });
        });
    }

    // --- Lógica para o Toggle de Visibilidade de Arquivos ---
    const fileVisibilityToggles = document.querySelectorAll('.file-visibility-toggle');
    fileVisibilityToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const fileId = this.dataset.fileId;
            const isVisible = this.checked;

            fetch(`<?= site_url('admin/files/') ?>${fileId}/toggle-client-visibility`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const message = data.new_visibility ? 'Arquivo agora está visível para o cliente.' : 'Arquivo agora está oculto para o cliente.';
                    showToast(message, 'success');
                } else {
                    this.checked = !isVisible; // Reverte em caso de erro
                    showToast(data.message || 'Erro ao alterar a visibilidade do arquivo.', 'danger');
                }
            }).catch(err => {
                this.checked = !isVisible;
                showToast('Erro de comunicação. Tente novamente.', 'danger');
            });
        });
    });
});
</script>

<?= $this->include('partials/footer') ?>