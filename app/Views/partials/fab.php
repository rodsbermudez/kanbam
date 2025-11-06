<?php if (session()->get('is_admin')): ?>

<!-- Estilos para o Floating Action Button -->
<style>
    .fab-container {
        position: fixed;
        bottom: 60px;
        right: 60px;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .fab-toggler {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--bs-primary);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, background-color 0.3s ease;
        cursor: pointer;
    }
    .fab-toggler:hover {
        background-color: var(--bs-primary-dark); /* Você precisaria definir essa variável ou usar uma cor mais escura */
        transform: scale(1.1);
    }
    .fab-toggler.open {
        transform: rotate(45deg);
    }
    .fab-menu {
        list-style: none;
        padding: 0;
        margin: 0 0 15px 0;
        display: flex;
        flex-direction: column-reverse;
        gap: 15px;
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.3s ease;
        order: -1; /* Garante que o menu apareça ACIMA do botão '+' */
    }
    .fab-container.open .fab-menu {
        transform: scaleY(1);
    }
    .fab-menu-item {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--bs-primary) !important;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .fab-menu-item:hover {
        background-color: var(--bs-gray-800) !important;
        transform: scale(1.1);
    }
    .fab-menu-item i {
        font-size: 1.2rem;
    }
</style>

<!-- Estrutura HTML do FAB -->
<div class="fab-container">
    <div class="fab-menu">
        <a href="#" class="fab-menu-item" data-bs-toggle="modal" data-bs-target="#globalAddTaskModal" title="Nova Tarefa">
            <i class="bi bi-list-task"></i>
        </a>
        <a href="#" class="fab-menu-item" data-bs-toggle="modal" data-bs-target="#globalAddProjectModal" title="Novo Projeto">
            <i class="bi bi-folder-plus"></i>
        </a>
        <a href="#" class="fab-menu-item" data-bs-toggle="modal" data-bs-target="#globalAddUserModal" title="Novo Usuário">
            <i class="bi bi-person-plus"></i>
        </a>
        <a href="#" class="fab-menu-item" data-bs-toggle="modal" data-bs-target="#globalAddClientModal" title="Novo Cliente">
            <i class="bi bi-person-badge"></i>
        </a>
    </div>
    <button class="fab-toggler">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>

