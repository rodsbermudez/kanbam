<?php

namespace App\Controllers;

use App\Models\ProjectModel;

class ProjectSearchController extends BaseController
{
    /**
     * Retorna uma lista de projetos formatada para o seletor global (Tom-select).
     * A lista inclui projetos que o usuário logado tem permissão para ver.
     * Este endpoint é público (para usuários logados) e não está sob o filtro de admin.
     */
    public function listForSelect()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $projectModel = new ProjectModel();
        $userId = session()->get('user_id');
        $isAdmin = session()->get('is_admin');
        $search = $this->request->getGet('search');

        $builder = $projectModel
            ->select('projects.id, projects.name, clients.tag as client_tag, clients.color as client_color')
            ->join('clients', 'clients.id = projects.client_id', 'left');

        if (!$isAdmin) {
            $builder->join('project_users', 'project_users.project_id = projects.id')
                    ->where('project_users.user_id', $userId);
        }

        // Adiciona a condição de busca se um termo foi enviado
        if ($search) {
            $builder->groupStart()
                    ->like('projects.name', $search)
                    ->orLike('clients.tag', $search)
                    ->groupEnd();
        }

        // Adiciona um groupBy para evitar projetos duplicados se um projeto tiver múltiplos usuários
        $projects = $builder->groupBy('projects.id')
                            ->orderBy('projects.name', 'ASC')
                            ->findAll();

        return $this->response->setJSON(['success' => true, 'projects' => $projects]);
    }
}