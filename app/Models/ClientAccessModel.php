<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientAccessModel extends Model
{
    protected $table            = 'client_access';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['client_id', 'agency_id', 'token', 'password', 'last_used_at'];
    protected $useTimestamps   = true;
    protected $createdField    = 'created_at';
    protected $updatedField    = 'updated_at';

    protected $validationRules = [
        'id'        => 'permit_empty|is_natural_no_zero',
        'client_id' => 'permit_empty|is_natural_no_zero',
        'agency_id' => 'permit_empty|is_natural_no_zero',
        'token'     => 'required|max_length[64]',
        'password'  => 'required|max_length[255]',
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