<!-- Modal Global: Adicionar Tarefa -->
<div class="modal fade" id="globalAddTaskModal" tabindex="-1" aria-labelledby="globalAddTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="globalAddTaskModalLabel">Criar Nova Tarefa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/tasks/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="global_project_id" class="form-label">Projeto</label>
                        <select name="project_id" id="global_project_id" class="form-select" required>
                            <option value="">-- Selecione um projeto --</option>
                            <?php if (!empty($global_projects)): ?>
                                <?php foreach ($global_projects as $proj): ?>
                                    <option value="<?= $proj->id ?>"><?= esc($proj->name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="global_title" class="form-label">Título da Tarefa</label>
                        <input type="text" class="form-control" id="global_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="global_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="global_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="global_status" class="form-label">Status Inicial</label>
                            <select name="status" id="global_status" class="form-select">
                                <?php foreach ($global_task_statuses as $status): ?>
                                    <option value="<?= esc($status) ?>"><?= esc(ucfirst($status)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="global_due_date" class="form-label">Data de Entrega</label>
                            <input type="date" class="form-control" id="global_due_date" name="due_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="global_user_id" class="form-label">Atribuir a</label>
                        <select name="user_id" id="global_user_id" class="form-select" disabled>
                            <option value="">-- Selecione um projeto primeiro --</option>
                        </select>
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

<!-- Modal Global: Adicionar Projeto -->
<div class="modal fade" id="globalAddProjectModal" tabindex="-1" aria-labelledby="globalAddProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="globalAddProjectModalLabel">Criar Novo Projeto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/projects') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="global_project_name" class="form-label">Nome do Projeto</label>
                        <input type="text" class="form-control" id="global_project_name" name="name" value="<?= old('name') ?>" required>
                    </div>
                    <!-- Campo Cliente (Obrigatório) -->
                    <div class="mb-3">
                        <label for="global_client_id" class="form-label">Cliente</label>
                        <select class="form-select" id="global_client_id" name="client_id" required>
                            <option value="">Selecione um cliente...</option>
                            <?php if (!empty($global_clients)): ?>
                                <?php foreach ($global_clients as $client): ?>
                                    <option value="<?= $client->id ?>" <?= old('client_id') == $client->id ? 'selected' : '' ?>>
                                        <?= esc($client->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-danger">A associação a um cliente é obrigatória.</small>
                    </div>
                    <div class="mb-3">
                        <label for="global_project_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="global_project_description" name="description" rows="3"><?= old('description') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Projeto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Global: Adicionar Usuário -->
<div class="modal fade" id="globalAddUserModal" tabindex="-1" aria-labelledby="globalAddUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="globalAddUserModalLabel">Criar Novo Usuário</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/users') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="global_user_name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="global_user_name" name="name" value="<?= old('name') ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="global_initials" class="form-label">Sigla (2 letras)</label>
                            <input type="text" class="form-control" id="global_initials" name="initials" value="<?= old('initials') ?>" maxlength="2">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="global_color" class="form-label">Cor do Ícone</label>
                            <input type="color" class="form-control form-control-color" id="global_color" name="color" value="<?= old('color', '#6c757d') ?>" title="Escolha uma cor">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="global_user_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="global_user_email" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <!-- Slack User ID -->
                    <div class="form-group mb-3">
                        <label for="global_slack_user_id" class="form-label">ID do Slack</label>
                        <input type="text" class="form-control" id="global_slack_user_id" name="slack_user_id"
                               placeholder="Ex: U0123ABCDEF"
                               value="<?= old('slack_user_id') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="global_user_password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="global_user_password" name="password" required>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="global_user_is_admin" name="is_admin" value="1" <?= old('is_admin') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="global_user_is_admin">É Administrador?</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="global_user_is_active" name="is_active" value="1" <?= old('is_active', 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="global_user_is_active">Usuário Ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Global: Adicionar Cliente -->
<div class="modal fade" id="globalAddClientModal" tabindex="-1" aria-labelledby="globalAddClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="globalAddClientModalLabel">Criar Novo Cliente</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/clients') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="global_client_name" class="form-label">Nome do Cliente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="global_client_name" name="name" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="global_client_tag" class="form-label">Tag <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="global_client_tag" name="tag" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="global_client_color" class="form-label">Cor</label>
                            <input type="color" class="form-control form-control-color" id="global_client_color" name="color" value="#6c757d" title="Escolha uma cor para o cliente">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="global_responsible_name" class="form-label">Nome do Responsável</label>
                        <input type="text" class="form-control" id="global_responsible_name" name="responsible_name">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="global_responsible_email" class="form-label">Email do Responsável</label>
                            <input type="email" class="form-control" id="global_responsible_email" name="responsible_email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="global_responsible_phone" class="form-label">Telefone do Responsável</label>
                            <input type="text" class="form-control" id="global_responsible_phone" name="responsible_phone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Lógica do FAB
    const fabContainer = document.querySelector('.fab-container');
    const fabToggler = document.querySelector('.fab-toggler');
    if (fabToggler) {
        fabToggler.addEventListener('click', () => {
            fabContainer.classList.toggle('open');
        });
    }

    // Lógica do Modal de Tarefa (AJAX para buscar membros)
    const projectSelect = document.getElementById('global_project_id');
    const userSelect = document.getElementById('global_user_id');

    if (projectSelect) {
        projectSelect.addEventListener('change', function() {
            const projectId = this.value;
            userSelect.innerHTML = '<option value="">Carregando...</option>';
            userSelect.disabled = true;

            if (!projectId) {
                userSelect.innerHTML = '<option value="">-- Selecione um projeto primeiro --</option>';
                return;
            }

            fetch(`<?= site_url('admin/projects/') ?>${projectId}/members`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                userSelect.innerHTML = '<option value="">-- Não atribuído --</option>';
                if (data.success && data.members) {
                    data.members.forEach(member => {
                        const option = new Option(member.name, member.id);
                        userSelect.add(option);
                    });
                }
                userSelect.disabled = false;
            })
            .catch(error => {
                console.error('Erro ao buscar membros:', error);
                userSelect.innerHTML = '<option value="">Erro ao carregar</option>';
            });
        });
    }
});
</script>

<?php endif; ?>