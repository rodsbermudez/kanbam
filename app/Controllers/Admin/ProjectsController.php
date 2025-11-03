<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\UserModel;
use App\Models\ProjectUserModel;
use App\Models\ClientModel;
use App\Models\TaskModel;
use App\Models\ProjectDocumentModel;
use App\Models\ProjectFileModel;
use App\Models\ImportedReportModel;
use App\Models\ProjectTypeModel;
use App\Models\TaskNoteModel;

class ProjectsController extends BaseController
{
    /**
     * Exibe a lista de projetos com filtro de busca.
     */
    public function index()
    {
        helper('text');
        helper('user');

        $projectModel = new ProjectModel();
        
        // Pega os parâmetros da URL
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status') ?? 'active';

        // Base query com join para incluir dados do cliente
        $projectModel->select('projects.*, clients.name as client_name, clients.tag as client_tag, clients.color as client_color, projects.status')
                     ->join('clients', 'clients.id = projects.client_id', 'left');

        // Aplica o filtro de status
        $projectModel->where('projects.status', $status);

        // Se for admin, busca todos os projetos. Senão, busca apenas os projetos do usuário.
        if (session()->get('is_admin')) {
            if ($search) {
                $projectModel->groupStart()
                             ->like('projects.name', $search)
                             ->orLike('projects.description', $search)
                             ->orLike('clients.name', $search)
                             ->groupEnd();
            }
            $projects = $projectModel->findAll();
        } else {
            $projects = $projectModel->getProjectsForUser(session()->get('user_id'), $status, $search);
        }

        $projectTaskStats = [];
        $projectMembers = [];

        if (!empty($projects)) {
            $projectIds = array_map(fn($p) => $p->id, $projects);

            // 1. Busca as estatísticas de tarefas (total, concluídas, atrasadas)
            $taskModel = new TaskModel();
            $taskStatsResult = $taskModel->select('
                project_id,
                COUNT(id) as total_tasks,
                SUM(CASE WHEN status = "concluída" THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN due_date < CURDATE() AND status NOT IN ("concluída", "cancelada") THEN 1 ELSE 0 END) as overdue_tasks
            ')
            ->whereIn('project_id', $projectIds)
            ->groupBy('project_id')
            ->findAll();

            // Cria um mapa de project_id => stats object
            foreach ($taskStatsResult as $row) {
                $projectTaskStats[$row->project_id] = $row;
            }

            // 2. Busca os membros dos projetos listados
            $userModel = new UserModel();
            $membersResult = $userModel->select('users.*, project_users.project_id')
                                       ->join('project_users', 'project_users.user_id = users.id')
                                       ->whereIn('project_users.project_id', $projectIds)
                                       ->findAll();
            // Cria um mapa de project_id => [user, user, ...]
            foreach ($membersResult as $member) {
                $projectMembers[$member->project_id][] = $member;
            }
        }

        // Busca a contagem de projetos para as abas
        $projectCounts = $projectModel->getProjectCountsByStatus();

        $data = [
            'title'            => 'Gerenciar Projetos',
            'projects'         => $projects,
            'search'           => $search,
            'project_task_stats' => $projectTaskStats,
            'project_members'  => $projectMembers,
            'current_status'   => $status,
            'project_counts'   => $projectCounts,
        ];

        // Pega o serviço de User Agent
        $agent = $this->request->getUserAgent();

        // Se for um dispositivo móvel, carrega a view otimizada
        if ($agent->isMobile()) {
            return view('admin/projects/mobile', $data);
        }

        return view('admin/projects/index', $data); // Mantém a view de desktop
    }

    /**
     * Exibe o formulário para criar um novo projeto.
     */
    public function new()
    {
        $clientModel = new ClientModel();
        return view('admin/projects/form', [
            'title'   => 'Novo Projeto',
            'clients' => $clientModel->orderBy('name', 'ASC')->findAll()
        ]);
    }

    /**
     * Processa a criação de um novo projeto.
     */
    public function create()
    {
        $projectModel = new ProjectModel();
        $data = $this->request->getPost();

        // Garante que um client_id vazio seja salvo como NULL
        if (empty($data['client_id'])) {
            $data['client_id'] = null;
        }

        if ($projectModel->save($data)) {
            $projectId = $projectModel->getInsertID();

            // Busca o projeto recém-criado com os dados do cliente para um payload mais completo
            $newProject = $projectModel
                ->select('projects.*, clients.name as client_name')
                ->join('clients', 'clients.id = projects.client_id', 'left')
                ->find($projectId);

            // Dispara o Webhook para o N8N
            dispararWebhookN8N('projeto-criado', [
                'event'   => 'project.created',
                'project' => $newProject, // Envia o objeto completo do projeto
                'actor'   => [
                    'id'   => session()->get('user_id'),
                    'name' => session()->get('name')
                ]
            ]);

            // Redireciona para a página de visualização do novo projeto
            return redirect()->to('/admin/projects/' . $projectId)->with('success', 'Projeto criado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $projectModel->errors());
    }

    /**
     * Exibe o dashboard de um projeto específico com a lista de membros.
     */
    public function show($id = null)
    {
        helper('text');

        $projectModel = new ProjectModel();
        $userModel = new UserModel();
        
        $project = $projectModel
            ->select('projects.*, clients.name as client_name, clients.tag as client_tag, clients.color as client_color, projects.status, projects.is_visible_to_client')
            ->join('clients', 'clients.id = projects.client_id', 'left')
            ->find($id);

        if (!$project) {
            return redirect()->to('/admin/projects')->with('error', 'Projeto não encontrado.');
        }

        // Verifica a permissão de acesso para usuários não-administradores
        if (!session()->get('is_admin')) {
            $projectUserModel = new ProjectUserModel();
            if (!$projectUserModel->isUserMemberOfProject(session()->get('user_id'), $id)) {
                return redirect()->to('/admin/projects')
                                 ->with('error', 'Você não tem permissão para acessar este projeto.');
            }
        }

        $assigned_users = $userModel->getUsersForProject($id);

        // Cria um mapa de usuários por ID para fácil acesso na view
        $usersById = [];
        foreach ($assigned_users as $user) {
            $usersById[$user->id] = $user;
        }

        $taskModel = new TaskModel();

        $statuses = [
            'não iniciadas',
            'em desenvovimento',
            'aprovação',
            'com cliente',
            'ajustes',
            'aprovada',
            'implementada',
            'concluída',
            'cancelada',
        ];

        // Ordena as tarefas: primeiro as com data (da mais antiga para a mais nova),
        // depois as sem data. A ordenação manual ('order') é usada como critério de desempate.
        $tasks = $taskModel->where('project_id', $id)
                           // O terceiro parâmetro `false` impede o CodeIgniter de escapar a expressão,
                           // corrigindo o erro de sintaxe SQL.
                           ->orderBy('due_date IS NULL', 'ASC', false)
                           ->orderBy('due_date', 'ASC')
                           ->orderBy('order', 'ASC') // Mantém a ordem manual como desempate
                           ->findAll();

        // Busca todas as notas de uma vez para otimizar as queries
        $taskIds = array_map(fn($task) => $task->id, $tasks);
        $noteModel = new TaskNoteModel();
        $allNotes = [];
        if (!empty($taskIds)) {
            $allNotes = $noteModel->select('task_notes.*, users.name')
                                  ->join('users', 'users.id = task_notes.user_id', 'left')
                                  ->whereIn('task_id', $taskIds)
                                  ->orderBy('task_notes.created_at', 'ASC')
                                  ->findAll();
        }

        // Agrupa as notas por ID da tarefa
        $notesByTaskId = [];
        foreach ($allNotes as $note) {
            $notesByTaskId[$note->task_id][] = $note;
        }

        // --- Lógica para o Cronograma Semanal ---
        // Garante que todas as tarefas, incluindo as concluídas, sejam consideradas para o cronograma.
        $weekly_tasks = [];
        foreach ($tasks as $task) {
            if (!empty($task->due_date)) {
                $weekly_tasks[] = $task;
            }
        }
        // Reordena por data, pois o array original pode ter tarefas sem data no início
        usort($weekly_tasks, fn($a, $b) => strcmp($a->due_date, $b->due_date));

        $weekly_schedule = [];
        if (!empty($weekly_tasks)) {
            $month_formatter = new \IntlDateFormatter('pt_BR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, null, null, 'MMMM \'de\' yyyy');

            foreach ($weekly_tasks as $task) {
                $date = new \DateTime($task->due_date);
                
                // Chave e rótulo do mês
                $month_key = $date->format('Y-m');
                $month_label = ucfirst($month_formatter->format($date));

                // Calcula o início (Segunda) e fim (Domingo) da semana
                $day_of_week = (int)$date->format('N'); // 1 (Seg) a 7 (Dom)
                $start_of_week = (clone $date)->modify('-' . ($day_of_week - 1) . ' days');
                $end_of_week = (clone $start_of_week)->modify('+6 days');
                
                // Chave e rótulo da semana
                $week_key = $start_of_week->format('Y-m-d');
                $week_label = "Semana de " . $start_of_week->format('d/m') . " a " . $end_of_week->format('d/m');

                // Agrupa os dados
                if (!isset($weekly_schedule[$month_key])) {
                    $weekly_schedule[$month_key] = ['label' => $month_label, 'weeks' => []];
                }
                if (!isset($weekly_schedule[$month_key]['weeks'][$week_key])) {
                    $weekly_schedule[$month_key]['weeks'][$week_key] = ['label' => $week_label, 'items' => []];
                }
                $weekly_schedule[$month_key]['weeks'][$week_key]['items'][] = $task;
            }
        }

        // Determina a chave da semana atual para destacar na view
        $today = new \DateTime();
        $day_of_week_today = (int)$today->format('N'); // 1 (Seg) a 7 (Dom)
        $start_of_current_week = (clone $today)->modify('-' . ($day_of_week_today - 1) . ' days');
        $current_week_key = $start_of_current_week->format('Y-m-d');
        // --- Fim da Lógica para o Cronograma Semanal ---

        // Busca os arquivos do projeto
        $fileModel = new ProjectFileModel();
        $project_files = $fileModel->select('project_files.*, users.name as uploader_name')
                           ->join('users', 'users.id = project_files.user_id')
                           ->where('project_files.project_id', $id)
                           ->orderBy('project_files.created_at', 'DESC')->findAll();

        // Busca os relatórios importados para o projeto
        $importedReportModel = new ImportedReportModel();
        $imported_reports = $importedReportModel->select('imported_reports.*, users.name as importer_name')
                                                ->join('users', 'users.id = imported_reports.imported_by_user_id')
                                                ->where('imported_reports.project_id', $id)
                                                ->orderBy('imported_reports.original_created_at', 'DESC')->findAll();

        // Pega a aba ativa da sessão ou da URL (URL tem prioridade)
        // E o documento a ser selecionado
        $activeTab = $this->request->getGet('active_tab') ?? session()->get('active_tab') ?? 'board';
        $selectDocId = $this->request->getGet('select_doc') ?? null;

        // Busca os documentos do projeto
        $docModel = new ProjectDocumentModel();
        $documents = $docModel->where('project_id', $id)->orderBy('order', 'ASC')->orderBy('title', 'ASC')->findAll();

        // Agrupa as tarefas por status para o quadro Kanban
        $groupedTasks = [];
        foreach ($statuses as $status) {
            $groupedTasks[$status] = [];
        }
        foreach ($tasks as $task) {
            if (array_key_exists($task->status, $groupedTasks)) {
                $groupedTasks[$task->status][] = $task;
            }
        }

        $data = [
            'title'           => 'Dashboard: ' . esc($project->name),
            'project'         => $project,
            'assigned_users'  => $assigned_users,
            'available_users' => $userModel->getUsersNotInProject($id),
            'statuses'        => $statuses,
            'tasks'           => $groupedTasks,
            'users_by_id'     => $usersById,
            'notes_by_task_id' => $notesByTaskId,
            'documents'       => $documents,
            'project_files'   => $project_files,
            'weekly_schedule' => $weekly_schedule,
            'current_week_key' => $current_week_key,
            'imported_reports' => $imported_reports,
            // Adiciona os tipos de projeto para o modal de IA
            'project_types'   => (new ProjectTypeModel())->findAll(),
            'active_tab'      => $activeTab,
            'select_doc_id'   => $selectDocId,
        ];

        // Pega o serviço de User Agent
        $agent = $this->request->getUserAgent();

        // Se for um dispositivo móvel, carrega a view otimizada
        if ($agent->isMobile()) {
            return view('admin/projects/show_mobile', $data);
        }

        return view('admin/projects/show', $data); // Mantém a view de desktop
    }

    /**
     * Exibe o formulário para editar um projeto.
     */
    public function edit($id)
    {
        $projectModel = new ProjectModel();
        $project = $projectModel->find($id);

        if (!$project) {
            return redirect()->to('/admin/projects')->with('error', 'Projeto não encontrado.');
        }

        $clientModel = new ClientModel();

        return view('admin/projects/form', [
            'title'   => 'Editar Projeto: ' . esc($project->name),
            'project' => $project,
            'clients' => $clientModel->orderBy('name', 'ASC')->findAll()
        ]);
    }

    /**
     * Processa a atualização de um projeto.
     */
    public function update($id)
    {
        $projectModel = new ProjectModel();
        $data = $this->request->getPost();

        // Garante que um client_id vazio seja salvo como NULL
        if (empty($data['client_id'])) {
            $data['client_id'] = null;
        }

        if ($projectModel->update($id, $data)) {
            return redirect()->to('/admin/projects')->with('success', 'Projeto atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $projectModel->errors());
    }

    /**
     * Deleta (soft delete) um projeto e todos os seus dados associados (tarefas, notas, membros).
     * A operação é executada dentro de uma transação para garantir a integridade dos dados.
     */
    public function delete($id)
    {
        $projectModel = new ProjectModel();
        $taskModel = new TaskModel();
        $noteModel = new TaskNoteModel();
        $projectUserModel = new ProjectUserModel();
        $db = \Config\Database::connect();

        // Inicia a transação
        $db->transStart();

        // 1. Encontra as tarefas do projeto
        $tasks = $taskModel->where('project_id', $id)->findAll();
        
        if (!empty($tasks)) {
            $taskIds = array_column($tasks, 'id');

            // 2. Remove as notas associadas às tarefas (hard delete)
            if (!empty($taskIds)) {
                $noteModel->whereIn('task_id', $taskIds)->delete();
            }

            // 3. Remove (soft delete) as tarefas
            $taskModel->whereIn('id', $taskIds)->delete();
        }

        // 4. Remove os membros associados ao projeto (hard delete)
        $projectUserModel->where('project_id', $id)->delete();

        // 5. Remove (soft delete) o projeto
        $projectModel->delete($id);

        // Finaliza a transação
        $db->transComplete();

        if ($db->transStatus() === false) {
            // Se a transação falhou
            return redirect()->to('/admin/projects')->with('error', 'Erro ao remover o projeto e seus dados.');
        }

        return redirect()->to('/admin/projects')->with('success', 'Projeto e todos os seus dados foram removidos com sucesso.');
    }

    /**
     * Reordena os documentos de um projeto via AJAX.
     */
    public function reorderDocuments($projectId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $orderedIds = $this->request->getJSON(true)['doc_ids'] ?? [];

        if (empty($orderedIds)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Nenhuma ordem recebida.']);
        }

        $docModel = new ProjectDocumentModel();
        
        $updateData = [];
        foreach ($orderedIds as $index => $docId) {
            $updateData[] = [
                'id'    => (int)$docId,
                'order' => $index
            ];
        }

        if ($docModel->updateBatch($updateData, 'id')) {
            return $this->response->setJSON(['success' => true, 'message' => 'Ordem das páginas salva com sucesso.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao salvar a nova ordem.']);
    }

    /**
     * Atualiza as configurações de layout do Kanban para um projeto via AJAX.
     */
    public function updateKanbanSettings($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $projectModel = new ProjectModel();
        $layout = $this->request->getJsonVar('layout');

        // Validação simples
        if (!in_array($layout, ['normal', 'simplified'])) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Layout inválido.']);
        }

        $data = ['kanban_layout' => $layout];

        if ($projectModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Layout do quadro atualizado.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao salvar o layout do quadro.']);
    }

    /**
     * Associa um usuário a um projeto.
     */
    public function addUser($projectId)
    {
        $projectUserModel = new ProjectUserModel();
        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return redirect()->to('/admin/projects/' . $projectId)->with('error', 'Nenhum usuário selecionado.')->with('active_tab', 'members');
        }

        // Verifica se a associação já existe
        $exists = $projectUserModel->where('project_id', $projectId)->where('user_id', $userId)->first();

        if (!$exists) {
            $data = ['project_id' => $projectId, 'user_id' => $userId];
            if ($projectUserModel->save($data)) {
                // Dispara o Webhook para o N8N
                $project = (new ProjectModel())->find($projectId);
                $user = (new UserModel())->find($userId);

                dispararWebhookN8N('novo-usuario-projeto', [
                    'event'   => 'user.added.to.project',
                    'project' => ['id' => $project->id, 'name' => $project->name],
                    'user'    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                    'actor'   => ['id' => session()->get('user_id'), 'name' => session()->get('name')]
                ]);

                return redirect()->to('/admin/projects/' . $projectId)->with('success', 'Usuário associado com sucesso.')->with('active_tab', 'members');
            }
            if ($projectUserModel->save($data)) {
                return redirect()->to('/admin/projects/' . $projectId)->with('success', 'Usuário associado com sucesso.')->with('active_tab', 'members');
            }
        }

        return redirect()->to('/admin/projects/' . $projectId)->with('error', 'Não foi possível associar o usuário.')->with('active_tab', 'members');
    }

    /**
     * Remove a associação de um usuário com um projeto.
     */
    public function removeUser($projectId, $userId)
    {
        $projectUserModel = new ProjectUserModel();
        $db = \Config\Database::connect();

        // Encontra o registro da associação para garantir que ele existe.
        $association = $projectUserModel->where('project_id', $projectId)
                                        ->where('user_id', $userId)
                                        ->first();

        // Se a associação existir, tenta removê-la e verifica se a linha foi afetada.
        if ($association) {
            $db->table($projectUserModel->getTable())->where('id', $association->id)->delete();

            if ($db->affectedRows() > 0) {
                return redirect()->to('/admin/projects/' . $projectId)->with('success', 'Usuário desassociado com sucesso.')->with('active_tab', 'members');
            }
        }

        // Se não encontrou a associação ou a exclusão falhou (0 linhas afetadas).
        return redirect()->to('/admin/projects/' . $projectId)->with('error', 'Não foi possível desassociar o usuário.')->with('active_tab', 'members');
    }

    /**
     * Retorna os membros de um projeto em formato JSON para chamadas AJAX.
     */
    public function getProjectMembers($projectId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userModel = new UserModel();
        $members = $userModel->getUsersForProject($projectId);

        return $this->response->setJSON(['success' => true, 'members' => $members]);
    }

    /**
     * Alterna o status de um projeto entre 'active' e 'concluded'.
     */
    public function toggleStatus($id)
    {
        $projectModel = new ProjectModel();
        $project = $projectModel->find($id);

        if (!$project) {
            return redirect()->back()->with('error', 'Projeto não encontrado.');
        }

        $newStatus = ($project->status === 'active') ? 'concluded' : 'active';

        if ($projectModel->update($id, ['status' => $newStatus])) {
            $message = ($newStatus === 'concluded') ? 'Projeto marcado como concluído.' : 'Projeto reativado.';
            return redirect()->to('admin/projects/' . $id)->with('success', $message);
        }

        return redirect()->to('admin/projects/' . $id)->with('error', 'Erro ao alterar o status do projeto.');
    }

    /**
     * Alterna a visibilidade de um projeto no portal do cliente via AJAX.
     */
    public function toggleClientVisibility($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $projectModel = new ProjectModel();
        $project = $projectModel->find($id);

        $newVisibility = !(bool)($project->is_visible_to_client ?? true);

        if ($projectModel->update($id, ['is_visible_to_client' => $newVisibility])) {
            return $this->response->setJSON(['success' => true, 'new_visibility' => $newVisibility]);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao atualizar a visibilidade do projeto.']);
    }
}