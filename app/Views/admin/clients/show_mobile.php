<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h2 mb-0"><?= esc($client->name) ?></h1>
                <span class="badge fs-6" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span>
            </div>
            <?php if(!empty($client->responsible_name)): ?>
                <p class="text-muted mb-0 small">
                    <i class="bi bi-person"></i> <?= esc($client->responsible_name) ?> (<?= esc($client->responsible_email) ?>)
                </p>
            <?php endif; ?>
        </div>
        <?php if (session()->get('is_admin')): ?>
            <a href="<?= site_url('admin/clients/' . $client->id . '/edit') ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Editar</a>
        <?php endif; ?>
    </div>

    <div class="d-grid gap-3">
        <!-- Tarefas Atrasadas -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0 h6"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tarefas Atrasadas</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($overdue_tasks)): ?>
                    <div class="p-3 text-center text-muted small">Nenhuma tarefa em atraso.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($overdue_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <strong><?= esc($task->title) ?></strong>
                                <small class="d-block text-muted">Projeto: <?= esc($task->project_name) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Próximas Tarefas -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 h6"><i class="bi bi-clock-history me-2"></i>Próximas Tarefas</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcoming_tasks)): ?>
                    <div class="p-3 text-center text-muted small">Nenhuma tarefa próxima.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <strong><?= esc($task->title) ?></strong>
                                <small class="d-block text-muted">Projeto: <?= esc($task->project_name) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Projetos Ativos -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 h6"><i class="bi bi-kanban me-2"></i>Projetos Ativos</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($active_projects)): ?>
                    <div class="p-3 text-center text-muted small">Nenhum projeto ativo.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($active_projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action">
                                <?= esc($project->name) ?>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Projetos Concluídos -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0 h6">Projetos Concluídos</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($concluded_projects)): ?>
                    <div class="p-3 text-center text-muted small">Nenhum projeto concluído.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($concluded_projects as $project): ?>
                            <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="list-group-item list-group-item-action">
                                <?= esc($project->name) ?>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acesso ao Portal do Cliente -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0 h6"><i class="bi bi-shield-lock me-2"></i>Acesso ao Portal</h5>
            </div>
            <div class="card-body">
                <?php if ($access): ?>
                    <p class="small">O acesso para este cliente está <strong>habilitado</strong>.</p>
                    <div class="mb-3">
                        <label class="form-label small">Link de Acesso:</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" value="<?= site_url('portal/' . $access->token) ?>" readonly id="accessLink">
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="copyLinkBtn"><i class="bi bi-clipboard"></i></button>
                        </div>
                    </div>
                    
                    <?php if (session()->has('generated_password')): ?>
                        <div class="alert alert-success">
                            <strong>Nova Senha Gerada:</strong>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control form-control-sm" value="<?= esc(session('generated_password')) ?>" readonly id="accessPassword">
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="copyPasswordBtn"><i class="bi bi-clipboard"></i></button>
                            </div>
                            <small class="d-block mt-2">Anote esta senha. Ela não será exibida novamente.</small>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <form action="<?= site_url('admin/clients/access/' . $access->id . '/regenerate-password') ?>" method="post" onsubmit="return confirm('Gerar uma nova senha invalidará a anterior. Deseja continuar?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-warning w-100">Gerar Nova Senha</button>
                        </form>
                        <form action="<?= site_url('admin/clients/access/' . $access->id . '/delete') ?>" method="post" onsubmit="return confirm('Tem certeza que deseja remover permanentemente o acesso deste cliente ao portal?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger w-100">Remover Acesso</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="small text-muted">O cliente ainda não tem acesso ao portal.</p>
                    <form action="<?= site_url('admin/clients/' . $client->id . '/enable-access') ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-success w-100">Habilitar Acesso e Gerar Senha</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reutiliza a mesma função de copiar da versão desktop
    function copyToClipboard(elementId, buttonId) {
        const input = document.getElementById(elementId);
        const button = document.getElementById(buttonId);
        if (!input || !button) return;

        button.addEventListener('click', function() {
            navigator.clipboard.writeText(input.value).then(function() {
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check-lg"></i>';
                setTimeout(() => { button.innerHTML = originalHtml; }, 2000);
            });
        });
    }

    copyToClipboard('accessLink', 'copyLinkBtn');
    copyToClipboard('accessPassword', 'copyPasswordBtn');
});
</script>

<?= $this->include('partials/footer') ?>