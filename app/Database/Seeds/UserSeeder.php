<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = new \App\Models\UserModel();

        // Dados do usuário administrador de teste
        $data = [
            'name'     => 'Administrador',
            'email'    => 'admin@kanban.com',
            'password' => 'password123', // <-- Você define a senha aqui em texto puro
            'is_admin' => 1,
        ];

        // O UserModel irá interceptar a inserção e
        // criptografar a senha automaticamente antes de salvar.
        $userModel->insert($data);

        // Mensagem para o console
        CLI::write('Usuário de teste criado: admin@kanban.com | senha: password123', 'green');
    }
}