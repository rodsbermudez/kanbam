<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UsersController extends BaseController
{
    /**
     * Exibe a lista de todos os usuários.
     */
    public function index()
    {
        $userModel = new UserModel();
        $data = [
            'title' => 'Gerenciar Usuários',
            'users' => $userModel->findAll()
        ];
        return view('admin/users/index', $data);
    }

    /**
     * Exibe o formulário para criar um novo usuário.
     */
    public function new()
    {
        return view('admin/users/form', [
            'title' => 'Novo Usuário'
        ]);
    }

    /**
     * Processa a criação de um novo usuário.
     */
    public function create()
    {
        $userModel = new UserModel();

        $data = $this->request->getPost();

        if ($userModel->save($data)) {
            return redirect()->to('/admin/users')->with('success', 'Usuário criado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $userModel->errors());
    }

    /**
     * Exibe o formulário para editar um usuário existente.
     */
    public function edit($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }

        return view('admin/users/form', [
            'title' => 'Editar Usuário: ' . esc($user->name),
            'user'  => $user
        ]);
    }

    /**
     * Processa a atualização de um usuário.
     */
    public function update($id)
    {
        $userModel = new UserModel();
        $data = $this->request->getPost();

        // Adiciona o ID aos dados para que a regra de validação 'is_unique' funcione corretamente na atualização.
        $data['id'] = $id;

        // Se a senha estiver vazia, remova-a dos dados e relaxe a regra de validação
        if (empty($data['password'])) {
            unset($data['password']);
            $userModel->setValidationRule('password', 'permit_empty|min_length[8]');
        }

        // Garante que o checkbox desmarcado seja salvo como 0
        $data['is_admin'] = $data['is_admin'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? 0;

        if ($userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'Usuário atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $userModel->errors());
    }

    /**
     * Deleta um usuário (soft delete).
     */
    public function delete($id)
    {
        $userModel = new UserModel();

        if ($userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'Usuário removido com sucesso.');
        }

        return redirect()->to('/admin/users')->with('error', 'Erro ao remover o usuário.');
    }
}