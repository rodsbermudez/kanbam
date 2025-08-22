<?php
helper('user');
helper('text');

foreach ($tasks as $task):
    // Lógica para determinar a cor do card com base na data de entrega
    $cardClass = '';
    if (!empty($task->due_date)) {
        try {
            $dueDate = new \DateTime($task->due_date);
            $today = new \DateTime('today');
            
            if ($dueDate < $today) {
                $cardClass = 'card-danger';
            } else {
                $interval = $today->diff($dueDate);
                if ($interval->days <= 3) {
                    $cardClass = 'card-warning';
                } else {
                    $cardClass = 'card-info';
                }
            }
        } catch (Exception $e) {
            // Data inválida, não aplica classe
        }
    } else {
        $cardClass = 'bg-light';
    }
?>
<a href="<?= site_url('admin/projects/' . $task->project_id_for_link) ?>" class="kanban-card <?= $cardClass ?> text-decoration-none d-block">
    <div class="mb-2">
        <span class="badge bg-secondary text-dark project-name-badge">
            <i class="bi bi-folder-fill me-1"></i><?= esc($task->project_name) ?>
        </span>
    </div>

    <h6 class="card-title fw-bold mb-0"><?= esc($task->title) ?></h6>

    <?php if (!empty($task->description)): ?>
        <p class="card-text mt-2 mb-0"><?= esc(character_limiter($task->description, 80)) ?></p>
    <?php endif; ?>

    <div class="kanban-card-footer d-flex justify-content-between align-items-center mt-3">
        <?= user_icon($users_by_id[$task->user_id] ?? null, 24) ?>
        
        <small class="text-muted">
            <?php if (!empty($task->due_date)): ?>
                <i class="bi bi-calendar-event"></i> <?= date('d/m/Y', strtotime($task->due_date)) ?>
            <?php endif; ?>
        </small>
    </div>
</a>
<?php endforeach; ?>

<?php if (empty($tasks)): ?>
    <p class="text-muted text-center p-4">Nenhuma tarefa vencendo nos próximos 7 dias.</p>
<?php endif; ?>