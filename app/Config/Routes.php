<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- Rotas do Portal do Cliente ---
// A rota de login agora usa uma expressão regular para garantir que o token seja uma string hexadecimal de 64 caracteres,
// evitando conflito com outras rotas como 'portal/dashboard'.
$routes->get('portal/([a-f0-9]{64})', 'ClientPortalController::loginForm/$1');
$routes->post('portal/([a-f0-9]{64})/login', 'ClientPortalController::attemptLogin/$1');
$routes->get('portal/logout', 'ClientPortalController::logout');
$routes->group('portal', ['filter' => 'client_portal_auth'], function ($routes) {
    $routes->get('dashboard', 'ClientPortalController::dashboard');
    $routes->get('dashboard/(:num)', 'ClientPortalController::dashboard/$1');
    $routes->get('files/(:num)/download', 'ClientPortalController::downloadFile/$1');
});

// --- Rotas de Autenticação (Admin/Equipe) ---
$routes->get('/', static fn () => redirect()->to('login')); // Rota principal redireciona para a página de login

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
        $routes->post('clients/(:num)/enable-access', 'Admin\ClientsController::enableAccess/$1');
        $routes->post('clients/access/(:num)/regenerate-password', 'Admin\ClientsController::regeneratePassword/$1');
        $routes->post('clients/access/(:num)/delete', 'Admin\ClientsController::deleteAccess/$1');

        // Gerenciamento de Projetos (criar, editar, deletar, gerenciar membros)
        // Rotas específicas de projeto (devem vir antes do 'resource' para ter prioridade)
        $routes->get('projects/(:num)/delete', 'Admin\ProjectsController::delete/$1');
        $routes->post('projects/(:num)/toggle-status', 'Admin\ProjectsController::toggleStatus/$1');
        $routes->post('projects/(:num)/toggle-client-visibility', 'Admin\ProjectsController::toggleClientVisibility/$1');
        $routes->post('projects/(:num)/kanban-settings', 'Admin\ProjectsController::updateKanbanSettings/$1');
        $routes->post('projects/(:num)/users', 'Admin\ProjectsController::addUser/$1');
        $routes->post('projects/(:num)/documents/reorder', 'Admin\ProjectsController::reorderDocuments/$1');
        $routes->post('projects/(:num)/users/(:num)/remove', 'Admin\ProjectsController::removeUser/$1/$2');
        $routes->post('projects/(:num)/files', 'Admin\ProjectFilesController::create/$1');
        $routes->post('projects/(:num)/links', 'Admin\ProjectFilesController::createLink/$1');
        $routes->post('projects/(:num)/reports/import', 'Admin\ReportsController::import/$1');
        $routes->post('projects/(:num)/tasks', 'Admin\TasksController::create/$1');
        $routes->post('projects/(:num)/tasks/save-ai', 'Admin\TasksController::saveAIGeneratedTasks/$1');
        $routes->post('projects/(:num)/tasks/generate-ai', 'Admin\TasksController::generateWithAI/$1');
        // Rota genérica 'resource' para as ações padrão (new, create, edit, update)
        $routes->resource('projects', ['controller' => 'Admin\ProjectsController', 'except' => ['index', 'show']]);

        // Rotas para Arquivos de Projeto
        $routes->get('files/(:num)/view', 'Admin\ProjectFilesController::view/$1');
        $routes->get('files/(:num)/download', 'Admin\ProjectFilesController::download/$1');
        $routes->post('files/(:num)/delete', 'Admin\ProjectFilesController::delete/$1');
        $routes->post('files/(:num)/toggle-client-visibility', 'Admin\ProjectFilesController::toggleClientVisibility/$1');

        // Rotas para Relatórios de Projeto
        $routes->get('reports/available/(:num)', 'Admin\ReportsController::listAvailable/$1');
        $routes->get('reports/(:num)', 'Admin\ReportsController::show/$1');
        $routes->post('reports/(:num)/delete', 'Admin\ReportsController::delete/$1');

        // Gerenciamento de Tarefas
        $routes->post('tasks/create', 'Admin\TasksController::create'); // Nova rota global
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
