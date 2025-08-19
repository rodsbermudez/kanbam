<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectUserModel extends Model
{
    protected $table            = 'project_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $protectFields    = true;
    protected $allowedFields    = ['project_id', 'user_id'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Verifica se um usuário é membro de um projeto específico.
     */
    public function isUserMemberOfProject(int $userId, int $projectId): bool
    {
        $result = $this->where('user_id', $userId)
                       ->where('project_id', $projectId)
                       ->first();

        return $result !== null;
    }
}