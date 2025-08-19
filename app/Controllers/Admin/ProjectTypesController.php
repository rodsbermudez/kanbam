<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProjectTypeModel;

class ProjectTypesController extends BaseController
{
    /**
     * Exibe a lista de tipos de projeto.
     */
    public function index()
    {
        $model = new ProjectTypeModel();
        $data = [
            'title' => 'Tipos de Projeto',
            'types' => $model->findAll(),
        ];
        return view('admin/project_types/index', $data);
    }

    /**
     * Exibe o formulário para criar um novo tipo de projeto.
     */
    public function new()
    {
        return view('admin/project_types/form', [
            'title' => 'Novo Tipo de Projeto'
        ]);
    }

    /**
     * Processa a criação de um novo tipo de projeto.
     */
    public function create()
    {
        $model = new ProjectTypeModel();

        if ($model->save($this->request->getPost())) {
            return redirect()->to('/admin/project-types')->with('success', 'Tipo de projeto criado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $model->errors());
    }

    /**
     * Exibe o formulário para editar um tipo de projeto.
     */
    public function edit($id = null)
    {
        $model = new ProjectTypeModel();
        $type = $model->find($id);

        if (!$type) {
            return redirect()->to('/admin/project-types')->with('error', 'Tipo de projeto não encontrado.');
        }

        return view('admin/project_types/form', [
            'title' => 'Editar Tipo de Projeto',
            'type'  => $type
        ]);
    }

    /**
     * Processa a atualização de um tipo de projeto.
     */
    public function update($id = null)
    {
        $model = new ProjectTypeModel();

        if ($model->update($id, $this->request->getPost())) {
            return redirect()->to('/admin/project-types')->with('success', 'Tipo de projeto atualizado com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $model->errors());
    }

    /**
     * Deleta (soft delete) um tipo de projeto.
     */
    public function delete($id = null)
    {
        $model = new ProjectTypeModel();
        if ($model->delete($id)) {
            return redirect()->to('/admin/project-types')->with('success', 'Tipo de projeto removido com sucesso.');
        }
        return redirect()->to('/admin/project-types')->with('error', 'Erro ao remover o tipo de projeto.');
    }
}