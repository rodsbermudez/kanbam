<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectTypeModel;
use App\Models\TaskModel;
use Gemini;

class TasksController extends BaseController
{
    /**
     * Processa a criação de uma nova tarefa.
     */
    public function create($projectId = null)
    {
        $taskModel = new TaskModel();

        $data = $this->request->getPost();

        // Se o projectId não veio pela URL (rota específica do projeto), 
        // pega do formulário (rota global do FAB).
        if ($projectId === null) {
            $projectId = $data['project_id'] ?? null;
        }
        // Garante que o ID do projeto esteja nos dados a serem salvos.
        $data['project_id'] = $projectId;

        // Garante que uma data de entrega vazia seja salva como NULL no banco de dados.
        if (empty($data['due_date'])) {
            $data['due_date'] = null;
        }

        // Garante que um user_id vazio seja salvo como NULL para evitar erro de chave estrangeira.
        if (array_key_exists('user_id', $data) && empty($data['user_id'])) {
            $data['user_id'] = null;
        }

        if ($taskModel->save($data)) {
            return redirect()->to('/admin/projects/' . $projectId)
                             ->with('success', 'Tarefa criada com sucesso.')
                             ->with('active_tab', 'board');
        }

        return redirect()->back()->withInput()
                         ->with('errors', $taskModel->errors())
                         ->with('active_tab', 'board');
    }

    /**
     * Retorna os dados de uma tarefa específica em formato JSON.
     * Usado para popular o modal de edição.
     */
    public function show($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $taskModel = new TaskModel();
        $task = $taskModel->find($id);

        if (!$task) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Tarefa não encontrada.']);
        }

