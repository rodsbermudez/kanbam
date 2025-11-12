<?= $this->include('partials/header') ?>
<?= $this->include('partials/navbar') ?>
<?php
// --- HISTÓRICO DE VERSÕES ---
// Adicione novas versões no topo do array. A primeira é sempre a mais recente.
$changelog = [
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