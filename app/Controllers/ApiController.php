<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\TaskNoteModel;

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

    public function projects()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $projectModel = new ProjectModel();
        $projects = $projectModel->select('projects.id, projects.name, projects.status, projects.is_visible_to_client, projects.client_id, clients.name as client_name, clients.tag as client_tag')
                                ->join('clients', 'clients.id = projects.client_id', 'left')
                                ->orderBy('projects.name', 'ASC')
                                ->findAll();

        return $this->jsonResponse(['projects' => $projects]);
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

    public function tasks()
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        $tasks = $taskModel->select('tasks.*, projects.name as project_name, projects.id as project_id')
                          ->join('projects', 'projects.id = tasks.project_id')
                          ->orderBy('tasks.created_at', 'DESC')
                          ->findAll();

        return $this->jsonResponse(['tasks' => $tasks]);
    }

    public function task($id)
    {
        if (!$this->authenticate()) {
            return $this->jsonError('Unauthorized', 401);
        }

        $taskModel = new TaskModel();
        $task = $taskModel->select('tasks.*, projects.name as project_name, projects.id as project_id')
                         ->join('projects', 'projects.id = tasks.project_id')
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