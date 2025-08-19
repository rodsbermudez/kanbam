<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = ['name', 'initials', 'color', 'email', 'password', 'is_admin', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'name'     => 'required|min_length[3]|max_length[255]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Retorna todos os usuários que estão associados a um projeto específico.
     */
    public function getUsersForProject(int $projectId)
    {
        return $this->select('users.*')
                    ->join('project_users', 'project_users.user_id = users.id')
                    ->where('project_users.project_id', $projectId)
                    ->findAll();
    }

    /**
     * Retorna todos os usuários que NÃO estão associados a um projeto específico.
     */
    public function getUsersNotInProject(int $projectId)
    {
        $subQuery = $this->db->table('project_users')
                             ->select('user_id')
                             ->where('project_id', $projectId);

        return $this->whereNotIn('id', $subQuery)
                    ->where('is_active', 1) // Sugestão: listar apenas usuários ativos
                    ->findAll();
    }
}