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
    protected $allowedFields    = ['name', 'description', 'client_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Retorna os projetos associados a um usuário específico, com opção de busca.
     */
    public function getProjectsForUser(int $userId, ?string $search = null)
    {
        $builder = $this->select('projects.*, clients.name as client_name, clients.tag as client_tag, clients.color as client_color')
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

        // Garante que a ordenação e outras configurações do model sejam aplicadas
        return $builder->findAll();
    }
}