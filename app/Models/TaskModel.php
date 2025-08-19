<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table            = 'tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'project_id',
        'status',
        'title',
        'description',
        'user_id',
        'due_date',
        'order',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'project_id'  => 'required_without[id]|is_natural_no_zero',
        'title'       => 'required_without[id]|min_length[3]|max_length[255]',
        'description' => 'permit_empty',
        'user_id'     => 'permit_empty|is_natural_no_zero',
        'due_date'    => 'permit_empty|valid_date',
        'status'      => 'required|in_list[não iniciadas,em desenvovimento,aprovação,com cliente,ajustes,aprovada,implementada,concluída,cancelada]',
        'order'       => 'permit_empty|is_natural',
    ];

    /**
     * Busca tarefas atribuídas a um usuário específico que vencem nos próximos X dias.
     *
     * @param int $userId ID do usuário.
     * @param int $days   Número de dias no futuro para a verificação.
     * @return array      Lista de tarefas.
     */
    public function getTasksDueSoonForUser($userId, $days = 7)
    {
        $dateLimit = date('Y-m-d', strtotime("+$days days"));
        $today = date('Y-m-d');

        return $this->select('tasks.*, projects.name as project_name, projects.id as project_id_for_link')
                    ->join('projects', 'projects.id = tasks.project_id')
                    ->where('tasks.user_id', $userId)
                    ->where('tasks.due_date IS NOT NULL') // Apenas tarefas com data
                    ->where('tasks.due_date <=', $dateLimit)
                    ->whereNotIn('tasks.status', ['concluída', 'cancelada']) // Ignora tarefas finalizadas
                    ->orderBy('tasks.due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Busca tarefas atribuídas a um usuário que vencem nos próximos X dias (não inclui atrasadas).
     *
     * @param int $userId ID do usuário.
     * @param int $days   Número de dias no futuro para a verificação.
     * @return array      Lista de tarefas.
     */
    public function getUpcomingTasksForUser($userId, $days = 7)
    {
        $dateLimit = date('Y-m-d', strtotime("+$days days"));
        $today = date('Y-m-d');

        return $this->select('tasks.title, tasks.due_date, projects.name as project_name, projects.id as project_id')
                    ->join('projects', 'projects.id = tasks.project_id')
                    ->where('tasks.user_id', $userId)
                    ->where('tasks.due_date >=', $today)
                    ->where('tasks.due_date <=', $dateLimit)
                    ->whereNotIn('tasks.status', ['concluída', 'cancelada'])
                    ->orderBy('tasks.due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Busca tarefas atribuídas a um usuário que estão com a data de entrega vencida.
     *
     * @param int $userId ID do usuário.
     * @return array      Lista de tarefas.
     */
    public function getOverdueTasksForUser($userId)
    {
        $today = date('Y-m-d');

        return $this->select('tasks.title, tasks.due_date, projects.name as project_name, projects.id as project_id')
                    ->join('projects', 'projects.id = tasks.project_id')
                    ->where('tasks.user_id', $userId)
                    ->where('tasks.due_date <', $today)
                    ->whereNotIn('tasks.status', ['concluída', 'cancelada'])
                    ->orderBy('tasks.due_date', 'ASC')
                    ->findAll();
    }

    protected $validationMessages   = [
        'title' => [
            'required'   => 'O campo título é obrigatório.',
            'min_length' => 'O título da tarefa deve ter pelo menos 3 caracteres.'
        ],
        'status' => [
            'required' => 'O status inicial da tarefa é obrigatório.',
            'in_list'  => 'O status selecionado não é válido.'
        ]
    ];
    protected $skipValidation       = false;
}