<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\TaskModel;

class ExternalReportController extends BaseController
{
    /**
     * Gera um resumo diário de tarefas por usuário para ser consumido por serviços externos (ex: n8n).
     * 
     * O JSON de saída contém uma lista de usuários, e para cada um:
     * - Informações do usuário (incluindo slack_user_id).
     * - Tarefas com vencimento para hoje.
     * - Tarefas atrasadas (excluindo as que estão com o cliente).
     * - Tarefas com vencimento para os próximos 7 dias.
     */
    public function dailyUserSummary()
    {
        // Validação de segurança básica (ex: um token secreto na URL)
        $secretToken = $this->request->getGet('token');
        $expectedToken = getenv('N8N_SECRET_TOKEN');

        if (empty($expectedToken) || $secretToken !== $expectedToken) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não autorizado.']);
        }

        $userModel = new UserModel();
        $taskModel = new TaskModel();

        // Busca todos os usuários ativos que têm um ID do Slack configurado.
        $users = $userModel
            ->where('is_active', 1)
            ->where('slack_user_id IS NOT NULL')
            ->where('slack_user_id !=', '')
            ->findAll();

        $reportData = [];

        foreach ($users as $user) {
            // 1. Tarefas com vencimento para hoje
            $todayTasks = $taskModel->getTasksForDate($user->id, date('Y-m-d'));

            // 2. Tarefas atrasadas (excluindo as que estão com o cliente ou em aprovação)
            $overdueTasks = $taskModel->getFilteredOverdueTasks($user->id, ['com cliente', 'aprovação']);

            // 3. Tarefas para os próximos 7 dias (de amanhã em diante)
            $upcomingTasks = $taskModel->getUpcomingTasks($user->id, 7);

            // Adiciona ao relatório apenas se o usuário tiver alguma tarefa em qualquer uma das categorias
            if (!empty($todayTasks) || !empty($overdueTasks) || !empty($upcomingTasks)) {
                $reportData[] = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'slack_user_id' => $user->slack_user_id,
                    ],
                    'today_tasks' => $todayTasks,
                    'overdue_tasks' => $overdueTasks,
                    'upcoming_tasks' => $upcomingTasks,
                ];
            }
        }

        return $this->response->setJSON(['users' => $reportData]);
    }
}