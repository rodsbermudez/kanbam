<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kanban') ?> - Kanban</title>
    <link id="bootstrap-theme" href="https://bootswatch.com/5/slate/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Paleta de cores personalizada */
        /* 
        Cores personalizadas comentadas para restaurar o tema original Superhero.
        Para reativar, basta remover os marcadores de comentário '/*' e '* /'.
        
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
        */

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

        /* Corrige a cor do texto no hover dos itens da lista no dashboard */
        .list-group-item-action:hover h6,
        .list-group-item-action:focus h6 {
            color: #fff;
        }
    </style>
</head>
<body>