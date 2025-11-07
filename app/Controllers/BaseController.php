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
        if (session()->get('isLoggedIn') && session()->get('is_admin')) {
            $projectModel = new \App\Models\ProjectModel();
            $this->viewData['global_projects'] = $projectModel->orderBy('name', 'ASC')->findAll();

            $this->viewData['global_task_statuses'] = [
                'não iniciadas', 'em desenvovimento', 'aprovação', 'com cliente',
                'ajustes', 'aprovada', 'implementada', 'concluída', 'cancelada',
            ];
        }
    }
}
