<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\ProjectUserModel;
use App\Models\TaskModel;
use App\Models\ProjectDocumentModel;
use App\Models\ProjectTypeModel;
use App\Models\TaskNoteModel;
use App\Models\UserModel;

class ProjectsController extends BaseController
{
    /**
     * Exibe a lista de projetos com filtro de busca.
     */
    public function index()
    {
        helper('text');

        $projectModel = new ProjectModel();
        $search = $this->request->getGet('search');

        // Se for admin, busca todos os projetos. Senão, busca apenas os projetos do usuário.
        if (session()->get('is_admin')) {
            if ($search) {
                $projectModel->like('name', $search)
                             ->orLike('description', $search);
            }
            $projects = $projectModel->findAll();
        } else {
            $projects = $projectModel->getProjectsForUser(session()->get('user_id'), $search);
        }

        $data = [
            'title'    => 'Gerenciar Projetos',
            'projects' => $projects,
            'search'   => $search,
        ];

        return view('admin/projects/index', $data);
    }

    /**
     * Exibe o formulário para criar um novo projeto.
     */
    public function new()
    {
        return view('admin/projects/form', [
            'title' => 'Novo Projeto'
        ]);
    }

    /**
     * Processa a criação de um novo projeto.
     */
    public function create()
    {
        $projectModel = new ProjectModel();

        if ($projectModel->save($this->request->getPost())) {
            return redirect()->to('/admin/projects')->with('success', 'Projeto criado com sucesso.');
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

        $project = $projectModel->find($id);

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
        $allNotes = $noteModel->getNotesForProjectTasks($taskIds);

        // Agrupa as notas por ID da tarefa
        $notesByTaskId = [];
        foreach ($allNotes as $note) {
            $notesByTaskId[$note->task_id][] = $note;
        }

        // Pega a aba ativa da sessão ou da URL (URL tem prioridade)
        // E o documento a ser selecionado
        $activeTab = $this->request->getGet('active_tab') ?? session()->get('active_tab') ?? 'board';
        $selectDocId = $this->request->getGet('select_doc') ?? null;

        // Busca os documentos do projeto
        $docModel = new ProjectDocumentModel();
        $documents = $docModel->where('project_id', $id)->orderBy('title', 'ASC')->findAll();

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

        $assigned_users = $userModel->getUsersForProject($id);

        // Cria um mapa de usuários por ID para fácil acesso na view
        $usersById = [];
        foreach ($assigned_users as $user) {
            $usersById[$user->id] = $user;
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
            // Adiciona os tipos de projeto para o modal de IA
            'project_types'   => (new ProjectTypeModel())->findAll(),
            'active_tab'      => $activeTab,
            'select_doc_id'   => $selectDocId,
        ];

        return view('admin/projects/show', $data);
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

        return view('admin/projects/form', [
            'title'   => 'Editar Projeto: ' . esc($project->name),
            'project' => $project
        ]);
    }

    /**
     * Processa a atualização de um projeto.
     */
    public function update($id)
    {
        $projectModel = new ProjectModel();

        if ($projectModel->update($id, $this->request->getPost())) {
            return redirect()->to('/admin/projects')->with('success', 'Projeto atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $projectModel->errors());
    }

    /**
     * Deleta (soft delete) um projeto.
     */
    public function delete($id)
    {
        $projectModel = new ProjectModel();
        if ($projectModel->delete($id)) {
            return redirect()->to('/admin/projects')->with('success', 'Projeto removido com sucesso.');
        }
        return redirect()->to('/admin/projects')->with('error', 'Erro ao remover o projeto.');
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
}