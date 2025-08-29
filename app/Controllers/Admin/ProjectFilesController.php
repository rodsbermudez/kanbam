<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectFileModel;
use App\Models\ProjectModel;

class ProjectFilesController extends BaseController
{
    /**
     * Processa o upload de um novo arquivo para um projeto.
     */
    public function create($projectId)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|max_length[255]',
            'file'  => 'uploaded[file]|max_size[file,10240]|ext_in[file,pdf,jpg,jpeg,png,gif,zip,svg,doc,docx,xls,xlsx,txt,md]', // Max 10MB e tipos permitidos
        ], [
            'file' => [
                'ext_in' => 'O tipo de arquivo que você está tentando enviar não é permitido. Apenas PDF, imagens, ZIP, SVG e documentos são aceitos.'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                             ->withInput()->with('errors', $validation->getErrors());
        }

        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            // Gera um nome aleatório para o arquivo para evitar conflitos
            $storedFileName = $file->getRandomName();
            
            // Move o arquivo para o diretório WRITEPATH/uploads/project_files/
            // Usar WRITEPATH é mais seguro pois não é acessível diretamente pela URL.
            $file->move(WRITEPATH . 'uploads/project_files', $storedFileName);

            $fileModel = new ProjectFileModel();
            $data = [
                'project_id'       => $projectId,
                'user_id'          => session()->get('user_id'),
                'title'            => $this->request->getPost('title'),
                'description'      => $this->request->getPost('description'),
                'file_name'        => $file->getClientName(),
                'stored_file_name' => $storedFileName,
                'file_type'        => $file->getClientMimeType(),
                'file_size'        => $file->getSize(),
            ];

            if ($fileModel->save($data)) {
                return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                                 ->with('success', 'Arquivo enviado com sucesso.');
            }
        }

        return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                         ->with('error', 'Ocorreu um erro ao enviar o arquivo.');
    }

    /**
     * Processa a criação de um novo link para um projeto.
     */
    public function createLink($projectId)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|max_length[255]',
            'url'   => 'required|valid_url_strict|max_length[2048]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                             ->withInput()->with('errors', $validation->getErrors());
        }

        $fileModel = new ProjectFileModel();
        $data = [
            'project_id'       => $projectId,
            'user_id'          => session()->get('user_id'),
            'title'            => $this->request->getPost('title'),
            'description'      => $this->request->getPost('description'),
            'item_type'        => 'link',
            'external_url'     => $this->request->getPost('url'),
        ];

        if ($fileModel->save($data)) {
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                             ->with('success', 'Link adicionado com sucesso.');
        }

        return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                         ->with('error', 'Ocorreu um erro ao adicionar o link.');
    }
    /**
     * Exibe um arquivo diretamente no navegador, se for um tipo suportado.
     */
    public function view($fileId)
    {
        $fileModel = new ProjectFileModel();
        $file = $fileModel->find($fileId);

        if (!$file) {
            // Idealmente, mostrar uma página de erro 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Arquivo não encontrado.');
        }

        $path = WRITEPATH . 'uploads/project_files/' . $file->stored_file_name;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Arquivo físico não encontrado no servidor.');
        }

        // Envia o arquivo para o navegador com o Content-Type correto e disposição "inline"
        return $this->response->setBody(file_get_contents($path))
                              ->setHeader('Content-Type', $file->file_type)
                              ->setHeader('Content-Disposition', 'inline; filename="' . basename($file->file_name) . '"');
    }
    /**
     * Força o download de um arquivo.
     */
    public function download($fileId)
    {
        $fileModel = new ProjectFileModel();
        $file = $fileModel->find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }

        $path = WRITEPATH . 'uploads/project_files/' . $file->stored_file_name;

        if (!file_exists($path)) {
             return redirect()->back()->with('error', 'Arquivo físico não encontrado no servidor.');
        }

        // Força o download no navegador com o nome original do arquivo
        return $this->response->download($path, null)->setFileName($file->file_name);
    }

    /**
     * Deleta (soft delete) um arquivo.
     */
    public function delete($fileId)
    {
        $fileModel = new ProjectFileModel();
        $file = $fileModel->find($fileId);

        if (!$file) {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }

        // Guarda o ID do projeto para o redirecionamento
        $projectId = $file->project_id;

        if ($fileModel->delete($fileId)) {
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                             ->with('success', 'Arquivo removido com sucesso.');
        }

        return redirect()->to('/admin/projects/' . $projectId . '?active_tab=files')
                         ->with('error', 'Erro ao remover o arquivo.');
    }

    /**
     * Alterna a visibilidade de um arquivo no portal do cliente via AJAX.
     */
    public function toggleClientVisibility($fileId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Acesso negado.');
        }

        $fileModel = new ProjectFileModel();
        $file = $fileModel->find($fileId);

        if (!$file) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Arquivo não encontrado.']);
        }

        $newVisibility = !(bool)($file->is_visible_to_client ?? false);

        if ($fileModel->update($fileId, ['is_visible_to_client' => $newVisibility])) {
            return $this->response->setJSON(['success' => true, 'new_visibility' => $newVisibility]);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Erro ao atualizar a visibilidade do arquivo.']);
    }
}