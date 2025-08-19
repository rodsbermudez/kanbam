<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\UserModel;

class TasksController extends BaseController
{
    /**
     * Busca e retorna as tarefas do usuário logado que vencem em breve.
     * Retorna uma view parcial renderizada em HTML.
     */
    public function dueSoon()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuário não autenticado.']);
        }

        $taskModel = new TaskModel();
        $tasks = $taskModel->getTasksDueSoonForUser($userId);

        // Precisamos dos dados de todos os usuários para exibir os ícones corretamente
        $users = (new UserModel())->findAll();
        $usersById = array_column($users, null, 'id');

        // Renderiza uma view parcial com a lista de cards
        $html = view('partials/task_card_list', [
            'tasks' => $tasks,
            'users_by_id' => $usersById,
        ]);

        return $this->response->setJSON(['success' => true, 'html' => $html, 'count' => count($tasks)]);
    }
}