        return $this->response->setJSON(['success' => true, 'task' => $task]);
    }

    /**
     * Processa a atualização de uma tarefa.
     */
    public function update($id = null)
    {
        $taskModel = new TaskModel();
        $task = $taskModel->find($id);

        if (!$task) {
            return redirect()->back()->with('error', 'Tarefa não encontrada.');
        }

        $data = $this->request->getPost();

        // Garante que valores vazios sejam salvos como NULL
        if (array_key_exists('due_date', $data) && empty($data['due_date'])) {
            $data['due_date'] = null;
        }
        if (array_key_exists('user_id', $data) && empty($data['user_id'])) {
            $data['user_id'] = null;
        }

        if ($taskModel->update($id, $data)) {
            return redirect()->to('/admin/projects/' . $task->project_id)
                             ->with('success', 'Tarefa atualizada com sucesso.')
                             ->with('active_tab', 'board');
        }

        return redirect()->to('/admin/projects/' . $task->project_id)
                         ->withInput()->with('errors', $taskModel->errors())->with('active_tab', 'board');
    }

    /**
     * Deleta (soft delete) uma tarefa.
     */
    public function delete($id = null)
    {
        $taskModel = new TaskModel();
        $task = $taskModel->find($id);
        $projectId = $task->project_id ?? null;

        if ($taskModel->delete($id)) {
            return redirect()->to('/admin/projects/' . $projectId)->with('success', 'Tarefa removida com sucesso.')->with('active_tab', 'board');
        }

        return redirect()->to('/admin/projects/' . $projectId)->with('error', 'Erro ao remover a tarefa.')->with('active_tab', 'board');
    }

    /**
     * Gera tarefas usando a API do Gemini.
     */
    public function generateWithAI($projectId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'project_type_id'     => 'required|is_not_unique[project_types.id]',
            'project_description' => 'required',
            'project_deadline'    => 'required|valid_date',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Todos os campos são obrigatórios.', 'errors' => $validation->getErrors()]);
        }

        $projectTypeId = $this->request->getPost('project_type_id');
        $description = $this->request->getPost('project_description');
        $pages = $this->request->getPost('project_pages');
        $deadline = $this->request->getPost('project_deadline');
        $today = date('Y-m-d');

        // Busca o prompt do banco de dados
        $projectTypeModel = new ProjectTypeModel();
        $projectType = $projectTypeModel->find($projectTypeId);

        if (!$projectType) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Tipo de projeto não encontrado.']);
        }

        // Substitui as variáveis no prompt
        $prompt = str_replace(
            ['{$description}', '{$pages}', '{$deadline}', '{$today}'],
            [$description, $pages, $deadline, $today],
            $projectType->prompt
        );

        try {
            $apiKey = getenv('GEMINI_API_KEY');
            if (empty($apiKey)) {
                log_message('error', 'GEMINI_API_KEY is not set.');
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'A chave da API de IA não está configurada no servidor.']);
            }

            $client = Gemini::client($apiKey);
            // Usamos o modelo 'gemini-1.5-flash-latest', que é estável e recomendado para um bom equilíbrio entre custo e performance.
            $result = $client->generativeModel(model: 'gemini-flash-latest')->generateContent($prompt);

            $tasksJson = $result->text();
            // Limpa a resposta da IA, removendo cercas de markdown e a palavra "json"
            $cleanedJson = preg_replace('/^```json\s*|```$/', '', $tasksJson);
            $tasks = json_decode($cleanedJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($tasks)) {
                log_message('error', 'Gemini API returned invalid JSON: ' . $tasksJson);
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'A IA retornou uma resposta em formato inválido. Tente ser mais específico na descrição.']);
            }

            $tasksToInsert = [];
            foreach ($tasks as $taskData) {
                // Validação e sanitização de cada tarefa recebida da IA
                if (!empty($taskData['title'])) {
                    $tasksToInsert[] = [
                        'project_id'  => $projectId,
                        'title'       => filter_var($taskData['title'], FILTER_SANITIZE_SPECIAL_CHARS),
                        'description' => isset($taskData['description']) ? filter_var($taskData['description'], FILTER_SANITIZE_SPECIAL_CHARS) : null,
                        'due_date'    => (isset($taskData['due_date']) && strtotime($taskData['due_date'])) ? $taskData['due_date'] : null,
                        'status'      => 'não iniciadas', // Status padrão
                    ];
                }
            }

            // Em vez de salvar, retornamos as tarefas para aprovação do usuário
            return $this->response->setJSON(['success' => true, 'tasks' => $tasksToInsert]);

        } catch (\Exception $e) {
            log_message('error', '[Gemini API Error] ' . $e->getMessage());

            // Para depuração, vamos mostrar o erro real em ambiente de desenvolvimento.
            // Lembre-se de remover ou ajustar isso em produção.
            $detailedError = (ENVIRONMENT === 'development') ? ' Detalhe: ' . $e->getMessage() : '';

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao contatar a API de IA.' . $detailedError]);
        }
    }

    /**
     * Salva as tarefas geradas pela IA após a aprovação do usuário.
     */
    public function saveAIGeneratedTasks($projectId)
    {
        try {
            $tasks = $this->request->getJSON(true)['tasks'] ?? [];

            if (empty($tasks)) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nenhuma tarefa foi enviada para salvamento.']);
            }

            $taskModel = new TaskModel();
            if ($taskModel->insertBatch($tasks)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Tarefas geradas com sucesso!']);
            }
            
            log_message('error', 'TaskModel::insertBatch failed for project ' . $projectId);
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Não foi possível salvar as tarefas no banco de dados.']);

        } catch (\Exception $e) {
            log_message('error', '[Save AI Tasks Error] ' . $e->getMessage());
            $detailedError = (ENVIRONMENT === 'development') ? ' Detalhe: ' . $e->getMessage() : '';
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Ocorreu um erro inesperado ao salvar as tarefas.' . $detailedError]);
        }
    }

    /**
     * Atualiza o status e a ordem de uma tarefa via drag-and-drop.
     */
    public function updateBoard()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $data = $this->request->getJSON(true);

        $taskId    = $data['taskId'] ?? null;
        $newStatus = $data['newStatus'] ?? null;
        $order     = $data['order'] ?? [];

        if (!$taskId || !$newStatus || empty($order)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Dados inválidos.']);
        }

        $taskModel = new TaskModel();

        try {
            // Prepara os dados para a atualização em lote
            $updateData = [];
            foreach ($order as $index => $id) {
                $updateData[] = [
                    'id'     => (int)$id,
                    'status' => $newStatus,
                    'order'  => $index
                ];
            }

            if ($taskModel->updateBatch($updateData, 'id')) {
                return $this->response->setJSON(['success' => true, 'message' => 'Quadro atualizado com sucesso.']);
            }

            // Se o updateBatch falhar (ex: por validação), retorna um erro.
            log_message('error', '[Update Board Validation Error] ' . json_encode($taskModel->errors()));
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Falha ao atualizar as tarefas.', 'errors' => $taskModel->errors()]);
        } catch (\Exception $e) {
            log_message('error', '[Update Board Error] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao atualizar o quadro.']);
        }
    }

    /**
     * Processa a atualização em massa de tarefas.
     */
    public function bulkUpdate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $data = $this->request->getJSON(true);
        $taskIds = $data['task_ids'] ?? [];

        if (empty($taskIds)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nenhuma tarefa selecionada.']);
        }

        $dataToUpdate = [];
        // Adiciona user_id ao update apenas se um valor foi enviado
        if (isset($data['user_id']) && $data['user_id'] !== '') {
            $dataToUpdate['user_id'] = $data['user_id'] === '0' ? null : $data['user_id'];
        }
        // Adiciona due_date ao update apenas se um valor foi enviado
        if (isset($data['due_date']) && !empty($data['due_date'])) {
            $dataToUpdate['due_date'] = $data['due_date'];
        }

        if (empty($dataToUpdate)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nenhuma alteração foi especificada.']);
        }

        $taskModel = new TaskModel();
        if ($taskModel->whereIn('id', $taskIds)->set($dataToUpdate)->update()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Tarefas atualizadas com sucesso.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao atualizar as tarefas.']);
    }

    /**
     * Processa a exclusão em massa de tarefas.
     */
    public function bulkDelete()
    {
        $taskIds = $this->request->getPost('task_ids');

        if (empty($taskIds) || !is_array($taskIds)) {
            return redirect()->back()->with('error', 'Nenhuma tarefa foi selecionada para remoção.');
        }

        $taskModel = new TaskModel();
        $task = $taskModel->find($taskIds[0]); // Pega a primeira tarefa para saber o ID do projeto
        $projectId = $task->project_id ?? null;

        if ($taskModel->delete($taskIds)) {
            return redirect()->to('/admin/projects/' . $projectId)->with('success', count($taskIds) . ' tarefas foram removidas com sucesso.');
        }

        return redirect()->to('/admin/projects/' . $projectId)->with('error', 'Ocorreu um erro ao remover as tarefas.');
    }
}