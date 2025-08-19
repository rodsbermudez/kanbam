<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container-fluid mt-6 px-4">

    <!-- Cabeçalho e Ações -->
    <div class="content-constrained">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0"><?= esc($project->name) ?></h1>
                <p class="text-muted"><?= esc($project->description) ?></p>
            </div>
            <?php if (session()->get('is_admin')): ?>
            <div id="main-actions" class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Ações do Quadro -->
                <div id="board-actions-group" class="btn-group" role="group" aria-label="Ações do Quadro">
                    <button type="button" class="btn btn-secondary" id="multiSelectBtn"><i class="bi bi-check2-square"></i> Selecionar Múltiplos</button>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Largura das Colunas">
                            <i class="bi bi-columns-gap"></i>
                        </button>
                        <ul class="dropdown-menu" id="column-width-selector">
                            <li><a class="dropdown-item" href="#" data-width="compact">Compacta</a></li>
                            <li><a class="dropdown-item" href="#" data-width="normal">Normal</a></li>
                            <li><a class="dropdown-item" href="#" data-width="large">Grande</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Ações de Tarefas -->
                <div class="btn-group" role="group" aria-label="Ações de Tarefas">
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
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal"><i class="bi bi-plus-lg"></i> Nova Tarefa</button>
                </div>
                <!-- Ações do Projeto -->
                <div class="btn-group" role="group" aria-label="Ações do Projeto">
                    <a href="<?= site_url('admin/projects/' . $project->id . '/edit') ?>" class="btn btn-primary">Editar Projeto</a>
                    <a href="<?= site_url('admin/projects/' . $project->id . '/delete') ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja remover este projeto?')">Remover Projeto</a>
                </div>
            </div>
            <div id="bulk-actions" class="d-none align-items-center gap-2">
                <strong id="selectionCount">0 tarefas selecionadas</strong>
                <button type="button" class="btn btn-primary" id="bulkEditBtn" disabled><i class="bi bi-pencil-square"></i> Editar Selecionadas</button>
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled><i class="bi bi-trash"></i> Remover Selecionadas</button>
                <button type="button" class="btn btn-secondary" id="cancelSelectBtn">Cancelar</button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .kanban-wrapper {
            position: relative;
        }
        .kanban-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background-color: rgba(var(--bs-dark-rgb), 0.7);
            border: 1px solid rgba(var(--bs-light-rgb), 0.3);
            color: var(--bs-light);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kanban-nav-btn.kanban-nav-left { left: -20px; }
        .kanban-nav-btn.kanban-nav-right { right: -20px; }

        .kanban-board-container {
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 1rem; /* Espaço para a barra de rolagem */
        }
        .kanban-board {
            display: inline-flex;
            gap: 1rem; /* Espaçamento entre as colunas */
            min-width: 100%;
        }
        .kanban-column {
            min-height: 50vh; /* Define a altura mínima da coluna */
            background-color: var(--bs-dark);
            border-radius: 0.5rem;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            white-space: normal; /* Permite que o texto quebre a linha dentro da coluna */
        }
        .kanban-board.kanban-width-compact .kanban-column {
            flex: 0 0 300px;
            max-width: 300px;
        }
        .kanban-board.kanban-width-normal .kanban-column {
            flex: 0 0 450px;
            max-width: 450px;
        }
        .kanban-board.kanban-width-large .kanban-column {
            flex: 0 0 600px;
            max-width: 600px;
        }
        .kanban-column-title {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            color: var(--bs-light);
        }
        .kanban-cards {
            flex-grow: 1;
            min-height: 100px; /* Altura mínima para a área de drop */
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .kanban-card {
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
            padding: 1rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .selection-mode-active .kanban-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .kanban-card.selected {
            border: 2px solid var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.25);
            cursor: grab;
        }
        .kanban-card .card-title {
            margin-bottom: 0.5rem;
        }
        .kanban-card .card-text {
            font-size: 0.875rem;
            color: var(--bs-secondary-color);
        }
        .kanban-card-footer {
            font-size: 0.8rem;
            margin-top: 1rem;
        }
        /* Define as cores de fundo para os cards de status */
        .kanban-card.card-warning {
            background-color: var(--bs-warning);
        }
        .kanban-card.card-danger {
            background-color: var(--bs-danger);
        }
        .kanban-card.card-info {
            background-color: var(--bs-info);
        }

        /* Para todos os cards coloridos, a cor do texto principal é branca */
        .kanban-card.card-warning,
        .kanban-card.card-danger,
        .kanbancard.card-info {
            color: #fff;
        }

        /* Garante que todo o texto dentro de cards coloridos, incluindo a data (text-muted), seja branco */
        .kanban-card:not(.bg-light) .card-text,
        .kanban-card:not(.bg-light) .text-muted,
        .kanban-card:not(.bg-light) .card-title {
            color: #fff !important;
        }
        .kanban-card.bg-light .card-text,
        .kanban-card.bg-light .text-muted,
        .kanban-card.bg-light .card-title {
            color: var(--bs-dark) !important; /* Garante que o texto seja escuro no fundo claro */
        }
        .kanban-card .btn-icon {
            padding: 0.1rem 0.4rem;
            line-height: 1;
            color: inherit;
            background: transparent;
            border: none;
        }
        .kanban-card .dropdown-menu {
            z-index: 1050;
        }
        /* Adiciona a borda branca ao ícone do usuário */
        .kanban-card-footer .user-icon-circle {
            border: 1px solid #fff;
            box-sizing: border-box; /* Garante que a borda não aumente o tamanho do ícone */
        }
        .kanban-card-notes {
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.2); /* Padrão para cards coloridos */
        }
        .kanban-card.bg-light .kanban-card-notes {
            border-top-color: var(--bs-border-color); /* Borda para card claro */
        }
        .note-item {
            font-size: 0.8rem;
        }
        .note-item + .note-item {
            margin-top: 0.5rem;
        }
        .note-text {
            flex-grow: 1;
            background-color: rgba(255,255,255,0.1); /* Padrão para cards coloridos */
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            color: #fff;
            word-break: break-word;
        }
        .kanban-card.bg-light .note-text {
            background-color: rgba(0,0,0,0.05); /* Fundo para card claro */
            color: var(--bs-dark);
        }
        .note-meta {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.7); /* Padrão para cards coloridos */
        }
        .kanban-card.bg-light .note-meta {
            color: var(--bs-secondary-color);
        }
        .note-meta a.delete-note-btn {
            color: var(--bs-danger);
            text-decoration: none;
            font-size: 0.65rem;
            font-weight: 500;
        }
        .note-meta a.delete-note-btn:hover {
            text-decoration: underline;
        }
        .kanban-card:not(.bg-light) .note-meta a.delete-note-btn {
            color: #fff;
            opacity: 0.8;
        }
        .kanban-card:not(.bg-light) .note-meta a.delete-note-btn:hover {
            opacity: 1;
        }

        /* Limita a largura do conteúdo e o centraliza */
        .content-constrained {
            max-width: 1500px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>

    <!-- Abas de Navegação -->
    <ul class="nav nav-tabs mb-4 justify-content-center" id="projectTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'board' ? 'active' : '' ?>" id="board-tab" data-bs-toggle="tab" data-bs-target="#board-tab-pane" type="button" role="tab" aria-controls="board-tab-pane" aria-selected="<?= $active_tab === 'board' ? 'true' : 'false' ?>">Quadro</button>
        </li>
        <?php if (session()->get('is_admin')): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'members' ? 'active' : '' ?>" id="members-tab" data-bs-toggle="tab" data-bs-target="#members-tab-pane" type="button" role="tab" aria-controls="members-tab-pane" aria-selected="<?= $active_tab === 'members' ? 'true' : 'false' ?>">Membros</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab === 'documents' ? 'active' : '' ?>" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button" role="tab" aria-controls="documents-tab-pane" aria-selected="<?= $active_tab === 'documents' ? 'true' : 'false' ?>">Documentos</button>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Conteúdo das Abas -->
    <div class="tab-content" id="projectTabContent">
        <!-- Aba Quadro Kanban -->
        <div class="tab-pane fade <?= $active_tab === 'board' ? 'show active' : '' ?>" id="board-tab-pane" role="tabpanel" aria-labelledby="board-tab" tabindex="0">
            <div class="kanban-wrapper">
                <button id="kanban-scroll-left" class="btn kanban-nav-btn kanban-nav-left"><i class="bi bi-arrow-left-circle-fill fs-4"></i></button>
                
                <div class="kanban-board-container" id="kanban-container">
                    <div class="kanban-board">
                        <?php foreach ($statuses as $status): ?>
                            <div class="kanban-column">
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
                                                    <p class="card-text mt-2"><?= esc(character_limiter($task->description, 80)) ?></p>
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
                                                    
                                                    <small class="text-muted">
                                                        <?php if (!empty($task->due_date)): ?>
                                                            <?= date('d/m/Y', strtotime($task->due_date)) ?>
                                                        <?php endif; ?>
                                                    </small>
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

                <button id="kanban-scroll-right" class="btn kanban-nav-btn kanban-nav-right"><i class="bi bi-arrow-right-circle-fill fs-4"></i></button>
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

        <!-- Aba Membros -->
        <?php if (session()->get('is_admin')): ?>
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
        <?php endif; ?>
    </div>
</main>

<!-- Modal Adicionar Tarefa -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addTaskModalLabel">Criar Nova Tarefa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/projects/' . $project->id . '/tasks') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título da Tarefa</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Inicial</label>
                        <select name="status" id="status" class="form-select">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= esc($status) ?>"><?= esc(ucfirst($status)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="user_id" class="form-label">Atribuir a</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">-- Não atribuído --</option>
                            <?php foreach ($assigned_users as $user): ?>
                                <option value="<?= $user->id ?>"><?= esc($user->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Data de Entrega</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Tarefa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Gerar Tarefas com IA -->
<div class="modal fade" id="aiTaskModal" tabindex="-1" aria-labelledby="aiTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="aiTaskModalLabel">Gerar Tarefas com Inteligência Artificial</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="aiTaskForm" action="<?= site_url('admin/projects/' . $project->id . '/tasks/generate-ai') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" id="project_type_id" name="project_type_id" value="">

                    <p class="text-muted">Descreva o projeto em detalhes. A IA irá sugerir os cards do Kanban com base nas informações fornecidas.</p>
                    
                    <div class="mb-3">
                        <label for="project_description" class="form-label" id="ai-label-description">Descrição Detalhada do Projeto</label>
                        <textarea class="form-control" id="project_description" name="project_description" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="project_pages" class="form-label" id="ai-label-items">Páginas a Serem Criadas (uma por linha)</label>
                        <textarea class="form-control" id="project_pages" name="project_pages" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="project_deadline" class="form-label">Prazo Final do Projeto</label>
                        <input type="date" class="form-control" id="project_deadline" name="project_deadline" required>
                    </div>

                    <div id="ai-error-alert" class="alert alert-danger d-none" role="alert"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="aiSubmitButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Gerar Tarefas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Revisão de Tarefas da IA -->
<div class="modal fade" id="aiReviewModal" tabindex="-1" aria-labelledby="aiReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="aiReviewModalLabel">Revisar Tarefas Sugeridas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Abaixo estão as tarefas sugeridas pela IA. Revise e aprove para adicioná-las ao projeto.</p>
                <div id="ai-review-list" class="list-group">
                    <!-- As tarefas serão inseridas aqui pelo JavaScript -->
                </div>
                <div id="ai-review-error-alert" class="alert alert-danger d-none mt-3" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="aiApproveButton">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Aprovar e Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Tarefa -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editTaskModalLabel">Editar Tarefa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTaskForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Título da Tarefa</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= esc($status) ?>"><?= esc(ucfirst($status)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label">Atribuir a</label>
                        <select name="user_id" id="edit_user_id" class="form-select">
                            <option value="">-- Não atribuído --</option>
                            <?php foreach ($assigned_users as $user): ?>
                                <option value="<?= $user->id ?>"><?= esc($user->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label">Data de Entrega</label>
                        <input type="date" class="form-control" id="edit_due_date" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Remoção de Tarefa -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteTaskModalLabel">Confirmar Remoção</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteTaskConfirmationText">Tem certeza que deseja remover esta tarefa?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteTaskForm" action="" method="post">
                     <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Remover</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Remoção de Nota -->
<div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteNoteModalLabel">Confirmar Remoção</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover a seguinte nota?</p>
                <blockquote class="blockquote-footer" id="deleteNoteConfirmationText">
                    <!-- O texto da nota virá aqui via JS -->
                </blockquote>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteNoteForm" action="" method="post">
                     <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Remover</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edição em Massa de Tarefas -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="bulkEditModalLabel">Editar Tarefas Selecionadas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkEditForm" action="<?= site_url('admin/tasks/bulk-update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>As alterações abaixo serão aplicadas a todas as tarefas selecionadas.</p>
                    <div class="mb-3">
                        <label for="bulk_user_id" class="form-label">Atribuir a</label>
                        <select name="user_id" id="bulk_user_id" class="form-select">
                            <option value="">-- Manter o atual --</option>
                            <option value="0">-- Remover atribuição --</option>
                            <?php foreach ($assigned_users as $user): ?>
                                <option value="<?= $user->id ?>"><?= esc($user->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="bulk_due_date" class="form-label">Data de Entrega</label>
                        <input type="date" class="form-control" id="bulk_due_date" name="due_date">
                        <small class="form-text text-muted">Deixe em branco para não alterar a data.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Criar Nota -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addNoteModalLabel">Adicionar Nota à Tarefa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNoteForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="note_text" class="form-label">Observação (máx. 180 caracteres)</label>
                        <textarea class="form-control" id="note_text" name="note" rows="4" maxlength="180" required></textarea>
                        <div id="charCounter" class="form-text text-end">0 / 180</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar/Criar Documento -->
<div class="modal fade" id="documentEditModal" tabindex="-1" aria-labelledby="documentEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="documentEditModalLabel">Editar Documento</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="documentEditForm" action="#" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" id="document_id" name="document_id">
                    <div class="mb-3">
                        <label for="document_edit_title" class="form-label">Título da Página</label>
                        <input type="text" class="form-control" id="document_edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="document_edit_content" class="form-label">Conteúdo</label>
                        <textarea id="document_edit_content" name="content"></textarea>
                    </div>
                    <div id="document-error-alert" class="alert alert-danger d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveDocumentBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Salvar Documento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Incluindo a biblioteca SortableJS via CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<!-- Incluindo o editor TinyMCE via CDN -->
<script src="https://cdn.tiny.cloud/1/q9ozbuo864vsu5yzdpyz9t3kpdfz4pqhum364ther9lt1iu8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

        scrollLeftBtn.addEventListener('click', () => container.scrollBy({ left: -scrollAmount, behavior: 'smooth' }));
        scrollRightBtn.addEventListener('click', () => container.scrollBy({ left: scrollAmount, behavior: 'smooth' }));

        container.addEventListener('scroll', updateArrowVisibility);

        // Apply saved width on page load
        const savedWidth = localStorage.getItem('kanban_column_width') || 'compact';
        applyColumnWidth(savedWidth);
    }

    // --- Lógica para o Modal de Geração com IA ---
    const aiTaskModalEl = document.getElementById('aiTaskModal');
    if (aiTaskModalEl) {
        aiTaskModalEl.addEventListener('show.bs.modal', function (event) {
            // Botão que acionou o modal
            const button = event.relatedTarget;

            // Extrai as informações dos atributos data-*
            const typeId = button.getAttribute('data-type-id');
            const labelDesc = button.getAttribute('data-label-description');
            const placeholderDesc = button.getAttribute('data-placeholder-description');
            const labelItems = button.getAttribute('data-label-items');
            const placeholderItems = button.getAttribute('data-placeholder-items');

            // Atualiza o conteúdo do modal
            const modal = this;
            modal.querySelector('#project_type_id').value = typeId;
            
            const labelDescEl = modal.querySelector('#ai-label-description');
            if (labelDescEl) labelDescEl.textContent = labelDesc || 'Descrição Detalhada do Projeto';
            
            const inputDescEl = modal.querySelector('#project_description');
            if (inputDescEl) inputDescEl.placeholder = placeholderDesc || '';

            const labelItemsEl = modal.querySelector('#ai-label-items');
            if (labelItemsEl) labelItemsEl.textContent = labelItems || 'Itens/Páginas a Serem Criados (um por linha)';

            const inputItemsEl = modal.querySelector('#project_pages');
            if (inputItemsEl) inputItemsEl.placeholder = placeholderItems || '';
        });
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
            .then(response => response.json())
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
                errorAlert.textContent = 'Erro de comunicação com o servidor. Tente novamente.';
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
                
                // Fetch task data
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
                        // Popula o modal de edição
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
                })
                .catch(err => {
                    console.error('Fetch Error:', err);
                    showToast('Erro ao buscar dados da tarefa.', 'danger');
                });
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
    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;

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
});

// --- Lógica para a Aba de Documentos ---
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;
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
});
</script>

<?= $this->include('partials/footer') ?>