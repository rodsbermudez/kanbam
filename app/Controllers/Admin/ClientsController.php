<?php

namespace App\Controllers\Admin; // <-- Verifique se o namespace inclui "\Admin"

use App\Controllers\BaseController;
use App\Models\ClientModel;

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
}