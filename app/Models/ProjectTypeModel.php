<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectTypeModel extends Model
{
    protected $table            = 'project_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'prompt', 'label_description', 'placeholder_description', 'label_items', 'placeholder_items'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name'   => 'required|min_length[3]|max_length[255]',
        'prompt' => 'required',
        'label_description' => 'permit_empty|max_length[255]',
        'label_items' => 'permit_empty|max_length[255]',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'O nome do tipo de projeto é obrigatório.',
            'is_unique' => 'Este nome de tipo de projeto já está em uso.'
        ],
        'prompt' => [
            'required' => 'O prompt para a IA é obrigatório.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}