<?php if (empty($tasks)): ?>
    <div class="p-3 text-center text-muted">
        Nenhuma tarefa vencendo em breve.
    </div>
<?php else: ?>
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
                        <?php if (!empty($task->client_tag)): ?>
                            <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                        <?php endif; ?>
                        <small class="text-muted <?= $isOverdue ? 'fw-bold text-danger' : '' ?>">
                            <?= date('d/m', strtotime($task->due_date)) ?>
                        </small>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>