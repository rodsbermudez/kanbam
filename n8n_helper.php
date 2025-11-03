<?php

if (!function_exists('dispararWebhookN8N')) {
    /**
     * Dispara um evento de webhook para a plataforma N8N de forma segura e assíncrona.
     *
     * @param string $webhookPath O "caminho" específico do webhook (ex: 'novo-usuario-projeto').
     * @param array  $payload     Os dados (array) a serem enviados como JSON.
     * @return void
     */
    function dispararWebhookN8N(string $webhookPath, array $payload): void
    {
        // 1. Obter a URL base e a chave secreta do arquivo .env
        // Isso evita hardcoding de credenciais no código.
        $baseUrl = getenv('N8N_BASE_URL');
        $secretKey = getenv('N8N_SECRET_KEY');

        // Se as variáveis não estiverem configuradas, apenas registre e saia.
        if (empty($baseUrl) || empty($secretKey)) {
            log_message('error', 'Webhook N8N não disparado: N8N_BASE_URL ou N8N_SECRET_KEY não estão configuradas no arquivo .env.');
            return;
        }

        // Monta a URL completa de forma segura
        $urlCompleta = rtrim($baseUrl, '/') . '/' . ltrim($webhookPath, '/');

        try {
            $client = \Config\Services::curlrequest([
                'base_uri' => $baseUrl,
            ]);

            $client->request('POST', $urlCompleta, [
                'json'    => $payload, // O CI4 já define 'Content-Type: application/json'
                'headers' => [
                    'X-API-KEY' => $secretKey
                ],
                'timeout' => 5.0 // Timeout de 5 segundos para não travar a aplicação
            ]);
        } catch (\Exception $e) {
            // Se o N8N falhar, não quebre o app. Apenas registre o erro.
            log_message('error', 'Falha ao disparar Webhook N8N para ' . $urlCompleta . ': ' . $e->getMessage());
        }
    }
}