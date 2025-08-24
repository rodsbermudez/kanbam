<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportedReportModel extends Model
{
    protected $table            = 'imported_reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id', 'original_report_id', 'url', 'target_keyword',
        'tech_stack', 'report_data', 'imported_by_user_id', 'original_created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}