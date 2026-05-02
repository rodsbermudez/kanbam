<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AgencyModel;
use App\Models\ClientModel;

class AgenciesController extends BaseController
{
    /**
     * Lista todas as agências
     */
    public function index()
    {
        $agencyModel = new AgencyModel();
        $data = [
            'title'    => 'Gerenciar Agências',
            'agencies' => $agencyModel->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/agencies/index', $data);
    }

    /**
     * Formulário para nova agência
     */
    public function new()
    {
        return view('admin/agencies/form', [
            'title' => 'Nova Agência'
        ]);
    }

    /**
     * Salva nova agência
     */
    public function create()
    {
        $agencyModel = new AgencyModel();

        if ($agencyModel->save($this->request->getPost())) {
            return redirect()->to('/admin/agencies')->with('success', 'Agência criada com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $agencyModel->errors());
    }

    /**
     * Formulário para editar agência
     */
    public function edit($id)
    {
        $agencyModel = new AgencyModel();
        $agency = $agencyModel->find($id);

        if (!$agency) {
            return redirect()->to('/admin/agencies')->with('error', 'Agência não encontrada.');
        }

        return view('admin/agencies/form', [
            'title'   => 'Editar Agência',
            'agency' => $agency
        ]);
    }

    /**
     * Atualiza agência
     */
    public function update($id)
    {
        $agencyModel = new AgencyModel();
        $data = $this->request->getPost();
        $data['id'] = $id;

        if ($agencyModel->save($data)) {
            return redirect()->to('/admin/agencies')->with('success', 'Agência atualizada com sucesso.');
        }

        return redirect()->back()->withInput()->with('errors', $agencyModel->errors());
    }

    /**
     * Exibe detalhes da agência e clientes vinculados
     */
    public function show($id)
    {
        $agencyModel = new AgencyModel();
        $clientModel = new ClientModel();
        $agency = $agencyModel->find($id);

        if (!$agency) {
            return redirect()->to('/admin/agencies')->with('error', 'Agência não encontrada.');
        }

        $linkedClients = $clientModel->where('agency_id', $id)
                                   ->orderBy('name', 'ASC')
                                   ->findAll();

        $availableClients = $clientModel->where('agency_id', null)
                                     ->orWhere('agency_id', $id)
                                     ->orderBy('name', 'ASC')
                                     ->findAll();

        $data = [
            'title'          => 'Agência: ' . esc($agency->name),
            'agency'         => $agency,
            'linked_clients' => $linkedClients,
            'available_clients' => $availableClients,
        ];

        return view('admin/agencies/show', $data);
    }

    /**
     * Deleta agência (soft delete)
     */
    public function delete($id)
    {
        $agencyModel = new AgencyModel();

        // Desvincula clientes
        $clientModel = new ClientModel();
        $clientModel->where('agency_id', $id)->set(['agency_id' => null])->update();

        if ($agencyModel->delete($id)) {
            return redirect()->to('/admin/agencies')->with('success', 'Agência removida com sucesso.');
        }

        return redirect()->to('/admin/agencies')->with('error', 'Erro ao remover a agência.');
    }

    /**
     * Vincula um cliente à agência
     */
    public function linkClient($agencyId)
    {
        $clientId = $this->request->getPost('client_id');

        if (!$clientId) {
            return redirect()->back()->with('error', 'Selecione um cliente.');
        }

        $agencyModel = new AgencyModel();
        if ($agencyModel->linkClient($agencyId, $clientId)) {
            return redirect()->back()->with('success', 'Cliente vinculado com sucesso.');
        }

        return redirect()->back()->with('error', 'Erro ao vincular cliente.');
    }

    /**
     * Desvincula um cliente da agência
     */
    public function unlinkClient($agencyId, $clientId)
    {
        $agencyModel = new AgencyModel();
        if ($agencyModel->unlinkClient($clientId)) {
            return redirect()->to('/admin/agencies/' . $agencyId)->with('success', 'Cliente desvinculado.');
        }

        return redirect()->back()->with('error', 'Erro ao desvincular cliente.');
    }
}