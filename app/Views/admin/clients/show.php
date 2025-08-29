<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="mb-0"><?= esc($client->name) ?></h1>
                <span class="badge fs-6" style="background-color: <?= esc($client->color ?? '#6c757d') ?>;"><?= esc($client->tag) ?></span>
            </div>
            <p class="text-muted mb-0">
                Responsável: <?= esc($client->responsible_name) ?> (<?= esc($client->responsible_email) ?>)
            </p>
        </div>
        <?php if (session()->get('is_admin')): ?>
            <a href="<?= site_url('admin/clients/' . $client->id . '/edit') ?>" class="btn btn-primary">Editar Cliente</a>
        <?php endif; ?>
    </div>

    <hr>

    <div class="row g-4">
        <!-- Coluna de Tarefas -->
        <div class="col-md-7">
            <!-- Próximas Tarefas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Próximas Tarefas (7 dias)</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($upcoming_tasks)): ?>
                        <div class="list-group-item">Nenhuma tarefa próxima.</div>
                    <?php else: ?>
                        <?php foreach ($upcoming_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= esc($task->title) ?></h6>
                                    <small><?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    Projeto: <?= esc($task->project_name) ?>
                                </p>
                                <?php if (!empty($task->client_tag)): ?>
                                    <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tarefas Atrasadas -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Tarefas Atrasadas</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($overdue_tasks)): ?>
                        <div class="list-group-item">Nenhuma tarefa atrasada.</div>
                    <?php else: ?>
                        <?php foreach ($overdue_tasks as $task): ?>
                            <a href="<?= site_url('admin/projects/' . $task->project_id) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-danger"><?= esc($task->title) ?></h6>
                                    <small class="text-danger">Venceu em: <?= date('d/m/Y', strtotime($task->due_date)) ?></small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    Projeto: <?= esc($task->project_name) ?>
                                </p>
                                <?php if (!empty($task->client_tag)): ?>
                                    <span class="badge" style="background-color: <?= esc($task->client_color ?? '#6c757d') ?>;"><?= esc($task->client_tag) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Coluna de Projetos -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Projetos Ativos</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($active_projects)): ?>
                        <div class="list-group-item">Nenhum projeto ativo para este cliente.</div>
                    <?php else: ?>
                        <?php foreach ($active_projects as $project): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="text-decoration-none text-body flex-grow-1">
                                    <?= esc($project->name) ?>
                                </a>
                                <div class="form-check form-switch" title="Exibir no portal do cliente">
                                    <input class="form-check-input project-visibility-toggle" type="checkbox" role="switch" 
                                           data-project-id="<?= $project->id ?>" 
                                           <?= !isset($project->is_visible_to_client) || $project->is_visible_to_client ? 'checked' : '' ?>>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Projetos Concluídos</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($concluded_projects)): ?>
                        <div class="list-group-item">Nenhum projeto concluído para este cliente.</div>
                    <?php else: ?>
                        <?php foreach ($concluded_projects as $project): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="<?= site_url('admin/projects/' . $project->id) ?>" class="text-decoration-none text-body flex-grow-1">
                                    <?= esc($project->name) ?> <span class="badge bg-success">Concluído</span>
                                </a>
                                <div class="form-check form-switch" title="Exibir no portal do cliente">
                                    <input class="form-check-input project-visibility-toggle" type="checkbox" role="switch" 
                                           data-project-id="<?= $project->id ?>" 
                                           <?= !isset($project->is_visible_to_client) || $project->is_visible_to_client ? 'checked' : '' ?>>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Acesso ao Portal do Cliente</h5>
                </div>
                <div class="card-body">
                    <?php if ($access): ?>
                        <p>O acesso para este cliente está <strong>habilitado</strong>.</p>
                        <div class="mb-3">
                            <label class="form-label">Link de Acesso:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?= site_url('portal/' . $access->token) ?>" readonly id="accessLink">
                                <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn"><i class="bi bi-clipboard"></i></button>
                            </div>
                        </div>
                        
                        <?php if (session()->has('generated_password')): ?>
                            <div class="alert alert-success">
                                <strong>Nova Senha Gerada:</strong>
                                <div class="input-group mt-2">
                                    <input type="text" class="form-control" value="<?= esc(session('generated_password')) ?>" readonly id="accessPassword">
                                    <button class="btn btn-outline-secondary" type="button" id="copyPasswordBtn"><i class="bi bi-clipboard"></i></button>
                                </div>
                                <small class="d-block mt-2">Anote esta senha. Ela não será exibida novamente.</small>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('admin/clients/access/' . $access->id . '/regenerate-password') ?>" method="post" onsubmit="return confirm('Gerar uma nova senha invalidará a anterior. Deseja continuar?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-warning w-100">Gerar Nova Senha</button>
                        </form>
                    <?php else: ?>
                        <p>O cliente ainda não tem acesso ao portal.</p>
                        <form action="<?= site_url('admin/clients/' . $client->id . '/enable-access') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100">Habilitar Acesso e Gerar Senha</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    const visibilityToggles = document.querySelectorAll('.project-visibility-toggle');
    visibilityToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const projectId = this.dataset.projectId;
            const isVisible = this.checked;
            const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]').value;

            fetch(`<?= site_url('admin/projects/') ?>${projectId}/toggle-client-visibility`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ is_visible: isVisible }) // Embora a lógica no backend não use isso, é bom enviar
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // A mensagem de sucesso pode ser exibida com um toast, se desejado.
                    // Por enquanto, a mudança visual do switch é suficiente.
                    console.log('Visibilidade do projeto alterada com sucesso.');
                } else {
                    // Reverte o switch em caso de erro
                    this.checked = !isVisible;
                    alert('Erro ao alterar a visibilidade do projeto.');
                }
            });
        });
    });
});
</script>

<?= $this->include('partials/footer') ?>