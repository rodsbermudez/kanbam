<?php

if (!function_exists('get_latest_app_version')) {
    /**
     * Lê o arquivo changelog.php para extrair a versão mais recente.
     *
     * @return string A versão mais recente ou '1.0.0' como fallback.
     */
    function get_latest_app_version(): string
    {
        $changelogFile = APPPATH . 'Views/changelog.php';

        if (!file_exists($changelogFile)) {
            return '1.0.0';
        }

        // Lê o conteúdo do arquivo
        $content = file_get_contents($changelogFile);

        // Usa expressão regular para encontrar a primeira chave do array $changelog
        if (preg_match("/'([\d\.]+)'\s*=>\s*\[/", $content, $matches)) {
            return $matches[1];
        }

        return '1.0.0'; // Fallback
    }
}