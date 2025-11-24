<!-- Due Tasks Sidebar -->
<style>
    .sidebar {
        width: 450px; /* Aumenta a largura da barra lateral */
    }
    #taskTabs .nav-link {
        white-space: nowrap; /* Impede que o texto quebre em várias linhas */
        font-size: 0.85rem; /* Reduz um pouco o tamanho da fonte para ajudar no ajuste */
    }
</style>
<div class="sidebar-overlay d-none" id="sidebar-overlay"></div>
<div class="sidebar" id="due-tasks-sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tarefas Atrasadas e Próximas</h5>
        <button type="button" class="btn-close" id="close-due-tasks-sidebar" aria-label="Close"></button>
    </div>
    <div class="sidebar-body">
        <?php
        // Mapeamento de status para cores de badge, para ser usado nos loops
        $status_colors = [
            'concluída'         => 'bg-success',
            'cancelada'         => 'bg-danger',
            'em desenvovimento' => 'bg-primary',
            'ajustes'           => 'bg-warning text-dark',
            'aprovação'         => 'bg-info text-dark',
            'não iniciadas'     => 'bg-light text-dark',
            'com cliente'       => 'bg-info text-dark',
            'aprovada'          => 'bg-success',
            'implementada'      => 'bg-success',
            'default'           => 'bg-secondary'
        ];
        ?>

        <ul class="nav nav-tabs nav-fill" id="taskTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#overdue-tab-pane" type="button" role="tab" aria-controls="overdue-tab-pane" aria-selected="true">
                    Atrasadas <span class="badge rounded-pill bg-danger"><?= count($sidebar_overdue_other ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-tab-pane" type="button" role="tab" aria-controls="upcoming-tab-pane" aria-selected="false">
                    Próximas <span class="badge rounded-pill bg-primary"><?= count($sidebar_upcoming ?? []) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="client-tab" data-bs-toggle="tab" data-bs-target="#client-tab-pane" type="button" role="tab" aria-controls="client-tab-pane" aria-selected="false">
                    Aguardando Cliente <span class="badge rounded-pill bg-info"><?= count($sidebar_overdue_client ?? []) ?></span>
                </button>
            </li>
        </ul>
        <div class="tab-content" id="taskTabsContent">
            <!-- Painel Tarefas Atrasadas -->
            <div class="tab-pane fade show active" id="overdue-tab-pane" role="tabpanel" aria-labelledby="overdue-tab" tabindex="0">
                <?php if (empty($sidebar_overdue_other)): ?>
                    <div class="p-3 text-center text-muted">Nenhuma tarefa atrasada.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($sidebar_overdue_other as $task): ?>
                            <?php $status_class = $status_colors[$task->status] ?? $status_colors['default']; ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small>Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <p class="mb-0 text-muted small">Projeto: <?= esc($task->project_name) ?></p>
                                    <div>
                                        <span class="badge <?= $status_class ?> me-1"><?= esc(ucfirst($task->status)) ?></span>
                                        <?php if (!empty($task->client_tag)): ?>
                                            <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Painel Próximas Tarefas -->
            <div class="tab-pane fade" id="upcoming-tab-pane" role="tabpanel" aria-labelledby="upcoming-tab" tabindex="0">
                <?php if (empty($sidebar_upcoming)): ?>
                    <div class="p-3 text-center text-muted">Nenhuma tarefa para os próximos 7 dias.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($sidebar_upcoming as $task): ?>
                            <?php $status_class = $status_colors[$task->status] ?? $status_colors['default']; ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <p class="mb-0 text-muted small">Projeto: <?= esc($task->project_name) ?></p>
                                    <div>
                                        <span class="badge <?= $status_class ?> me-1"><?= esc(ucfirst($task->status)) ?></span>
                                        <?php if (!empty($task->client_tag)): ?>
                                            <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Painel Aguardando Cliente -->
            <div class="tab-pane fade" id="client-tab-pane" role="tabpanel" aria-labelledby="client-tab" tabindex="0">
                <?php if (empty($sidebar_overdue_client)): ?>
                    <div class="p-3 text-center text-muted">Nenhuma tarefa aguardando o cliente.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($sidebar_overdue_client as $task): ?>
                            <?php $status_class = $status_colors[$task->status] ?? $status_colors['default']; ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <p class="mb-0 text-muted small">Projeto: <?= esc($task->project_name) ?></p>
                                    <div>
                                        <span class="badge <?= $status_class ?> me-1"><?= esc(ucfirst($task->status)) ?></span>
                                        <?php if (!empty($task->client_tag)): ?>
                                            <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>