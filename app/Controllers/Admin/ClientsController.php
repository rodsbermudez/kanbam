<?php

namespace App\Controllers\Admin; // <-- Verifique se o namespace inclui "\Admin"

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\ProjectModel;
use App\Models\TaskModel;
use App\Models\ClientAccessModel;

class ClientsController extends BaseController
{
    /**
     * Exibe a lista de todos os clientes.
     */
    public function index()
    {
        $clientModel = new ClientModel();
        $data = [
            'title'   => 'Gerenciar Clientes',
            'clients' => $clientModel->findAll()
        ];
        return view('admin/clients/index', $data);
    }

    /**
     * Exibe o formulário para criar um novo cliente.
     */
    public function new()
    {
        return view('admin/clients/form', [
            'title' => 'Novo Cliente'
        ]);
    }

    /**
     * Processa a criação de um novo cliente.
     */
    public function create()
    {
        $clientModel = new ClientModel();

        if ($clientModel->save($this->request->getPost())) {
            return redirect()->to('/admin/clients')->with('success', 'Cliente criado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $clientModel->errors());
    }

    /**
     * Exibe os detalhes de um cliente específico, seus projetos e tarefas.
     */
    public function show($id)
    {
        helper('text');
        $clientModel = new ClientModel();
        $projectModel = new ProjectModel();
        $taskModel = new TaskModel();
        $clientAccessModel = new ClientAccessModel();

        $client = $clientModel->find($id);

        if (!$client) {
            return redirect()->to('/admin/clients')->with('error', 'Cliente não encontrado.');
        }
        $access = $clientAccessModel->where('client_id', $id)->first();

        // Separa os projetos em ativos e concluídos
        $allProjects = $projectModel->getProjectsForClient($id);
        $activeProjects = [];
        $concludedProjects = [];

        foreach ($allProjects as $project) {
            if (isset($project->status) && $project->status === 'concluded') {
                $concludedProjects[] = $project;
            } else {
                $activeProjects[] = $project;
            }
        }

        $data = [
            'title'          => 'Detalhes do Cliente: ' . esc($client->name),
            'client'         => $client,
            'active_projects'  => $activeProjects,
            'concluded_projects' => $concludedProjects,
            'upcoming_tasks' => $taskModel->getUpcomingTasksForClient($id),
            'overdue_tasks'  => $taskModel->getOverdueTasksForClient($id),
            'access'         => $access,
        ];

        return view('admin/clients/show', $data);
    }



    /**
     * Exibe o formulário para editar um cliente existente.
     */
    public function edit($id)
    {
        $clientModel = new ClientModel();
        $client = $clientModel->find($id);

        if (!$client) {
            return redirect()->to('/admin/clients')->with('error', 'Cliente não encontrado.');
        }

        return view('admin/clients/form', [
            'title'  => 'Editar Cliente: ' . esc($client->name),
            'client' => $client
        ]);
    }

    /**
     * Processa a atualização de um cliente.
     */
    public function update($id)
    {
        $clientModel = new ClientModel();
        $data = $this->request->getPost();

        // Adiciona o ID aos dados para que a regra de validação 'is_unique' funcione corretamente na atualização.
        $data['id'] = $id;

        if ($clientModel->update($id, $data)) {
            return redirect()->to('/admin/clients')->with('success', 'Cliente atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $clientModel->errors());
    }

    /**
     * Deleta um cliente (soft delete).
     */
    public function delete($id)
    {
        $clientModel = new ClientModel();

        if ($clientModel->delete($id)) {
            return redirect()->to('/admin/clients')->with('success', 'Cliente removido com sucesso.');
        }

        return redirect()->to('/admin/clients')->with('error', 'Erro ao remover o cliente.');
    }

    /**
     * Habilita o acesso do cliente e gera uma senha.
     */
    public function enableAccess($clientId)
    {
        $clientAccessModel = new ClientAccessModel();

        // Gera um token único e uma senha aleatória
        $token = bin2hex(random_bytes(32));
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()'), 0, 12);

        $data = [
            'client_id' => $clientId,
            'token'     => $token,
            'password'  => $password, // O model fará o hash
        ];

        if ($clientAccessModel->save($data)) {
            // Guarda a senha em texto plano na sessão para exibir ao admin uma única vez
            session()->setFlashdata('generated_password', $password);
            return redirect()->to('/admin/clients/' . $clientId)->with('success', 'Acesso do cliente habilitado com sucesso!');
        }

        return redirect()->to('/admin/clients/' . $clientId)->with('error', 'Erro ao habilitar o acesso.');
    }

    /**
     * Gera uma nova senha para um acesso de cliente existente.
     */
    public function regeneratePassword($accessId)
    {
        $clientAccessModel = new ClientAccessModel();
        $access = $clientAccessModel->find($accessId);
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()'), 0, 12);

        $clientAccessModel->update($accessId, ['password' => $password]);
        session()->setFlashdata('generated_password', $password);
        return redirect()->to('/admin/clients/' . $access->client_id)->with('success', 'Nova senha gerada com sucesso!');
    }

    /**
     * Remove o acesso de um cliente ao portal.
     */
    public function deleteAccess($accessId)
    {
        $clientAccessModel = new ClientAccessModel();
        $access = $clientAccessModel->find($accessId);

        if (!$access) {
            return redirect()->back()->with('error', 'Registro de acesso não encontrado.');
        }

        $clientId = $access->client_id;

        if ($clientAccessModel->delete($accessId, true)) { // true para hard delete
            return redirect()->to('/admin/clients/' . $clientId)->with('success', 'Acesso do cliente removido com sucesso.');
        }

        return redirect()->to('/admin/clients/' . $clientId)->with('error', 'Erro ao remover o acesso do cliente.');
    }
}