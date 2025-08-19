<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectDocumentModel;

class ProjectDocumentsController extends BaseController
{
    /**
     * Retorna os dados de um documento específico em formato JSON.
     */
    public function show($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $docModel = new ProjectDocumentModel();
        $document = $docModel->find($id);

        if (!$document) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Documento não encontrado.']);
        }

        return $this->response->setJSON(['success' => true, 'document' => $document]);
    }

    /**
     * Processa a criação de um novo documento.
     */
    public function create($projectId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $docModel = new ProjectDocumentModel();
        $data = $this->request->getJSON(true);
        $data['project_id'] = $projectId;

        if ($docModel->save($data)) {
            $newId = $docModel->getInsertID();
            return $this->response->setJSON(['success' => true, 'message' => 'Documento criado com sucesso.', 'new_id' => $newId]);
        }

        return $this->response->setStatusCode(400)->setJSON(['success' => false, 'errors' => $docModel->errors()]);
    }

    /**
     * Processa a atualização de um documento.
     */
    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $docModel = new ProjectDocumentModel();
        if (!$docModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Documento não encontrado.']);
        }

        $data = $this->request->getJSON(true);

        if ($docModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Documento atualizado com sucesso.', 'id' => $id]);
        }

        return $this->response->setStatusCode(400)->setJSON(['success' => false, 'errors' => $docModel->errors()]);
    }

    /**
     * Deleta (soft delete) um documento.
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $docModel = new ProjectDocumentModel();
        if (!$docModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Documento não encontrado.']);
        }

        if ($docModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Documento removido com sucesso.']);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao remover o documento.']);
    }
}