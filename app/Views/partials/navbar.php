<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url('admin/dashboard') ?>">
            <img src="<?= base_url('logo-patropi.svg') ?>" alt="Patropi" style="height: 30px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/projects') ?>">Projetos</a>
                </li>
                <?php if (session()->get('is_admin')): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/clients') ?>">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/users') ?>">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('admin/project-type') ?>">Tipos de Projeto</a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center ms-auto">
                <!-- Global Project Search -->
                <div class="me-3" style="width: 500px;">
                    <select id="globalProjectSearch" placeholder="Buscar projeto..."></select>
                </div>
            </div>
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <!-- Due Tasks Sidebar Trigger -->
                    <button class="nav-link position-relative" type="button" id="open-due-tasks-sidebar" title="Tarefas Próximas" style="background: transparent; border: none;">
                        <i class="bi bi-bell"></i>
                        <span id="due-tasks-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                            <!-- Count will be inserted here by JS -->
                        </span>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= esc(session()->get('user_name')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>