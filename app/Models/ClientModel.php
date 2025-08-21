<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'tag',
        'responsible_name',
        'responsible_email',
        'responsible_phone',
        'color'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id'   => 'permit_empty|is_natural_no_zero',
        'name' => 'required|min_length[3]|max_length[128]',
        'tag'  => 'required|min_length[2]|max_length[20]|is_unique[clients.tag,id,{id}]',
        'responsible_email' => 'permit_empty|valid_email|max_length[128]',
        'color' => 'permit_empty|max_length[7]',
    ];
    protected $validationMessages   = [
        'name' => ['required' => 'O nome do cliente é obrigatório.'],
        'tag'  => ['required' => 'A tag do cliente é obrigatória.', 'is_unique' => 'Esta tag já está em uso. Por favor, escolha outra.']
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}