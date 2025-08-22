<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TaskModel;

class DashboardController extends BaseController
{
    /**
     * Exibe a página principal do dashboard do usuário.
     */
    public function index()
    {
        $taskModel = new TaskModel();
        $clientModel = new ClientModel();
        $userId = session()->get('user_id');
        $clientId = $this->request->getGet('client_id');

        $data = [
            'title'              => 'Dashboard',
            'clients'            => $clientModel->orderBy('name', 'ASC')->findAll(),
            'selected_client'    => $clientId ? $clientModel->find($clientId) : null,
            'upcoming_tasks'     => $taskModel->getUpcomingTasksForUser($userId, 7, $clientId),
            'overdue_tasks'      => $taskModel->getOverdueTasksForUser($userId, $clientId),
            'selected_client_id' => $clientId,
        ];

        return view('dashboard/index', $data);
    }
}