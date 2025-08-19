<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskNoteModel extends Model
{
    protected $table            = 'task_notes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['task_id', 'user_id', 'note'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'note' => 'required|max_length[180]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Busca notas de uma lista de tarefas, já incluindo os dados do usuário.
     */
    public function getNotesForProjectTasks(array $taskIds)
    {
        if (empty($taskIds)) {
            return [];
        }

        return $this->select('task_notes.*, users.name, users.initials, users.color')
                    ->join('users', 'users.id = task_notes.user_id')
                    ->whereIn('task_notes.task_id', $taskIds)
                    ->orderBy('task_notes.created_at', 'ASC') // ASC para pegar as mais antigas primeiro
                    ->findAll();
    }
}