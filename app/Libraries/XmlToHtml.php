<?php

namespace App\Libraries;

/**
 * Biblioteca para converter XML de relatórios em HTML formatado
 */
class XmlToHtml
{
    /**
     * Converte conteúdo XML em HTML formatado
     */
    public static function convert(string $xmlContent): string
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);
        
        if (!$xml) {
            return '<div class="alert alert-danger">Erro ao processar XML.</div>';
        }
        
        $html = '<div class="report-container">';
        
        // Cabeçalho
        if (isset($xml->header)) {
            $html .= self::renderHeader($xml->header);
        }
        
        // Seções
        if (isset($xml->sections->section)) {
            foreach ($xml->sections->section as $section) {
                $html .= self::renderSection($section);
            }
        }
        
        // Rodapé
        if (isset($xml->footer)) {
            $html .= self::renderFooter($xml->footer);
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Renderiza o cabeçalho do relatório
     */
    private static function renderHeader($header): string
    {
        $title = esc($header->title ?? 'Relatório');
        $client = esc($header->client ?? '');
        $date = esc($header->date ?? '');
        $author = esc($header->author ?? '');
        
        return '<div class="report-header mb-4 pb-3 border-bottom">' .
            '<h2 class="report-title">' . $title . '</h2>' .
            '<div class="report-meta">' .
                '<span class="badge bg-primary me-2">' . $client . '</span>' .
                '<span class="text-muted me-2">|</span>' .
                '<span class="text-muted me-2">' . $date . '</span>' .
                '<span class="text-muted me-2">|</span>' .
                '<span class="text-muted">' . $author . '</span>' .
            '</div>' .
        '</div>';
    }
    
    /**
     * Renderiza uma seção do relatório
     */
    private static function renderSection($section): string
    {
        $type = (string) $section['type'];
        $title = esc($section->title ?? '');
        $description = isset($section->description) ? esc($section->description) : '';
        
        $html = '<div class="report-section mb-4">';
        
        if ($title) {
            $html .= '<h3 class="report-section-title">' . $title . '</h3>';
        }
        
        if ($description) {
            $html .= '<p class="report-description">' . $description . '</p>';
        }
        
        switch ($type) {
            case 'info_simple':
                $html .= self::renderInfoSimple($section);
                break;
            case 'table':
            case 'table_versions':
            case 'table_status':
                $html .= self::renderTable($section, $type);
                break;
            case 'list_items':
                $html .= self::renderListItems($section);
                break;
            case 'alert':
                $html .= self::renderAlert($section);
                break;
            case 'pendencies':
                $html .= self::renderPendencies($section);
                break;
            case 'text_block':
                $html .= self::renderTextBlock($section);
                break;
            default:
                $html .= '<p class="text-muted">Tipo de seção não suportado: ' . esc($type) . '</p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Renderiza seção info_simple (chave: valor)
     */
    private static function renderInfoSimple($section): string
    {
        if (!isset($section->items->item)) {
            return '';
        }
        
        $rows = '';
        foreach ($section->items->item as $item) {
            $key = esc($item['key'] ?? '');
            $value = esc($item['value'] ?? '');
            $status = esc($item['status'] ?? 'default');
            $badgeClass = self::getStatusClass($status);
            
            $rows .= '<tr>' .
                '<td class="fw-semibold" style="width: 40%">' . $key . '</td>' .
                '<td><span class="badge ' . $badgeClass . '">' . $value . '</span></td>' .
            '</tr>';
        }
        
        return '<table class="table table-bordered table-sm"><tbody>' . $rows . '</tbody></table>';
    }
    
    /**
     * Renderiza tabela
     */
    private static function renderTable($section, $type): string
    {
        $hasColumns = isset($section->columns->column);
        $hasRows = isset($section->rows->row);
        
        if (!$hasRows) {
            return '';
        }
        
        $headerCols = '';
        if ($hasColumns) {
            foreach ($section->columns->column as $col) {
                $width = isset($col['width']) ? 'style="width:' . esc($col['width']) . '"' : '';
                $headerCols .= '<th ' . $width . '>' . esc($col) . '</th>';
            }
        }
        
        $bodyRows = '';
        foreach ($section->rows->row as $row) {
            $bodyCols = '';
            foreach ($row->cell as $cell) {
                $status = isset($cell['status']) ? esc($cell['status']) : 'default';
                $badgeClass = self::getStatusClass($status);
                $cellContent = esc($cell);
                
                if ($type === 'table_versions') {
                    $bodyCols .= '<td>' . $cellContent . '</td>';
                } else {
                    $bodyCols .= '<td><span class="badge ' . $badgeClass . '">' . $cellContent . '</span></td>';
                }
            }
            $bodyRows .= '<tr>' . $bodyCols . '</tr>';
        }
        
        $tableHeader = $hasColumns ? '<thead><tr>' . $headerCols . '</tr></thead>' : '';
        
        // Summary
        $summaryHtml = '';
        if (isset($section->summary)) {
            $total = esc($section->summary->total ?? '');
            $pending = esc($section->summary->pending ?? '');
            
            if ($total || $pending) {
                $colCount = $hasColumns ? count($section->columns->column) : 3;
                $summaryHtml = '<tfoot class="table-light"><tr><td colspan="' . $colCount . '"><small class="text-muted">' . $total;
                if ($pending) {
                    $summaryHtml .= '<span class="ms-3">' . $pending . '</span>';
                }
                $summaryHtml .= '</small></td></tr></tfoot>';
            }
        }
        
        $tableClass = $type === 'table_versions' ? 'table table-bordered table-sm' : 'table table-bordered table-sm';
        
        return '<table class="' . $tableClass . '">' . $tableHeader . '<tbody>' . $bodyRows . '</tbody>' . $summaryHtml . '</table>';
    }
    
    /**
     * Renderiza lista de itens
     */
    private static function renderListItems($section): string
    {
        if (!isset($section->items->item)) {
            return '';
        }
        
        $items = '';
        foreach ($section->items->item as $item) {
            $items .= '<li class="mb-1">' . esc($item) . '</li>';
        }
        
        return '<ul class="report-list">' . $items . '</ul>';
    }
    
    /**
     * Renderiza alerta
     */
    private static function renderAlert($section): string
    {
        $level = esc($section->level ?? 'info');
        $message = esc($section->message ?? '');
        $actionRequired = esc($section->action_required ?? '');
        
        $alertClass = self::getAlertClass($level);
        
        $impacts = '';
        if (isset($section->impact->item)) {
            $impactItems = '';
            foreach ($section->impact->item as $impact) {
                $impactItems .= '<li>' . esc($impact) . '</li>';
            }
            $impacts = '<ul class="mb-2">' . $impactItems . '</ul>';
        }
        
        $actionHtml = $actionRequired ? '<strong>Ação requerida:</strong> ' . esc($actionRequired) : '';
        
        return '<div class="alert alert-' . $alertClass . '">' .
            '<strong>' . $message . '</strong>' .
            $impacts .
            $actionHtml .
        '</div>';
    }
    
    /**
     * Renderiza pendências
     */
    private static function renderPendencies($section): string
    {
        if (!isset($section->items->item)) {
            return '';
        }
        
        $items = '';
        foreach ($section->items->item as $item) {
            $priority = esc($item['priority'] ?? 'medium');
            $priorityBadge = self::getPriorityClass($priority);
            
            $items .= '<li class="d-flex align-items-center gap-2 mb-2">' .
                '<span class="badge ' . $priorityBadge . '">' . $priority . '</span>' .
                '<span>' . $item . '</span>' .
            '</li>';
        }
        
        return '<ul class="report-pendencies list-unstyled">' . $items . '</ul>';
    }
    
    /**
     * Renderiza texto livre
     */
    private static function renderTextBlock($section): string
    {
        $content = isset($section->content) ? nl2br(esc($section->content)) : '';
        return '<div class="report-text-block">' . $content . '</div>';
    }
    
    /**
     * Renderiza rodapé
     */
    private static function renderFooter($footer): string
    {
        $note = esc($footer->note ?? '');
        
        return '<div class="report-footer mt-4 pt-3 border-top text-muted">' .
            '<small>' . $note . '</small>' .
        '</div>';
    }
    
    /**
     * Retorna classe CSS para status
     */
    private static function getStatusClass(string $status): string
    {
        switch ($status) {
            case 'success':
                return 'bg-success';
            case 'warning':
                return 'bg-warning text-dark';
            case 'danger':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
    
    /**
     * Retorna classe CSS para alertas
     */
    private static function getAlertClass(string $level): string
    {
        switch ($level) {
            case 'critical':
                return 'danger';
            case 'warning':
                return 'warning';
            default:
                return 'info';
        }
    }
    
    /**
     * Retorna classe CSS para prioridade
     */
    private static function getPriorityClass(string $priority): string
    {
        switch ($priority) {
            case 'high':
                return 'bg-danger';
            case 'medium':
                return 'bg-warning text-dark';
            default:
                return 'bg-secondary';
        }
    }
    
    /**
     * Extrai o título do XML
     */
    public static function extractTitle(string $xmlContent): string
    {
        $xml = simplexml_load_string($xmlContent);
        if ($xml && isset($xml->header->title)) {
            return (string) $xml->header->title;
        }
        return 'Relatório sem título';
    }
    
    /**
     * Lista arquivos XML em uma pasta
     */
    public static function listXmlFiles(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }
        
        $files = [];
        $items = scandir($directory);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $path = $directory . '/' . $item;
            if (is_file($path) && pathinfo($item, PATHINFO_EXTENSION) === 'xml') {
                $files[] = [
                    'name' => $item,
                    'path' => $path,
                    'modified' => filemtime($path),
                ];
            }
        }
        
        // Ordenar por data de modificação (mais recente primeiro)
        usort($files, function($a, $b) { return $b['modified'] - $a['modified']; });
        
        return $files;
    }
}