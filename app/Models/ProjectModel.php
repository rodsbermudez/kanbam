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
    protected $allowedFields    = ['name', 'description'];

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
        $builder = $this->select('projects.*')
                        ->join('project_users', 'project_users.project_id = projects.id')
                        ->where('project_users.user_id', $userId);

        if ($search) {
            $builder->groupStart()
                    ->like('projects.name', $search)
                    ->orLike('projects.description', $search)
                    ->groupEnd();
        }

        // Garante que a ordenação e outras configurações do model sejam aplicadas
        return $builder->findAll();
    }
}