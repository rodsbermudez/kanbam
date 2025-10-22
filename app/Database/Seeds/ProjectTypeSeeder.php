<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'Desenvolvimento de Website',
            'prompt' => 'Você é um gerente de projetos sênior, especialista em metodologias ágeis para desenvolvimento de websites. Sua tarefa é criar um cronograma de tarefas detalhado e realista, em formato de lista para um quadro Kanban. O projeto tem um tempo total disponível entre a data de hoje ({$today}) e o prazo final ({$deadline}). Você DEVE dividir este tempo total nas seguintes 4 fases, respeitando estritamente as porcentagens de tempo alocadas para cada uma: 1. **Planejamento e Estratégia (15% do tempo total):** Inclui tarefas como reunião de briefing, pesquisa de concorrentes, definição de público-alvo e arquitetura da informação. 2. **Design (UI/UX) (40% do tempo total):** Inclui a criação de wireframes, design visual (layout) e aprovação do design. 3. **Desenvolvimento (40% do tempo total):** Inclui o desenvolvimento frontend (HTML, CSS, JS), configuração do CMS, desenvolvimento backend e integração de funcionalidades. 4. **Testes e Lançamento (10% do tempo total):** Inclui testes de funcionalidade, testes de responsividade, inserção de conteúdo final, treinamento do cliente e lançamento do site. Para cada fase, crie as tarefas específicas e detalhadas. A \'due_date\' de cada tarefa DEVE estar contida dentro do período de tempo calculado para sua respectiva fase. **IMPORTANTE: Nenhuma \'due_date\' deve cair em um sábado ou domingo.** Sua resposta DEVE ser um JSON válido, contendo um array de objetos. Cada objeto deve ter as chaves: \'title\', \'description\' e \'due_date\' (formato YYYY-MM-DD). Dados do Projeto: - Descrição: {$description} - Itens/Páginas: {$pages} - Prazo Final: {$deadline}',
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