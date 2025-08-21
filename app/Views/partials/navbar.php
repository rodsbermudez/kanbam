<!-- app/Views/partials/navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('dashboard') ?>">Kanban</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('dashboard') ?>">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Projetos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= site_url('admin/projects') ?>">Gerenciar Projetos</a></li>
                        <?php if (session()->get('is_admin')): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= site_url('admin/project-type') ?>">Tipos de Projeto (IA)</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php if (session()->get('is_admin')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/users') ?>">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/clients') ?>">Clientes</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item position-relative">
                    <a class="nav-link" href="#" id="open-due-tasks-sidebar" title="Tarefas Atrasadas e Próximas">
                        <i class="bi bi-calendar-check fs-5"></i>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle d-none" id="due-tasks-count" style="font-size: 0.6em; margin-top: 0.5rem;"></span>
                    </a>
                </li>
                <li class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= esc(session()->get('user_name')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                        <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>