<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    /**
     * Exibe o formulário de login.
     */
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }
        return view('auth/login');
    }

    /**
     * Tenta autenticar o usuário.
     */
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user->password)) {
            return redirect()->back()->withInput()->with('error', 'Credenciais inválidas.');
        }

        // Verifica se o usuário está ativo
        if (!isset($user->is_active) || !$user->is_active) {
            return redirect()->back()->withInput()->with('error', 'Sua conta está desativada. Entre em contato com o administrador.');
        }

        $this->setUserSession($user);

        return redirect()->to('dashboard')->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Define os dados do usuário na sessão.
     */
    private function setUserSession($user)
    {
        $sessionData = [
            'user_id'     => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'is_admin'    => (bool) $user->is_admin,
            'isLoggedIn'  => true,
            'user_object' => $user, // Adiciona o objeto completo do usuário
        ];

        session()->set($sessionData);
    }

    /**
     * Faz o logout do usuário.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login')->with('success', 'Você foi desconectado.');
    }
}