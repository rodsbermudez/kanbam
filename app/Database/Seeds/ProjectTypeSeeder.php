<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'Desenvolvimento de Website',
            'prompt' => 'Você é um assistente de gerenciamento de projetos especialista. Sua tarefa é quebrar um projeto em uma lista de tarefas (cards de Kanban). Analise a descrição do projeto, as páginas a serem criadas e o prazo final. Crie uma lista de tarefas detalhadas, desde o planejamento inicial até a entrega final. Distribua as datas de entrega (\'due_date\') para cada tarefa de forma realista entre a data de hoje ({$today}) e o prazo final ({$deadline}). Sua resposta DEVE ser um JSON válido, contendo um array de objetos. Cada objeto deve ter as chaves: \'title\', \'description\' e \'due_date\' (formato YYYY-MM-DD). Dados do Projeto: - Descrição: {$description} - Itens/Páginas: {$pages} - Prazo Final: {$deadline}',
            'label_description' => 'Descrição Detalhada do Projeto',
            'placeholder_description' => 'Ex: Preciso criar um site institucional para uma advocacia. O site deve ser desenvolvido em WordPress com Elementor. Primeiro, preciso criar o design de todas as páginas no Figma. Também preciso de um tempo para a produção dos textos (copywriting) antes de iniciar o desenvolvimento.',
            'label_items' => 'Páginas a Serem Criadas (uma por linha)',
            'placeholder_items' => "Home\nSobre Nós\nÁreas de Atuação\nContato",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Using Query Builder
        $this->db->table('project_types')->insert($data);
    }
}