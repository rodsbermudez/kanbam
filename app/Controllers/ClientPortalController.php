<?php

namespace App\Controllers;

use App\Models\ClientAccessModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ClientModel;

class ClientPortalController extends BaseController
{
    public function loginForm($token)
    {
        $clientAccessModel = new ClientAccessModel();
        $access = $clientAccessModel->where('token', $token)->first();

        if (!$access) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Página de acesso não encontrada ou inválida.');
        }

        return view('client_portal/login', [
            'title' => 'Portal do Cliente',
            'token' => $token
        ]);
    }

    public function attemptLogin($token)
    {
        $password = $this->request->getPost('password');
        if (empty($password)) {
            return redirect()->back()->with('error', 'O campo senha é obrigatório.');
        }

        $clientAccessModel = new ClientAccessModel();
        $access = $clientAccessModel->where('token', $token)->first();

        if (!$access || !password_verify($password, $access->password)) {
            return redirect()->back()->with('error', 'Senha inválida.');
        }

        // Login bem-sucedido
        $clientModel = new ClientModel();
        $client = $clientModel->find($access->client_id);

        $sessionData = [
            'client_portal_access_id'   => $access->id,
            'client_portal_client_id'   => $access->client_id,
            'client_portal_client_name' => $client->name,
            'client_portal_token'       => $token,
            'is_client_portal_logged_in' => true,
        ];
        session()->set($sessionData);

        // Atualiza o último acesso
        $clientAccessModel->update($access->id, ['last_used_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('/portal/dashboard');
    }

    public function dashboard($selectedProjectId = null)
    {
        helper('text');
        helper('user');

        $clientId = session()->get('client_portal_client_id');

        $projectModel = new ProjectModel();
        $projects = $projectModel->where('client_id', $clientId)
                                  ->where('is_visible_to_client', 1) // Apenas projetos visíveis
                                  ->orderBy('name', 'ASC')
                                  ->findAll();

        if (empty($projects)) {
            return view('client_portal/dashboard', [
                'title' => 'Portal do Cliente',
                'projects' => [],
                'selected_project' => null,
                'weekly_schedule' => [],
            ]);
        }

        if ($selectedProjectId === null) {
            $selectedProjectId = $projects[0]->id;
        }

        $selectedProject = $projectModel->find($selectedProjectId);

        // Valida se o projeto existe, pertence ao cliente e está marcado como visível.
        if (!$selectedProject || $selectedProject->client_id != $clientId || !$selectedProject->is_visible_to_client) {
            return redirect()->to('/portal/dashboard')->with('error', 'Projeto inválido ou não acessível.');
        }

        // Lógica do Cronograma Semanal (reutilizada)
        // Busca apenas as tarefas com data de entrega para o cronograma, incluindo as concluídas.
        $taskModel = new TaskModel();
        $weekly_tasks = $taskModel->where('project_id', $selectedProjectId)
                                  ->where('due_date IS NOT NULL')
                                  ->orderBy('due_date', 'ASC')
                                  ->findAll();
        $weekly_schedule = [];
        if (!empty($weekly_tasks)) {
            $month_formatter = new \IntlDateFormatter('pt_BR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, null, null, 'MMMM \'de\' yyyy');
            foreach ($weekly_tasks as $task) {
                $date = new \DateTime($task->due_date);
                $month_key = $date->format('Y-m');
                $month_label = ucfirst($month_formatter->format($date));
                $day_of_week = (int)$date->format('N');
                $start_of_week = (clone $date)->modify('-' . ($day_of_week - 1) . ' days');
                $end_of_week = (clone $start_of_week)->modify('+6 days');
                $week_key = $start_of_week->format('Y-m-d');
                $week_label = "Semana de " . $start_of_week->format('d/m') . " a " . $end_of_week->format('d/m');
                if (!isset($weekly_schedule[$month_key])) {
                    $weekly_schedule[$month_key] = ['label' => $month_label, 'weeks' => []];
                }
                if (!isset($weekly_schedule[$month_key]['weeks'][$week_key])) {
                    $weekly_schedule[$month_key]['weeks'][$week_key] = ['label' => $week_label, 'items' => []];
                }
                $weekly_schedule[$month_key]['weeks'][$week_key]['items'][] = $task;
            }
        }
        $today = new \DateTime();
        $day_of_week_today = (int)$today->format('N');
        $start_of_current_week = (clone $today)->modify('-' . ($day_of_week_today - 1) . ' days');
        $current_week_key = $start_of_current_week->format('Y-m-d');

        $data = [
            'title'            => 'Portal: ' . esc($selectedProject->name),
            'projects'         => $projects,
            'selected_project' => $selectedProject,
            'weekly_schedule'  => $weekly_schedule,
            'current_week_key' => $current_week_key,
        ];

        return view('client_portal/dashboard', $data);
    }

    public function logout()
    {
        $token = session()->get('client_portal_token');
        session()->destroy();
        return redirect()->to('/portal/' . $token)->with('success', 'Você saiu com segurança.');
    }
}
