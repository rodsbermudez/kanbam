<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'start_date', 'end_date', 'client_id', 'status', 'is_visible_to_client', 'kanban_layout'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Retorna os projetos associados a um usuário específico, com opção de busca.
     */
    public function getProjectsForUser(int $userId, string $status = 'active', ?string $search = null)
    {
        $builder = $this->select('projects.*, clients.name as client_name, clients.tag as client_tag, clients.color as client_color, projects.status')
                        ->join('clients', 'clients.id = projects.client_id', 'left')
                        ->join('project_users', 'project_users.project_id = projects.id')
                        ->where('project_users.user_id', $userId);

        if ($search) {
            $builder->groupStart()
                    ->like('projects.name', $search)
                    ->orLike('projects.description', $search)
                    ->orLike('clients.name', $search)
                    ->groupEnd();
        }

        // Aplica o filtro de status e retorna os resultados
        return $builder->where('projects.status', $status)->findAll();
    }

    /**
     * Retorna a contagem de projetos por status.
     * @return array
     */
    public function getProjectCountsByStatus()
    {
        $counts = $this->select("
                            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                            SUM(CASE WHEN status = 'concluded' THEN 1 ELSE 0 END) as concluded
                        ")
                        ->where('deleted_at IS NULL')
                        ->get()
                        ->getRowArray();

        return [
            'active' => (int)($counts['active'] ?? 0),
            'concluded' => (int)($counts['concluded'] ?? 0),
        ];
    }

    /**
     * Retorna os projetos associados a um cliente específico.
     * @param int $clientId
     * @return array
     */
    public function getProjectsForClient(int $clientId)
    {
        return $this->where('client_id', $clientId)->orderBy('name', 'ASC')->findAll();
    }
}