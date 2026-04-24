<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReportModel;
use App\Models\ClientModel;
use App\Libraries\XmlToHtml;

class MaintenanceController extends BaseController
{
    protected $reportsPath;

    public function __construct()
    {
        $this->reportsPath = WRITEPATH . 'reports/';
    }

    /**
     * Lista de relatórios
     */
    public function index()
    {
        $model = new ReportModel();
        $clientModel = new ClientModel();
        
        $filterClientId = $this->request->getGet('client_id');
        
        if ($filterClientId) {
            $reports = $model->where('client_id', $filterClientId)->orderBy('created_at', 'DESC')->findAll();
        } else {
            $reports = $model->orderBy('created_at', 'DESC')->findAll();
        }
        
        // Adicionar dados do cliente
        $clients = $clientModel->orderBy('name', 'ASC')->findAll();
        $clientsById = [];
        foreach ($clients as $client) {
            $clientsById[$client->id] = $client;
        }
        
        foreach ($reports as $report) {
            $report->client = $clientsById[$report->client_id] ?? null;
        }
        
        $data = [
            'title' => 'Relatórios',
            'reports' => $reports,
            'clients' => $clients,
            'filter_client_id' => $filterClientId,
        ];
        
        return view('admin/reports/index', $data);
    }

    /**
     * Formulário para importar relatório
     */
    public function create()
    {
        $clientModel = new ClientModel();
        $clients = $clientModel->orderBy('name', 'ASC')->findAll();
        
        // Lista arquivos XML da pasta
        $xmlFiles = XmlToHtml::listXmlFiles($this->reportsPath);
        
        $data = [
            'title' => 'Importar Relatório',
            'clients' => $clients,
            'xmlFiles' => $xmlFiles,
        ];
        
        return view('admin/reports/create', $data);
    }

    /**
     * Salva o relatório no banco
*/
    public function store()
    {
        // Aceita GET ou POST
        $fileName = $this->request->getPost('xml_file') ?? $this->request->getGet('xml_file');
        $clientId = (int) ($this->request->getPost('client_id') ?? $this->request->getGet('client_id'));
        $title = $this->request->getPost('title') ?? $this->request->getGet('title');
        
        if (empty($fileName) || empty($clientId)) {
            return redirect()->to('/admin/maintenance/create')->with('error', 'Selecione o arquivo e o cliente.');
        }
        
        $fullPath = $this->reportsPath . $fileName;
        
        if (!file_exists($fullPath)) {
            return redirect()->to('/admin/maintenance/create')->with('error', 'Arquivo não encontrado.');
        }
        
        $xmlContent = file_get_contents($fullPath);
        
        if (empty($title)) {
            $title = XmlToHtml::extractTitle($xmlContent);
        }
        
        $db = \Config\Database::connect();
        
        $result = $db->table('reports')->insert([
            'client_id' => $clientId,
            'title' => $title,
            'xml_content' => $xmlContent,
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        if ($result) {
            $reportId = $db->insertID();
            return redirect()->to('/admin/maintenance/' . $reportId . '/view')->with('success', 'Relatório importado com sucesso.');
        }
        
        $error = $db->error();
        return redirect()->to('/admin/maintenance/create')->with('error', 'Erro ao salvar: ' . $error['message']);
    }

    /**
     * Visualiza o relatório
     */
    public function view($id)
    {
        $model = new ReportModel();
        $clientModel = new ClientModel();
        
        $report = $model->find($id);
        
        if (!$report) {
            return redirect()->to('/admin/maintenance')->with('error', 'Relatório não encontrado.');
        }
        
        // Buscar cliente
        $client = $clientModel->find($report->client_id);
        $report->client = $client;
        
        // Converter XML para HTML
        $htmlContent = XmlToHtml::convert($report->xml_content);
        
        $data = [
            'title' => 'Ver Relatório',
            'report' => $report,
            'htmlContent' => $htmlContent,
        ];
        
        return view('admin/reports/view', $data);
    }

    /**
     * Exclui o relatório
     */
    public function delete($id)
    {
        $model = new ReportModel();
        
        $report = $model->find($id);
        
        if (!$report) {
            return redirect()->to('/admin/maintenance')->with('error', 'Relatório não encontrado.');
        }
        
        if ($model->delete($id)) {
            return redirect()->to('/admin/maintenance')->with('success', 'Relatório excluído com sucesso.');
        }
        
        return redirect()->to('/admin/maintenance')->with('error', 'Erro ao excluir relatório.');
    }
}