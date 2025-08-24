<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ImportedReportModel;
use App\Models\ProjectModel;

class ReportsController extends BaseController
{
    /**
     * Retorna uma lista de relatórios disponíveis para importação em formato JSON.
     * Exclui relatórios que já foram importados para o projeto atual.
     */
    public function listAvailable($projectId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            // 1. Conecta-se à base de dados local para ver quais relatórios já foram importados
            $importedReportModel = new ImportedReportModel();
            $existingReports = $importedReportModel->withDeleted() // Considera também os relatórios removidos
                                                   ->where('project_id', $projectId)
                                                   ->findColumn('original_report_id');

            // 2. Conecta-se à base de dados de relatórios
            $reportsDB = \Config\Database::connect('reportsDB');
            $builder = $reportsDB->table('analyses');
            $builder->select('id, url, target_keyword, created_at');

            // 3. Exclui os IDs já importados, se houver algum
            if (!empty($existingReports)) {
                $builder->whereNotIn('id', $existingReports);
            }

            // Alterado para ordenar por 'id' para garantir que os mais recentes sempre apareçam primeiro,
            // mesmo que a data de criação seja nula ou incorreta.
            $availableReports = $builder->orderBy('id', 'DESC')->get()->getResult();

            return $this->response->setJSON(['success' => true, 'reports' => $availableReports]);

        } catch (\Throwable $e) {
            log_message('error', '[ReportsController::listAvailable] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Não foi possível conectar à base de dados de relatórios. Verifique as configurações.']);
        }
    }

    /**
     * Importa um relatório da base externa para a base local.
     */
    public function import($projectId)
    {
        $originalReportId = $this->request->getPost('report_id');

        if (empty($originalReportId)) {
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                             ->with('error', 'Nenhum relatório foi selecionado.');
        }

        try {
            // 1. Busca o relatório completo na base de dados externa
            $reportsDB = \Config\Database::connect('reportsDB');
            $originalReport = $reportsDB->table('analyses')->where('id', $originalReportId)->get()->getRow();

            if (!$originalReport) {
                return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                                 ->with('error', 'O relatório de origem não foi encontrado.');
            }

            // 2. Prepara os dados para salvar na base local
            $dataToSave = [
                'project_id'            => $projectId,
                'original_report_id'    => $originalReport->id,
                'url'                   => $originalReport->url,
                'target_keyword'        => $originalReport->target_keyword,
                'tech_stack'            => $originalReport->tech_stack,
                'report_data'           => $originalReport->analysis_data,
                'imported_by_user_id'   => session()->get('user_id'),
                'original_created_at'   => $originalReport->created_at,
            ];

            // 3. Salva na base local
            $importedReportModel = new ImportedReportModel();
            if ($importedReportModel->save($dataToSave)) {
                return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                                 ->with('success', 'Relatório importado com sucesso!');
            }

            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                             ->with('error', 'Erro ao salvar o relatório importado.')
                             ->with('errors', $importedReportModel->errors());

        } catch (\Throwable $e) {
            log_message('error', '[ReportsController::import] ' . $e->getMessage());
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                             ->with('error', 'Ocorreu um erro durante a importação: ' . $e->getMessage());
        }
    }

    /**
     * Exibe um relatório importado.
     */
    public function show($reportId)
    {
        $reportModel = new ImportedReportModel();
        $report = $reportModel->find($reportId);

        if (!$report) {
            return redirect()->back()->with('error', 'Relatório importado não encontrado.');
        }

        $data = [
            'title'       => 'Relatório de SEO: ' . esc($report->url),
            'report'      => $report,
            'report_data' => json_decode($report->report_data), // Decodifica o JSON para a view
            'project_id'  => $report->project_id, // Para o botão "voltar"
        ];

        return view('admin/reports/show', $data);
    }

    /**
     * Deleta (soft delete) um relatório importado.
     * A remoção acontece apenas na base de dados local.
     */
    public function delete($reportId)
    {
        $reportModel = new ImportedReportModel();
        $report = $reportModel->find($reportId);

        if (!$report) {
            return redirect()->back()->with('error', 'Relatório importado não encontrado.');
        }

        // Guarda o ID do projeto para o redirecionamento correto
        $projectId = $report->project_id;

        if ($reportModel->delete($reportId)) {
            return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                             ->with('success', 'Relatório removido com sucesso.');
        }

        return redirect()->to('/admin/projects/' . $projectId . '?active_tab=reports')
                         ->with('error', 'Erro ao remover o relatório.');
    }
}