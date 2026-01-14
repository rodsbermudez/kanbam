<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>
<?php
// --- HISTÓRICO DE VERSÕES ---
// Adicione novas versões no topo do array. A primeira é sempre a mais recente.
$changelog = [
    '1.1.7' => [
        'date' => '2025-11-24',
        'items' => [
            '<strong>Permissões:</strong> Ajuste no carregamento de dados globais para permitir que usuários restritos visualizem a sidebar corretamente.',
            '<strong>Projetos:</strong> Correção na listagem de projetos para evitar erro de duplicidade (join) ao filtrar por usuário.',
            '<strong>Interface:</strong> A contagem de projetos nas abas (ativos/concluídos) agora reflete apenas os projetos que o usuário tem permissão de acessar.',
        ]
    ],
    '1.1.6' => [
        'date' => '2025-11-23',
        'items' => [
            '<strong>Sidebar de Tarefas:</strong> Reformulada para exibir tarefas em abas (Atrasadas, Próximas, Aguardando Cliente) e notificar apenas sobre tarefas com atraso real.',
            '<strong>Layout:</strong> Ajustes gerais no layout para melhorar a consistência visual com os novos temas.',
            '<strong>Temas:</strong> Adicionados 2 novos temas: Cosmo e Superhero.',
            '<strong>Temas:</strong> Removidos 4 temas para simplificar a seleção.',
        ]
    ],
    '1.1.5' => [
        'date' => '2024-07-26',
        'items' => [
            '<strong>Integração:</strong> Criado um endpoint (`/reports/daily-summary`) para gerar resumos diários de tarefas por usuário, destinado a automações com n8n e Slack.',
            '<strong>Correção:</strong> Corrigido um erro que ocorria ao atualizar tarefas em projetos que não possuíam um canal do Slack configurado.',
            '<strong>Ajuste:</strong> A estrutura do JSON do relatório diário foi ajustada para agrupar os usuários sob uma chave "users".',
        ]
    ],
    '1.1.4' => [
        'date' => '2024-07-26',
        'items' => [
            '<strong>Projetos:</strong> Adicionada a funcionalidade "Adiar Início do Projeto", que permite reagendar todas as tarefas de um projeto com base em uma nova data de início.',
        ]
    ],
    '1.1.3' => [
        'date' => '2024-07-26',
        'items' => [
            '<strong>Dashboard:</strong> As tarefas de projetos concluídos foram removidas de todos os quadros do dashboard (desktop e mobile).',
        ]
    ],
    '1.1.2' => [
        'date' => '2024-07-26',
        'items' => [
            '<strong>Dashboard:</strong> Reorganização dos quadros de tarefas atrasadas em três categorias: "Concluídas (Atrasadas)", "Outras Tarefas Atrasadas" e "Aguardando Cliente / Aprovação".',
            '<strong>Dashboard:</strong> O quadro "Aguardando Cliente / Aprovação" agora agrupa tarefas com status "Com cliente" e "Aprovação".',
            '<strong>Dashboard:</strong> Reordenação dos cards do dashboard para desktop (layout 2x2) e mobile.',
            '<strong>Dashboard:</strong> Correção da exibição do nome do usuário na mensagem de boas-vindas.',
            '<strong>Geral:</strong> Criação da página de Histórico de Atualizações (changelog).',
            '<strong>Geral:</strong> A versão da aplicação no rodapé agora é lida dinamicamente a partir deste arquivo.',
        ]
    ],
    '1.1.1' => [
        'date' => '2024-07-25',
        'items' => ['Versão inicial com funcionalidades de gestão de projetos, tarefas e clientes.']
    ]
];
?>

<main class="container mt-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Histórico de Atualizações</h1>
            <p class="lead text-muted">Acompanhe as novidades e melhorias da plataforma.</p>
        </div>
    </div>
    
    <?php foreach ($changelog as $version => $details): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Versão <?= $version ?> - <?= date('d/m/Y', strtotime($details['date'])) ?></h5>
            </div>
            <div class="card-body">
                <ul>
                    <?php foreach ($details['items'] as $item): ?>
                        <li><?= $item ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Placeholder para futuras atualizações -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Próximas Atualizações...</h5>
        </div>
        <div class="card-body text-muted">
            <p>Novas funcionalidades e melhorias serão adicionadas aqui em futuras versões.</p>
        </div>
    </div>

</main>

<?= $this->include('partials/footer') ?>