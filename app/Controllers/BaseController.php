<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['auth', 'user', 'version'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     *
     * @var array
     */
    protected $viewData = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();

        // Adiciona a versão da aplicação aos dados globais da view
        $this->viewData['app_version'] = get_latest_app_version();

        $this->loadGlobalData();
        \Config\Services::renderer()->setData($this->viewData);
    }

    /**
     * Carrega dados que precisam estar disponíveis globalmente em todas as views.
     *
     * @return void
     */
    protected function loadGlobalData()
    {
        if (session()->get('isLoggedIn')) {
            $projectModel = new \App\Models\ProjectModel();
            
            if (session()->get('is_admin')) {
                $this->viewData['global_projects'] = $projectModel->orderBy('name', 'ASC')->findAll();
            }

            $this->viewData['global_task_statuses'] = [
                'não iniciadas', 'em desenvovimento', 'aprovação', 'com cliente',
                'ajustes', 'aprovada', 'implementada', 'concluída', 'cancelada',
            ];

            // Carrega dados de tarefas para a sidebar
            $userId = session()->get('user_id');
            if ($userId) {
                $taskModel = new \App\Models\TaskModel();

                // Busca projetos ativos do usuário para filtrar as tarefas
                $active_projects = $projectModel->getProjectsForUser($userId);
                $active_project_ids = array_flip(array_map(fn($p) => $p->id, $active_projects));

                // Filtra tarefas futuras e atrasadas
                $upcoming_tasks = $taskModel->getUpcomingTasksForUser($userId, 7);
                $overdue_tasks = $taskModel->getOverdueTasksForUser($userId);
                
                $this->viewData['sidebar_upcoming'] = array_filter($upcoming_tasks, fn($task) => isset($active_project_ids[$task->project_id]));

                $active_overdue_tasks = array_filter($overdue_tasks, fn($task) => isset($active_project_ids[$task->project_id]));

                // Separa tarefas atrasadas em categorias
                $overdue_with_client = [];
                $overdue_other = [];

                foreach ($active_overdue_tasks as $task) {
                    if (in_array($task->status, ['com cliente', 'aprovação'])) {
                        $overdue_with_client[] = $task;
                    } else {
                        $overdue_other[] = $task;
                    }
                }

                $this->viewData['sidebar_overdue_other'] = $overdue_other;
                $this->viewData['sidebar_overdue_client'] = $overdue_with_client;
                $this->viewData['sidebar_overdue_count'] = count($overdue_other);
            }
        }
    }
}
