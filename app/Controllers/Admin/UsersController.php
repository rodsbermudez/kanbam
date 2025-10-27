<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\ProjectUserModel;
use App\Models\TaskModel;
use App\Models\UserModel;

class UsersController extends BaseController
{
    /**
     * Exibe a lista de todos os usuários.
     */
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();

        $projectCounts = [];
        $openTaskCounts = [];

        if (!empty($users)) {
            $userIds = array_map(fn($u) => $u->id, $users);

            // 1. Busca a contagem de projetos por usuário
            $projectUserModel = new ProjectUserModel();
            $projectCountsResult = $projectUserModel->select('user_id, COUNT(id) as project_count')
                                                     ->whereIn('user_id', $userIds)
                                                     ->groupBy('user_id')
                                                     ->findAll();
            foreach ($projectCountsResult as $row) {
                $projectCounts[$row->user_id] = $row->project_count;
            }

            // 2. Busca a contagem de tarefas abertas por usuário
            $taskModel = new TaskModel();
            $openTaskCountsResult = $taskModel->select('user_id, COUNT(id) as task_count')
                                              ->whereIn('user_id', $userIds)
                                              ->whereNotIn('status', ['concluída', 'cancelada'])
                                              ->groupBy('user_id')
                                              ->findAll();
            foreach ($openTaskCountsResult as $row) {
                $openTaskCounts[$row->user_id] = $row->task_count;
            }
        }

        $data = [
            'title'            => 'Gerenciar Usuários',
            'users'            => $users,
            'project_counts'   => $projectCounts,
            'open_task_counts' => $openTaskCounts,
        ];

        // Pega o serviço de User Agent
        $agent = $this->request->getUserAgent();

        // Se for um dispositivo móvel, carrega a view otimizada
        if ($agent->isMobile()) {
            return view('admin/users/mobile', $data);
        }

        // Mantém a view de desktop para outros dispositivos
        return view('admin/users/index', $data);
    }

    /**
     * Exibe o formulário para criar um novo usuário.
     */
    public function new()
    {
        return view('admin/users/form', [
            'title' => 'Novo Usuário'
        ]);
    }

    /**
     * Processa a criação de um novo usuário.
     */
    public function create()
    {
        $userModel = new UserModel();

        $data = $this->request->getPost();

        if ($userModel->save($data)) {
            return redirect()->to('/admin/users')->with('success', 'Usuário criado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $userModel->errors());
    }

    /**
     * Exibe o formulário para editar um usuário existente.
     */
    public function edit($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        return view('admin/users/form', [
            'title' => 'Editar Usuário: ' . esc($user->name),
            'user'  => $user
        ]);
    }

    /**
     * Processa a atualização de um usuário.
     */
    public function update($id)
    {
        $userModel = new UserModel();
        $data = $this->request->getPost();

        // Adiciona o ID aos dados para que a regra de validação 'is_unique' funcione corretamente na atualização.
        $data['id'] = $id;

        // Se a senha estiver vazia, remova-a dos dados e relaxe a regra de validação
        if (empty($data['password'])) {
            unset($data['password']);
            $userModel->setValidationRule('password', 'permit_empty|min_length[8]');
        }

        // Garante que o checkbox desmarcado seja salvo como 0
        $data['is_admin'] = $data['is_admin'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? 0;

        if ($userModel->update($id, $data)) {
            return redirect()->to('/admin/users/' . $id)->with('success', 'Usuário atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $userModel->errors());
    }

    /**
     * Exibe os detalhes de um usuário específico, seus projetos e tarefas.
     */
    public function show($id)
    {
        helper('text');
        helper('user');
        $userModel = new UserModel();
        $projectModel = new ProjectModel();
        $taskModel = new TaskModel();

        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        $data = [
            'title'          => 'Detalhes do Usuário: ' . esc($user->name),
            'user'           => $user,
            'projects'       => $projectModel->getProjectsForUser($id),
            'upcoming_tasks' => $taskModel->getUpcomingTasksForUser($id),
            'overdue_tasks'  => $taskModel->getOverdueTasksForUser($id),
        ];

        // Pega o serviço de User Agent
        $agent = $this->request->getUserAgent();

        // Se for um dispositivo móvel, carrega a view otimizada
        if ($agent->isMobile()) {
            return view('admin/users/show_mobile', $data);
        }

        // Mantém a view de desktop para outros dispositivos
        return view('admin/users/show', $data);
    }

    /**
     * Deleta um usuário (soft delete).
     */
    public function delete($id)
    {
        $userModel = new UserModel();

        if ($userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'Usuário removido com sucesso.');
        }

        return redirect()->to('/admin/users')->with('error', 'Erro ao remover o usuário.');
    }
}