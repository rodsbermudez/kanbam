<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>

<main class="container mt-6">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <h1><?= isset($type) ? 'Editar Tipo de Projeto' : 'Criar Tipo de Projeto' ?></h1>
            <hr>

            <?php if (session()->get('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                    <?php foreach (session()->get('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= isset($type) ? site_url('admin/project-type/' . $type->id) : site_url('admin/project-type') ?>" method="post">
                <?= csrf_field() ?>

                <?php if (isset($type)): ?>
                    <input type="hidden" name="_method" value="PUT">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome do Tipo de Projeto</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $type->name ?? '') ?>" required>
                    <small class="form-text text-muted">Ex: Website Institucional, Campanha de Marketing, App Mobile.</small>
                </div>

                <div class="mb-3">
                    <label for="prompt" class="form-label">Prompt para a IA</label>
                    <textarea class="form-control" id="prompt" name="prompt" rows="15" required><?= old('prompt', $type->prompt ?? 'Você é um assistente de gerenciamento de projetos especialista. Sua tarefa é quebrar um projeto em uma lista de tarefas (cards de Kanban). Analise a descrição do projeto, as páginas a serem criadas e o prazo final. Crie uma lista de tarefas detalhadas, desde o planejamento inicial até a entrega final. Distribua as datas de entrega (\'due_date\') para cada tarefa de forma realista entre a data de hoje ({$today}) e o prazo final ({$deadline}). Sua resposta DEVE ser um JSON válido, contendo um array de objetos. Cada objeto deve ter as chaves: \'title\', \'description\' e \'due_date\' (formato YYYY-MM-DD). Dados do Projeto: - Descrição: {$description} - Itens/Páginas: {$pages} - Prazo Final: {$deadline}') ?></textarea>
                    <small class="form-text text-muted">
                        Descreva as instruções para a IA. Use as variáveis <code>{$description}</code>, <code>{$pages}</code>, <code>{$deadline}</code> e <code>{$today}</code> que serão substituídas dinamicamente.
                    </small>
                </div>

                <fieldset class="border p-3 my-4 rounded">
                    <legend class="float-none w-auto px-2 h6">Customização do Formulário da IA</legend>
                    <p class="text-muted small">Personalize os rótulos e placeholders que o usuário verá ao usar este tipo de projeto para gerar tarefas.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="label_description" class="form-label">Rótulo do Campo de Descrição</label>
                            <input type="text" class="form-control" id="label_description" name="label_description" value="<?= old('label_description', $type->label_description ?? 'Descrição Detalhada do Projeto') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="label_items" class="form-label">Rótulo do Campo de Itens</label>
                            <input type="text" class="form-control" id="label_items" name="label_items" value="<?= old('label_items', $type->label_items ?? 'Itens/Páginas a Serem Criados (um por linha)') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="placeholder_description" class="form-label">Placeholder do Campo de Descrição</label>
                        <textarea class="form-control" id="placeholder_description" name="placeholder_description" rows="3"><?= old('placeholder_description', $type->placeholder_description ?? 'Ex: Preciso criar um site institucional para uma advocacia...') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="placeholder_items" class="form-label">Placeholder do Campo de Itens</label>
                        <textarea class="form-control" id="placeholder_items" name="placeholder_items" rows="3"><?= old('placeholder_items', $type->placeholder_items ?? "Home\nSobre Nós\nContato") ?></textarea>
                    </div>
                </fieldset>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('admin/project-type') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<?= $this->include('partials/footer') ?>