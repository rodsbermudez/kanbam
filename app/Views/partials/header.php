<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Projetos') ?> - Patropi Comunica</title>
    <link id="bootstrap-theme" href="https://bootswatch.com/5/pulse/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Paleta de cores personalizada */
        /* 
        Cores personalizadas comentadas para restaurar o tema original Superhero.
        Para reativar, basta remover os marcadores de comentário '/*' e '* /'.
        */
        :root {
            --bs-primary: #ed1e64;
            --bs-primary-rgb: 237, 30, 100;

            --bs-secondary: #7abfa7;
            --bs-secondary-rgb: 122, 191, 167;

            --bs-success: #35bd0b;
            --bs-success-rgb: 53, 189, 11;

            --bs-info: #2a6aa9;
            --bs-info-rgb: 42, 106, 169;

            --bs-warning: #bd5e0b;
            --bs-warning-rgb: 189, 94, 11;

            --bs-danger: #e6172f;
            --bs-danger-rgb: 230, 23, 47;

            --bs-light: #abb6c2;
            --bs-light-rgb: 171, 182, 194;

            --bs-dark: #1a1a1c;
            --bs-dark-rgb: 26, 26, 28;

            --bs-link-color: #ed1e64;
            --bs-link-hover-color: #c41853;
        }

        /* Custom spacing utility */
        .mt-6 {
            margin-top: 6rem !important;
        }

        /* Estilos para o Seletor de Tema */
        .theme-switcher {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            z-index: 1050;
        }
        .theme-switcher .btn {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Estilos para a Sidebar de Tarefas */
        .sidebar {
            position: fixed;
            top: 0;
            right: -450px; /* Começa fora da tela */
            width: 450px;
            max-width: 90vw;
            height: 100%;
            background-color: var(--bs-dark);
            z-index: 1055;
            transition: right 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--bs-border-color);
        }
        .sidebar.show {
            right: 0; /* Entra na tela */
        }
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid var(--bs-border-color);
            color: var(--bs-light);
        }
        .sidebar-body {
            padding: 1rem;
            overflow-y: auto;
            flex-grow: 1;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1054;
        }
        .sidebar .kanban-card {
            margin-bottom: 1rem;
        }
        .sidebar .project-name-badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Clickable table rows */
        .table-hover .clickable-row:hover {
            cursor: pointer;
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        /* Kanban Board and Card Styles */
        .kanban-wrapper {
            position: relative;
        }
        .kanban-nav-btn {
            position: fixed; /* Garante que as setas fiquem fixas na rolagem vertical */
            top: 65%;
            transform: translateY(-50%);
            z-index: 1040; /* Aumentado para evitar conflitos com outros elementos */
            background-color: rgba(var(--bs-dark-rgb), 0.7);
            border: 1px solid rgba(var(--bs-light-rgb), 0.3);
            color: var(--bs-light);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kanban-nav-btn.kanban-nav-left { left: 2.5rem; }
        .kanban-nav-btn.kanban-nav-right { right: 2.5rem; }

        .kanban-board-container {
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 1rem;
        }
        .kanban-board {
            display: inline-flex;
            gap: 1rem;
            min-width: 100%;
        }
        .kanban-column {
            min-height: 50vh;
            border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
            box-shadow: 5px 5px 10px rgba(55,94,148,.2),-5px -5px 10px rgba(255,255,255,.4);
            border-radius: 0.5rem;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            white-space: normal;
        }
        .kanban-board.kanban-width-compact .kanban-column { flex: 0 0 300px; max-width: 300px; }
        .kanban-board.kanban-width-normal .kanban-column { flex: 0 0 450px; max-width: 450px; }
        .kanban-board.kanban-width-large .kanban-column { flex: 0 0 600px; max-width: 600px; }
        
        .kanban-column-title {
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        .kanban-cards {
            flex-grow: 1;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .kanban-card {
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
            padding: 1rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            color: var(--bs-body-color); /* Garante que o texto tenha a cor padrão */
        }
        .selection-mode-active .kanban-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .kanban-card.selected {
            border: 2px solid var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.25);
            cursor: grab;
        }
        .kanban-card .card-title { margin-bottom: 0.5rem; }
        .kanban-card .card-text { font-size: 0.875rem; color: var(--bs-secondary-color); }
        .kanban-card-footer { font-size: 0.8rem; margin-top: 1rem; }
        
        /* Cores de status */
        .kanban-card.card-warning { background-color: var(--bs-warning); }
        .kanban-card.card-danger { background-color: var(--bs-danger); }
        .kanban-card.card-info { background-color: var(--bs-info); }

        /* Texto em cards coloridos */
        .kanban-card.card-warning,
        .kanban-card.card-danger,
        .kanban-card.card-info {
            color: #fff;
        }
        .kanban-card:not(.bg-light) .card-text,
        .kanban-card:not(.bg-light) .text-muted,
        .kanban-card:not(.bg-light) .card-title {
            color: #fff !important;
        }
        .kanban-card.bg-light .card-text,
        .kanban-card.bg-light .text-muted,
        .kanban-card.bg-light .card-title {
            color: var(--bs-dark) !important;
        }
        .kanban-card .btn-icon { padding: 0.1rem 0.4rem; line-height: 1; color: inherit; background: transparent; border: none; }
        .kanban-card .dropdown-menu { z-index: 1050; }
        .kanban-card-footer .user-icon-circle { border: 1px solid #fff; box-sizing: border-box; }
        
        /* Notas no card */
        .kanban-card-notes { margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.2); }
        .kanban-card.bg-light .kanban-card-notes { border-top-color: var(--bs-border-color); }
        .note-item { font-size: 0.8rem; }
        .note-item + .note-item { margin-top: 0.5rem; }
        .note-text { flex-grow: 1; background-color: rgba(255,255,255,0.1); padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #fff; word-break: break-word; }
        .kanban-card.bg-light .note-text { background-color: rgba(0,0,0,0.05); color: var(--bs-dark); }
        .note-meta { font-size: 0.7rem; color: rgba(255,255,255,0.7); }
        .kanban-card.bg-light .note-meta { color: var(--bs-secondary-color); }
        .note-meta a.delete-note-btn { color: var(--bs-danger); text-decoration: none; font-size: 0.65rem; font-weight: 500; }
        .note-meta a.delete-note-btn:hover { text-decoration: underline; }
        .kanban-card:not(.bg-light) .note-meta a.delete-note-btn { color: #fff; opacity: 0.8; }
        .kanban-card:not(.bg-light) .note-meta a.delete-note-btn:hover { opacity: 1; }

        /* Limita a largura do conteúdo e o centraliza */
        .content-constrained {
            max-width: 1500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Tom Select dark theme fix for legibility */
        .ts-control, .ts-control input {
            background-color: #f8f9fa !important; /* Standard Bootstrap light */
            color: #212529 !important; /* Standard Bootstrap dark text */
            border-color: #dee2e6 !important; /* Standard Bootstrap gray border */
        }

        /* The selected item tag inside the control */
        .ts-control .item {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }

        /* Dropdown container */
        .ts-dropdown {
            background: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }

        /* Options in the dropdown */
        .ts-dropdown .option {
            color: #212529 !important;
        }

        /* Active/hovered option in the dropdown */
        .ts-dropdown .option.active, 
        .ts-dropdown .option:hover {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }
        .ts-control .ts-clear-button {
            color: #212529 !important;
        }

        /* Estilos para os botões de ação do FAB (Floating Action Button) */
        .fab-actions .btn {
            background-color: var(--bs-secondary) !important;
            border-color: var(--bs-secondary) !important;
            color: #fff !important; /* Garante o contraste do ícone */
        }
    </style>
</head>
<body>