<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table            = 'reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps   = false;
    protected $protectFields    = false;
    protected $allowedFields   = [
        'client_id',
        'title',
        'xml_content',
        'created_by',
        'created_at',
    ];

    protected $skipValidation = true;

    public function getReportsForClient($clientId)
    {
        return $this->where('client_id', $clientId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getReportsWithClient()
    {
        $clientModel = new ClientModel();
        
        $reports = $this->orderBy('created_at', 'DESC')->findAll();
        
        $clients = $clientModel->findAll();
        $clientsById = array_combine(array_map(fn($c) => $c->id, $clients), $clients);
        
        foreach ($reports as $report) {
            $report->client = $clientsById[$report->client_id] ?? null;
        }
        
        return $reports;
    }
}