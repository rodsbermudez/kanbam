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

<!-- Modal Adicionar Arquivo/Link -->
<div class="modal fade" id="addFileModal" tabindex="-1" aria-labelledby="addFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addFileModalLabel">Adicionar Item</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill" id="addItemTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upload-file-tab" data-bs-toggle="tab" data-bs-target="#upload-file-pane" type="button" role="tab" aria-controls="upload-file-pane" aria-selected="true">Enviar Arquivo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-link-tab" data-bs-toggle="tab" data-bs-target="#add-link-pane" type="button" role="tab" aria-controls="add-link-pane" aria-selected="false">Adicionar Link</button>
                    </li>
                </ul>
                <div class="tab-content p-3" id="addItemTabContent">
                    <!-- Tab de Upload de Arquivo -->
                    <div class="tab-pane fade show active" id="upload-file-pane" role="tabpanel" aria-labelledby="upload-file-tab">
                        <form action="<?= site_url('admin/projects/' . $project->id . '/files') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-3"><label for="file_title" class="form-label">Título do Arquivo <span class="text-danger">*</span></label><input type="text" class="form-control" id="file_title" name="title" required></div>
                            <div class="mb-3"><label for="file_description" class="form-label">Breve Descrição</label><textarea class="form-control" id="file_description" name="description" rows="2"></textarea></div>
                            <div class="mb-3"><label for="file_upload" class="form-label">Selecione o Arquivo <span class="text-danger">*</span></label><input class="form-control" type="file" id="file_upload" name="file" required></div>
                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Enviar Arquivo</button>
                            </div>
                        </form>
                    </div>
                    <!-- Tab de Adicionar Link -->
                    <div class="tab-pane fade" id="add-link-pane" role="tabpanel" aria-labelledby="add-link-tab">
                        <form action="<?= site_url('admin/projects/' . $project->id . '/links') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3"><label for="link_title" class="form-label">Título do Link <span class="text-danger">*</span></label><input type="text" class="form-control" id="link_title" name="title" required></div>
                            <div class="mb-3"><label for="link_url" class="form-label">URL Externa <span class="text-danger">*</span></label><input type="url" class="form-control" id="link_url" name="url" placeholder="https://exemplo.com" required></div>
                            <div class="mb-3"><label for="link_description" class="form-label">Breve Descrição</label><textarea class="form-control" id="link_description" name="description" rows="2"></textarea></div>
                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Adicionar Link</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importar Relatório -->
<div class="modal fade" id="importReportModal" tabindex="-1" aria-labelledby="importReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importReportModalLabel">Importar Relatório de SEO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/projects/' . $project->id . '/reports/import') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Selecione um dos relatórios disponíveis abaixo para importá-lo para este projeto.</p>
                    <div id="available-reports-container" class="list-group" style="max-height: 400px; overflow-y: auto;">
                        <!-- A lista de relatórios será carregada aqui via AJAX -->
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </div>
                    </div>
                    <div id="import-error-alert" class="alert alert-danger d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="importReportSubmitBtn" disabled>Importar Selecionado</button>
                </div>
            </form>
        </div>
    </div>
</div>