<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rota principal redireciona para a página de login
$routes->get('/', static fn () => redirect()->to('login'));

// Rotas de Autenticação (públicas)
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login/attempt', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// Rotas Protegidas (requerem login)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'dashboard']);

    // Rota para a busca global de projetos (para a navbar)
    $routes->get('projects/list-for-select', 'ProjectSearchController::listForSelect');

    // Rota para buscar tarefas do usuário que vencem em breve (para a sidebar)
    $routes->get('tasks/due-soon', 'TasksController::dueSoon');

    // Rotas de visualização de projetos para TODOS os usuários logados
    $routes->get('admin/projects', 'Admin\ProjectsController::index');
    $routes->get('admin/projects/(:num)', 'Admin\ProjectsController::show/$1');

    // Grupo de rotas de ADMINISTRAÇÃO (requer permissão de admin)
    $routes->group('admin', ['filter' => 'admin'], static function ($routes) {
        // Gerenciamento de Usuários
        $routes->resource('users', ['controller' => 'Admin\UsersController']);
        $routes->get('users/(:num)/delete', 'Admin\UsersController::delete/$1');

        // Gerenciamento de Tipos de Projeto (para IA)
        $routes->resource('project-type', ['controller' => 'Admin\ProjectTypeController']);
        $routes->get('project-type/(:num)/delete', 'Admin\ProjectTypeController::delete/$1');

        // Gerenciamento de Clientes
        $routes->resource('clients', ['controller' => 'Admin\ClientsController']);
        $routes->get('clients/(:num)/delete', 'Admin\ClientsController::delete/$1');

        // Gerenciamento de Projetos (criar, editar, deletar, gerenciar membros)
        $routes->resource('projects', ['controller' => 'Admin\ProjectsController', 'except' => ['index', 'show']]);
        $routes->get('projects/(:num)/delete', 'Admin\ProjectsController::delete/$1');
        $routes->post('projects/(:num)/users', 'Admin\ProjectsController::addUser/$1');
        $routes->post('projects/(:num)/users/(:num)/remove', 'Admin\ProjectsController::removeUser/$1/$2');

        // Rotas para Arquivos de Projeto
        $routes->post('projects/(:num)/files', 'Admin\ProjectFilesController::create/$1');
        $routes->get('files/(:num)/view', 'Admin\ProjectFilesController::view/$1');
        $routes->get('files/(:num)/download', 'Admin\ProjectFilesController::download/$1');
        $routes->post('files/(:num)/delete', 'Admin\ProjectFilesController::delete/$1');

        // Rotas para Relatórios de Projeto
        $routes->get('reports/available/(:num)', 'Admin\ReportsController::listAvailable/$1');
        $routes->post('projects/(:num)/reports/import', 'Admin\ReportsController::import/$1');
        $routes->get('reports/(:num)', 'Admin\ReportsController::show/$1');
        $routes->post('reports/(:num)/delete', 'Admin\ReportsController::delete/$1');

        // Gerenciamento de Tarefas
        $routes->post('projects/(:num)/tasks', 'Admin\TasksController::create/$1'); // Rota reativada para o modal específico do projeto
        $routes->post('tasks/create', 'Admin\TasksController::create'); // Nova rota global
        $routes->post('projects/(:num)/tasks/save-ai', 'Admin\TasksController::saveAIGeneratedTasks/$1');
        $routes->post('projects/(:num)/tasks/generate-ai', 'Admin\TasksController::generateWithAI/$1');
        $routes->post('tasks/update-board', 'Admin\TasksController::updateBoard');

        // Rotas para buscar, atualizar e deletar uma tarefa específica
        $routes->get('tasks/(:num)', 'Admin\TasksController::show/$1');
        $routes->post('tasks/(:num)/update', 'Admin\TasksController::update/$1');
        $routes->post('tasks/(:num)/delete', 'Admin\TasksController::delete/$1');

        // Gerenciamento de Notas de Tarefas
        $routes->post('tasks/(:num)/notes', 'Admin\TaskNotesController::create/$1');
        $routes->post('notes/(:num)/delete', 'Admin\TaskNotesController::delete/$1');

        // Rotas para ações em massa
        $routes->post('tasks/bulk-update', 'Admin\TasksController::bulkUpdate');
        $routes->post('tasks/bulk-delete', 'Admin\TasksController::bulkDelete');

        // Rota para AJAX (buscar membros de um projeto)
        $routes->get('projects/(:num)/members', 'Admin\ProjectsController::getProjectMembers/$1');
    });

    // Gerenciamento de Documentos de Projeto (requer apenas login, não admin)
    $routes->get('documents/(:num)', 'Admin\ProjectDocumentsController::show/$1', ['filter' => 'auth']);
    $routes->post('projects/(:num)/documents', 'Admin\ProjectDocumentsController::create/$1', ['filter' => 'auth']);
    $routes->post('documents/(:num)/update', 'Admin\ProjectDocumentsController::update/$1', ['filter' => 'auth']);
    $routes->post('documents/(:num)/delete', 'Admin\ProjectDocumentsController::delete/$1', ['filter' => 'auth']);
});
