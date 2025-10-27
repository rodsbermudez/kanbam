<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\TaskModel;

class DashboardController extends BaseController
{
    /**
     * Exibe o dashboard principal do usuário.
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Se não houver usuário na sessão, redireciona para o login.
        // Isso é uma salvaguarda, o filtro de autenticação deve cuidar disso.
        if (!$userId) {
            return redirect()->to('/login');
        }

        $projectModel = new ProjectModel();
        $taskModel = new TaskModel();

        $data = [
            'title'          => 'Dashboard',
            'projects'       => $projectModel->getProjectsForUser($userId),
            'upcoming_tasks' => $taskModel->getUpcomingTasksForUser($userId, 7), // 7 dias por padrão
            'overdue_tasks'  => $taskModel->getOverdueTasksForUser($userId),
        ];

        // Pega o serviço de User Agent
        $agent = $this->request->getUserAgent();

        // Se for um dispositivo móvel, carrega a view otimizada
        if ($agent->isMobile()) {
            // Ajustado para o caminho correto da view mobile
            return view('dashboard/mobile', $data);
        }

        // Mantém a view de desktop para outros dispositivos
        // Ajustado para o caminho correto da view de desktop
        return view('dashboard/index', $data);
    }
}