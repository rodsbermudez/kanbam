<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ClientPortalAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('is_client_portal_logged_in')) {
            // Tenta obter o token da sessão para redirecionar para a página de login correta
            $token = session()->get('client_portal_token');
            $redirectUrl = $token ? '/portal/' . $token : '/'; // Fallback para a home se não houver token
            return redirect()->to($redirectUrl)->with('error', 'Por favor, insira sua senha para acessar.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}