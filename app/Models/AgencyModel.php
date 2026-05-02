<?php

namespace App\Models;

use CodeIgniter\Model;

class AgencyModel extends Model
{
    protected $table            = 'agencies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'contact_name',
        'email',
        'phone',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'name'         => 'required|min_length[3]|max_length[255]',
        'contact_name' => 'permit_empty|max_length[255]',
        'email'        => 'permit_empty|valid_email|max_length[128]',
        'phone'        => 'permit_empty|max_length[50]',
    ];
    protected $validationMessages   = [
        'name' => ['required' => 'O nome da agência é obrigatório.'],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Busca clientes vinculados à agência
     */
    public function getClients($agencyId)
    {
        $clientModel = new \App\Models\ClientModel();
        return $clientModel->where('agency_id', $agencyId)
                        ->orderBy('name', 'ASC')
                        ->findAll();
    }

    /**
     * Vincula um cliente à agência
     */
    public function linkClient($agencyId, $clientId)
    {
        $clientModel = new \App\Models\ClientModel();
        return $clientModel->update($clientId, ['agency_id' => $agencyId]);
    }

    /**
     * Desvincula um cliente da agência
     */
    public function unlinkClient($clientId)
    {
        $clientModel = new \App\Models\ClientModel();
        return $clientModel->update($clientId, ['agency_id' => null]);
    }
}