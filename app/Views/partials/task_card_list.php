<?php if (empty($tasks)): ?>
    <div class="p-3 text-center text-muted">
        Nenhuma tarefa vencendo em breve.
    </div>
<?php else: ?>
    <?php
    // Mapeamento de status para cores de badge, para ser usado no loop
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
    <?php foreach ($tasks as $task): ?>
        <?php
            $isOverdue = false;
            if (!empty($task->due_date)) {
                try {
                    $dueDate = new \DateTime($task->due_date);
                    $today = new \DateTime('today');
                    if ($dueDate < $today) {
                        $isOverdue = true;
                    }
                } catch (Exception $e) {}
            }
            $status_class = $status_colors[$task->status] ?? $status_colors['default'];
        ?>
        <a href="<?= site_url('admin/projects/' . $task->project_id_for_link) ?>" class="text-decoration-none text-dark">
            <div class="card mb-2 task-card-sidebar <?= $isOverdue ? 'border-danger' : '' ?>">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title mb-1 small"><?= esc($task->title) ?></h6>
                    </div>
                    <p class="card-text small text-muted mb-1">
                        <?= esc($task->project_name) ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge <?= $status_class ?> me-1"><?= esc(ucfirst($task->status)) ?></span>
                            <?php if (!empty($task->client_tag)): ?>
                                <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted <?= $isOverdue ? 'fw-bold text-danger' : '' ?>">
                            <?= date('d/m', strtotime($task->due_date)) ?>
                        </small>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>