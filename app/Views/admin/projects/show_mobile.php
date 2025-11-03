<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<style>
    .task-card-mobile {
        border-left-width: 4px;
    }
    .task-card-mobile.card-danger { border-left-color: var(--bs-danger); }
    .task-card-mobile.card-warning { border-left-color: var(--bs-warning); }
    .task-card-mobile.card-info { border-left-color: var(--bs-info); }
</style>

<main class="container mt-6">
    <!-- Cabeçalho do Projeto -->
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h2 mb-0"><?= esc($project->name) ?></h1>
                <?php if (isset($project->status) && $project->status === 'concluded'): ?>
                    <span class="badge bg-success">Concluído</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($project->client_tag)): ?>
                <span class="badge fs-6" style="background-color: <?= esc($project->client_color ?? '#6c757d') ?>;"><?= esc($project->client_tag) ?></span>
            <?php endif; ?>
        </div>

        <?php if (session()->get('is_admin')): ?>
        <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear-fill"></i> Ações
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTaskModal"><i class="bi bi-plus-lg me-2"></i>Nova Tarefa</a></li>
                <li><hr class="dropdown-divider"></li>
                <li class="dropdown-submenu">
                     <a class="dropdown-item dropdown-toggle" href="#"><i class="bi bi-magic me-2"></i>Gerar com IA</a>
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
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= site_url('admin/projects/' . $project->id . '/edit') ?>"><i class="bi bi-pencil-square me-2"></i>Editar Projeto</a></li>
                <li>
                    <?php
                        $isActive = isset($project->status) && $project->status === 'active';
                        $toggleText = $isActive ? 'Concluir Projeto' : 'Reativar Projeto';
                        $toggleClass = $isActive ? '' : 'text-success fw-bold';
                        $toggleIcon = $isActive ? 'bi-check-circle' : 'bi-arrow-clockwise';
                    ?>
                    <form action="<?= site_url('admin/projects/' . $project->id . '/toggle-status') ?>" method="post" id="toggleStatusForm" class="d-none"><?= csrf_field() ?></form>
                    <a class="dropdown-item <?= $toggleClass ?>" href="#" onclick="event.preventDefault(); document.getElementById('toggleStatusForm').submit();">
                        <i class="bi <?= $toggleIcon ?> me-2"></i><?= $toggleText ?>
                    </a>
                </li>
                <li><a class="dropdown-item text-danger" href="<?= site_url('admin/projects/' . $project->id . '/delete') ?>" onclick="return confirm('Tem certeza que deseja remover este projeto?');"><i class="bi bi-trash me-2"></i>Remover Projeto</a></li>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- Navegação por Abas (Dropdown) -->
    <div class="mb-4">
        <select class="form-select" id="mobileTabSelector">
            <option value="board" <?= $active_tab === 'board' ? 'selected' : '' ?>>Quadro</option>
            <?php if (session()->get('is_admin')): ?>
            <option value="members" <?= $active_tab === 'members' ? 'selected' : '' ?>>Membros</option>
            <option value="documents" <?= $active_tab === 'documents' ? 'selected' : '' ?>>Documentos</option>
            <option value="files" <?= $active_tab === 'files' ? 'selected' : '' ?>>Arquivos</option>
            <option value="reports" <?= $active_tab === 'reports' ? 'selected' : '' ?>>Relatórios</option>
            <option value="timeline" <?= $active_tab === 'timeline' ? 'selected' : '' ?>>Cronograma</option>
            <?php endif; ?>
        </select>
    </div>

    <!-- Conteúdo das Abas -->
    <div class="tab-content">
        <!-- Aba Quadro -->
        <div class="tab-pane <?= $active_tab === 'board' ? 'show active' : '' ?>" id="board-content">
            <div class="accordion" id="kanbanAccordion">
                <?php
                    $simplified_statuses = ['não iniciadas', 'em desenvovimento', 'aprovação', 'concluída', 'cancelada'];
                    $is_simplified = ($project->kanban_layout ?? 'normal') === 'simplified';
                    $statuses_to_show = $is_simplified ? $simplified_statuses : $statuses;
                ?>
                <?php foreach ($statuses as $status): ?>
                    <?php if ($is_simplified && !in_array($status, $simplified_statuses)) continue; ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?= str_replace(' ', '-', $status) ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= str_replace(' ', '-', $status) ?>" aria-expanded="false" aria-controls="collapse-<?= str_replace(' ', '-', $status) ?>">
                                <?= esc(ucfirst($status)) ?>
                                <span class="badge bg-secondary ms-2"><?= count($tasks[$status] ?? []) ?></span>
                            </button>
                        </h2>
                        <div id="collapse-<?= str_replace(' ', '-', $status) ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= str_replace(' ', '-', $status) ?>">
                            <div class="accordion-body p-2">
                                <?php if (empty($tasks[$status])): ?>
                                    <p class="text-center text-muted small p-3">Nenhuma tarefa aqui.</p>
                                <?php else: ?>
                                    <div class="d-grid gap-2">
                                    <?php foreach ($tasks[$status] as $task): ?>
                                        <?php
                                            $cardClass = '';
                                            if (!empty($task->due_date) && !in_array($task->status, ['concluída', 'cancelada'])) {
                                                $dueDate = new \DateTime($task->due_date);
                                                $today = new \DateTime('today');
                                                if ($dueDate < $today) {
                                                    $cardClass = 'card-danger';
                                                } elseif ($today->diff($dueDate)->days <= 3) {
                                                    $cardClass = 'card-warning';
                                                } else {
                                                    $cardClass = 'card-info';
                                                }
                                            }
                                        ?>
                                        <div class="card task-card-mobile <?= $cardClass ?>">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="card-title mb-1 me-2"><?= esc($task->title) ?></h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light py-0 px-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><h6 class="dropdown-header">Mover para:</h6></li>
                                                            <?php foreach ($statuses_to_show as $s): ?>
                                                                <li><a class="dropdown-item change-status-btn" href="#" data-task-id="<?= $task->id ?>" data-new-status="<?= esc($s) ?>"><?= esc(ucfirst($s)) ?></a></li>
                                                            <?php endforeach; ?>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item edit-task-btn" href="#" data-task-id="<?= $task->id ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                                            <li><a class="dropdown-item delete-task-btn" href="#" data-task-id="<?= $task->id ?>" data-task-title="<?= esc($task->title) ?>"><i class="bi bi-trash me-2"></i>Remover</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php if (!empty($task->description)): ?>
                                                    <p class="card-text small text-muted"><?= esc(character_limiter($task->description, 100)) ?></p>
                                                <?php endif; ?>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <?php if (!empty($task->user_id) && isset($users_by_id[$task->user_id])): ?>
                                                        <?= user_icon($users_by_id[$task->user_id], 24) ?>
                                                    <?php else: ?>
                                                        <div style="width: 24px;"></div>
                                                    <?php endif; ?>
                                                    <small class="text-muted">
                                                        <?php if (!empty($task->due_date)): ?>
                                                            <i class="bi bi-calendar-check"></i> <?= date('d/m/Y', strtotime($task->due_date)) ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Outras Abas (Conteúdo a ser adaptado) -->
        <div class="tab-pane <?= $active_tab === 'members' ? 'show active' : '' ?>" id="members-content">
            <div class="alert alert-info">A visualização de membros para mobile ainda será implementada.</div>
        </div>
        <div class="tab-pane <?= $active_tab === 'documents' ? 'show active' : '' ?>" id="documents-content">
            <div class="alert alert-info">A visualização de documentos para mobile ainda será implementada.</div>
        </div>
        <div class="tab-pane <?= $active_tab === 'files' ? 'show active' : '' ?>" id="files-content">
            <div class="alert alert-info">A visualização de arquivos para mobile ainda será implementada.</div>
        </div>
        <div class="tab-pane <?= $active_tab === 'reports' ? 'show active' : '' ?>" id="reports-content">
            <div class="alert alert-info">A visualização de relatórios para mobile ainda será implementada.</div>
        </div>
        <div class="tab-pane <?= $active_tab === 'timeline' ? 'show active' : '' ?>" id="timeline-content">
            <div class="alert alert-info">A visualização de cronograma para mobile ainda será implementada.</div>
        </div>
    </div>

    <!-- Modais (reutilizados da versão desktop) -->
    <?= $this->include('admin/projects/modal') ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]')?.value;

    // Lógica para o seletor de abas
    const tabSelector = document.getElementById('mobileTabSelector');
    if (tabSelector) {
        tabSelector.addEventListener('change', function() {
            const selectedTab = this.value;
            const url = new URL(window.location);
            url.searchParams.set('active_tab', selectedTab);
            window.location.href = url.toString();
        });
    }

    // Lógica para mudar status da tarefa
    document.body.addEventListener('click', function(e) {
        const changeStatusBtn = e.target.closest('.change-status-btn');
        if (changeStatusBtn) {
            e.preventDefault();
            const taskId = changeStatusBtn.dataset.taskId;
            const newStatus = changeStatusBtn.dataset.newStatus;

            // Simula um drag-and-drop para a rota existente
            const data = {
                taskId: taskId,
                newStatus: newStatus,
                order: [taskId] // A ordem não importa tanto aqui, mas precisa ser um array
            };

            fetch(`<?= site_url('admin/tasks/update-board') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao atualizar o status da tarefa.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro de comunicação com o servidor.');
            });
        }

        // Lógica para editar tarefa
        const editBtn = e.target.closest('.edit-task-btn');
        if (editBtn) {
            e.preventDefault();
            const taskId = editBtn.dataset.taskId;
            openEditTaskModal(taskId);
        }

        // Lógica para remover tarefa
        const deleteBtn = e.target.closest('.delete-task-btn');
        if (deleteBtn) {
            e.preventDefault();
            const taskId = deleteBtn.dataset.taskId;
            const taskTitle = deleteBtn.dataset.taskTitle;
            const deleteTaskModal = new bootstrap.Modal(document.getElementById('deleteTaskModal'));
            const deleteTaskForm = document.getElementById('deleteTaskForm');
            const deleteTaskText = document.getElementById('deleteTaskConfirmationText');

            deleteTaskForm.action = `<?= site_url('admin/tasks/') ?>${taskId}/delete`;
            deleteTaskText.innerHTML = `Tem certeza que deseja remover a tarefa: <strong>"${taskTitle}"</strong>?`;
            deleteTaskModal.show();
        }
    });

    // Função para abrir o modal de edição (reutilizada)
    function openEditTaskModal(taskId) {
        const editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        const editTaskForm = document.getElementById('editTaskForm');

        fetch(`<?= site_url('admin/tasks/') ?>${taskId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
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
                alert(data.message || 'Tarefa não encontrada.');
            }
        }).catch(err => alert('Erro ao buscar dados da tarefa.'));
    }
});
</script>

<?= $this->include('partials/footer') ?>