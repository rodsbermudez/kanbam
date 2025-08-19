<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TaskNoteModel;
use App\Models\TaskModel;

class TaskNotesController extends BaseController
{
    /**
     * Processa a criação de uma nova nota para uma tarefa.
     */
    public function create($taskId)
    {
        $taskModel = new TaskModel();
        $task = $taskModel->find($taskId);

        if (!$task) {
            return redirect()->back()->with('error', 'Tarefa não encontrada.');
        }

        $noteModel = new TaskNoteModel();

        $data = [
            'task_id' => $taskId,
            'user_id' => session()->get('user_id'),
            'note'    => trim($this->request->getPost('note')),
        ];

        if ($noteModel->save($data)) {
            return redirect()->to('/admin/projects/' . $task->project_id)
                             ->with('success', 'Nota adicionada com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $noteModel->errors());
    }

    /**
     * Deleta uma nota.
     */
    public function delete($noteId)
    {
        $noteModel = new TaskNoteModel();
        $note = $noteModel->find($noteId);

        if (!$note) {
            return redirect()->back()->with('error', 'Nota não encontrada.');
        }

        // Pega o ID do projeto para o redirecionamento
        $taskModel = new TaskModel();
        $task = $taskModel->find($note->task_id);
        $projectId = $task->project_id ?? null;

        // Verifica permissão: admin ou autor da nota
        if (!session()->get('is_admin') && $note->user_id != session()->get('user_id')) {
            return redirect()->to('/admin/projects/' . $projectId)
                             ->with('error', 'Você não tem permissão para remover esta nota.');
        }

        if ($noteModel->delete($noteId)) {
            return redirect()->to('/admin/projects/' . $projectId)
                             ->with('success', 'Nota removida com sucesso.');
        }

        return redirect()->to('/admin/projects/' . $projectId)
                         ->with('error', 'Erro ao remover a nota.');
    }
}