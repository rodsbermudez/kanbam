<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\TaskNoteModel;
use App\Models\UserModel;

class ApiController extends BaseController
{
    private const API_TOKEN = 'kanbam-api-token-secret-2024';

    private function authenticate(): bool
    {
        $token = $this->request->getHeader('X-API-Token');
        if (!$token) {
            return false;
        }
        return $token->getValue() === self::API_TOKEN;
    }

    private function jsonResponse(array $data, int $statusCode = 200)
    {
        return $this->response->setStatusCode($statusCode)
                             ->setJSON($data);
    }

    private function jsonError(string $message, int $statusCode = 400)
    {
        return $this->jsonResponse(['error' => $message], $statusCode);
    }

    public function clients()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $clientModel = new ClientModel();
        $clients = $clientModel->select('id, name, tag, responsible_name, responsible_email, color, created_at')
                             ->orderBy('name', 'ASC')
                             ->findAll();

        return $this->jsonResponse(['clients' => $clients]);
    }

    public function users()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $userModel = new UserModel();
        $users = $userModel->select('id, name, initials, color, is_admin')
                         ->where('is_active', 1)
                         ->orderBy('name', 'ASC')
                         ->findAll();

        return $this->jsonResponse(['users' => $users]);
    }

    public function user($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $userModel = new UserModel();
        $user = $userModel->select('id, name, initials, color, is_admin')
                         ->find($id);

        if (!$user) {
            return $this->jsonError('User not found', 404);
        }

        return $this->jsonResponse(['user' => $user]);
    }

    public function projects()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $projectModel = new ProjectModel();
        $builder = $projectModel->select('projects.id, projects.name, projects.status, projects.is_visible_to_client, projects.client_id, clients.name as client_name, clients.tag as client_tag')
                                ->join('clients', 'clients.id = projects.client_id', 'left');

        $clientId = $this->request->getGet('client_id');
        $status = $this->request->getGet('status');

        if ($clientId) {
            $builder->where('projects.client_id', $clientId);
        }

        if ($status) {
            $builder->where('projects.status', $status);
        }

        $projects = $builder->orderBy('projects.name', 'ASC')->findAll();

        return $this->jsonResponse(['projects' => $projects]);
    }

    public function client($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $clientModel = new ClientModel();
        $client = $clientModel->find($id);

        if (!$client) {
            return $this->jsonError('Client not found', 404);
        }

        return $this->jsonResponse(['client' => $client]);
    }

    public function clientByTag($tag)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $clientModel = new ClientModel();
        $client = $clientModel->where('tag', strtoupper($tag))->first();

        if (!$client) {
            return $this->jsonError('Client not found', 404);
        }

        return $this->jsonResponse(['client' => $client]);
    }

    public function project($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $projectModel = new ProjectModel();
        $project = $projectModel->select('projects.*, clients.name as client_name, clients.tag as client_tag, clients.color as client_color')
                                ->join('clients', 'clients.id = projects.client_id', 'left')
                                ->find($id);

        if (!$project) {
            return $this->jsonError('Project not found', 404);
        }

        return $this->jsonResponse(['project' => $project]);
    }

    public function createProject()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $data = $this->request->getJSON(true);

        if (empty($data['name'])) {
            return $this->jsonError('name is required');
        }

        $projectModel = new ProjectModel();
        $projectData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'client_id' => $data['client_id'] ?? null,
            'status' => $data['status'] ?? 'active',
            'is_visible_to_client' => $data['is_visible_to_client'] ?? false,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ];

        if ($projectModel->save($projectData)) {
            $projectId = $projectModel->getInsertID();
            $project = $projectModel->find($projectId);
            return $this->jsonResponse(['project' => $project, 'message' => 'Project created'], 201);
        }

        return $this->jsonError(implode(', ', $projectModel->errors()));
    }

    public function updateProject($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $projectModel = new ProjectModel();
        $project = $projectModel->find($id);

        if (!$project) {
            return $this->jsonError('Project not found', 404);
        }

        $data = $this->request->getJSON(true);
        $updateData = [];

        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['client_id'])) $updateData['client_id'] = $data['client_id'];
        if (isset($data['status'])) $updateData['status'] = $data['status'];
        if (isset($data['is_visible_to_client'])) $updateData['is_visible_to_client'] = $data['is_visible_to_client'];
        if (isset($data['start_date'])) $updateData['start_date'] = $data['start_date'];
        if (isset($data['end_date'])) $updateData['end_date'] = $data['end_date'];

        if (empty($updateData)) {
            return $this->jsonError('No data to update');
        }

        $updateData['id'] = $id;
        
        if ($projectModel->save($updateData)) {
            $updatedProject = $projectModel->find($id);
            return $this->jsonResponse(['project' => $updatedProject, 'message' => 'Project updated']);
        }

        return $this->jsonError(implode(', ', $projectModel->errors()));
    }

    public function tasks()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        $builder = $taskModel->select('tasks.*, projects.name as project_name, projects.id as project_id, users.name as user_name, users.initials as user_initials, users.color as user_color, clients.name as client_name, clients.tag as client_tag, clients.id as client_id')
                          ->join('projects', 'projects.id = tasks.project_id')
                          ->join('clients', 'clients.id = projects.client_id', 'left')
                          ->join('users', 'users.id = tasks.user_id', 'left');

        $userId = $this->request->getGet('user_id');
        $status = $this->request->getGet('status');
        $projectId = $this->request->getGet('project_id');
        $dueFrom = $this->request->getGet('due_from');
        $dueTo = $this->request->getGet('due_to');
        $excludeStatus = $this->request->getGet('exclude_status');

        if ($userId) {
            $builder->where('tasks.user_id', $userId);
        }

        if ($status) {
            $builder->where('tasks.status', $status);
        }

        if ($projectId) {
            $builder->where('tasks.project_id', $projectId);
        }

        if ($excludeStatus) {
            $statuses = array_map('trim', explode(',', $excludeStatus));
            $builder->whereNotIn('tasks.status', $statuses);
        }

        if ($dueFrom) {
            $fromDate = $this->parseRelativeDate($dueFrom);
            if ($fromDate) {
                $builder->where('tasks.due_date >=', $fromDate);
            }
        }

        if ($dueTo) {
            $toDate = $this->parseRelativeDate($dueTo);
            if ($toDate) {
                $builder->where('tasks.due_date <=', $toDate);
            }
        }

        $tasks = $builder->orderBy('tasks.due_date', 'ASC')->findAll();

        return $this->jsonResponse(['tasks' => $tasks]);
    }

    private function parseRelativeDate(string $date): ?string
    {
        $today = new \DateTime();
        
        switch (strtolower($date)) {
            case 'today':
                return $today->format('Y-m-d');
            case 'tomorrow':
                return (clone $today)->modify('+1 day')->format('Y-m-d');
            case 'yesterday':
                return (clone $today)->modify('-1 day')->format('Y-m-d');
            case 'this_week':
                return $today->format('Y-m-d');
            case 'next_week':
                return (clone $today)->modify('+7 days')->format('Y-m-d');
            case 'last_week':
                return (clone $today)->modify('-7 days')->format('Y-m-d');
            case 'this_month':
                return $today->format('Y-m-d');
            case 'next_month':
                return (clone $today)->modify('+1 month')->format('Y-m-d');
            default:
                $parsed = date_parse($date);
                if ($parsed && $parsed['year']) {
                    return $parsed['year'] . '-' . str_pad($parsed['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parsed['day'], 2, '0', STR_PAD_LEFT);
                }
                return null;
        }
    }

    public function task($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        $task = $taskModel->select('tasks.*, projects.name as project_name, projects.id as project_id, users.name as user_name, users.initials as user_initials, users.color as user_color, clients.name as client_name, clients.tag as client_tag, clients.id as client_id')
                         ->join('projects', 'projects.id = tasks.project_id')
                         ->join('clients', 'clients.id = projects.client_id', 'left')
                         ->join('users', 'users.id = tasks.user_id', 'left')
                         ->find($id);

        if (!$task) {
            return $this->jsonError('Task not found', 404);
        }

        $noteModel = new TaskNoteModel();
        $notes = $noteModel->select('task_notes.*, users.name as user_name, users.initials as user_initials')
                          ->join('users', 'users.id = task_notes.user_id', 'left')
                          ->where('task_notes.task_id', $id)
                          ->orderBy('task_notes.created_at', 'ASC')
                          ->findAll();

        return $this->jsonResponse(['task' => $task, 'notes' => $notes]);
    }

    public function createTask()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $data = $this->request->getJSON(true);

        if (empty($data['project_id']) || empty($data['title'])) {
            return $this->jsonError('project_id and title are required');
        }

        $taskModel = new TaskModel();
        $taskData = [
            'project_id'  => $data['project_id'],
            'title'      => $data['title'],
            'description' => $data['description'] ?? '',
            'status'     => $data['status'] ?? 'não iniciadas',
            'user_id'    => $data['user_id'] ?? null,
            'due_date'   => $data['due_date'] ?? null,
        ];

        if ($taskModel->save($taskData)) {
            $taskId = $taskModel->getInsertID();
            $task = $taskModel->find($taskId);
            return $this->jsonResponse(['task' => $task, 'message' => 'Task created'], 201);
        }

        return $this->jsonError(implode(', ', $taskModel->errors()));
    }

    public function updateTask($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        $task = $taskModel->find($id);

        if (!$task) {
            return $this->jsonError('Task not found', 404);
        }

        $data = $this->request->getJSON(true);
        $updateData = [];

        if (isset($data['title'])) $updateData['title'] = $data['title'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['status'])) $updateData['status'] = $data['status'];
        if (isset($data['user_id'])) $updateData['user_id'] = $data['user_id'];
        if (isset($data['due_date'])) $updateData['due_date'] = $data['due_date'];

        if (empty($updateData)) {
            return $this->jsonError('No data to update');
        }

        $updateData['id'] = $id;
        
        if ($taskModel->save($updateData)) {
            $updatedTask = $taskModel->find($id);
            return $this->jsonResponse(['task' => $updatedTask, 'message' => 'Task updated']);
        }

        return $this->jsonError(implode(', ', $taskModel->errors()));
    }

    public function deleteTask($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        $task = $taskModel->find($id);

        if (!$task) {
            return $this->jsonError('Task not found', 404);
        }

        if ($taskModel->delete($id)) {
            return $this->jsonResponse(['message' => 'Task deleted']);
        }

        return $this->jsonError('Failed to delete task');
    }

    public function taskNotes($taskId)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        if (!$taskModel->find($taskId)) {
            return $this->jsonError('Task not found', 404);
        }

        $noteModel = new TaskNoteModel();
        $notes = $noteModel->select('task_notes.*, users.name as user_name, users.initials as user_initials')
                          ->join('users', 'users.id = task_notes.user_id', 'left')
                          ->where('task_notes.task_id', $taskId)
                          ->orderBy('task_notes.created_at', 'ASC')
                          ->findAll();

        return $this->jsonResponse(['notes' => $notes]);
    }

    public function createTaskNote($taskId)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        if (!$taskModel->find($taskId)) {
            return $this->jsonError('Task not found', 404);
        }

        $data = $this->request->getJSON(true);

        if (empty($data['note'])) {
            return $this->jsonError('note is required');
        }

        $noteModel = new TaskNoteModel();
        $noteData = [
            'task_id' => $taskId,
            'user_id' => $data['user_id'] ?? 1,
            'note'   => $data['note'],
        ];

        if ($noteModel->save($noteData)) {
            $noteId = $noteModel->getInsertID();
            $note = $noteModel->find($noteId);
            return $this->jsonResponse(['note' => $note, 'message' => 'Note created'], 201);
        }

        return $this->jsonError(implode(', ', $noteModel->errors()));
    }

    public function deleteNote($noteId)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $noteModel = new TaskNoteModel();
        $note = $noteModel->find($noteId);

        if (!$note) {
            return $this->jsonError('Note not found', 404);
        }

        if ($noteModel->delete($noteId)) {
            return $this->jsonResponse(['message' => 'Note deleted']);
        }

        return $this->jsonError('Failed to delete note');
    }
}