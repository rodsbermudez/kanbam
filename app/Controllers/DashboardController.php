<?php

namespace App\Controllers;

use App\Models\TaskModel;

class DashboardController extends BaseController
{
    /**
     * Exibe a página principal do dashboard do usuário.
     */
    public function index()
    {
        $taskModel = new TaskModel();
        $userId = session()->get('user_id');

        $data = [
            'title' => 'Dashboard',
            'upcoming_tasks' => $taskModel->getUpcomingTasksForUser($userId),
            'overdue_tasks'  => $taskModel->getOverdueTasksForUser($userId),
        ];

        return view('dashboard/index', $data);
    }
}