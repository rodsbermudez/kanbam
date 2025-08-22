<footer class="container-fluid text-center text-muted small py-3 mt-4">
    Kanban v<?= config('App')->version ?? '1.0.0' ?>
</footer>

<?= $this->include('partials/theme_switcher') ?>
<?= $this->include('partials/fab') ?>
<?= $this->include('partials/due_tasks_sidebar') ?>
<?= $this->include('partials/toasts') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.map(function (toastEl) {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 3500 // 3.5 segundos
        });
        toast.show();
    });

    // --- Lógica para o Seletor de Tema ---
    const themeLink = document.getElementById('bootstrap-theme');
    const themeSwitcher = document.querySelector('.theme-switcher');
    const currentTheme = localStorage.getItem('kanban_theme') || 'slate';

    function applyTheme(themeName) {
        const themeUrl = `https://bootswatch.com/5/${themeName}/bootstrap.min.css`;
        themeLink.setAttribute('href', themeUrl);
        localStorage.setItem('kanban_theme', themeName);
    }

    if (themeSwitcher) {
        themeSwitcher.addEventListener('click', function(e) {
            const themeItem = e.target.closest('[data-theme]');
            if (themeItem) {
                e.preventDefault();
                const themeName = themeItem.dataset.theme;
                applyTheme(themeName);
            }
        });
    }

    // Aplica o tema salvo ao carregar a página
    if (currentTheme !== 'slate') {
        applyTheme(currentTheme);
    }

    // --- Lógica para a Sidebar de Tarefas Próximas ---
    const openSidebarBtn = document.getElementById('open-due-tasks-sidebar');
    const closeSidebarBtn = document.getElementById('close-due-tasks-sidebar');
    const sidebar = document.getElementById('due-tasks-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const dueTasksList = document.getElementById('due-tasks-list');
    const dueTasksCountBadge = document.getElementById('due-tasks-count');

    function toggleSidebar(show) {
        if (show) {
            sidebar.classList.add('show');
            sidebarOverlay.classList.remove('d-none');
            fetchDueTasks(); // Busca as tarefas ao abrir
        } else {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.add('d-none');
        }
    }

    function fetchDueTasks(updateCountOnly = false) {
        fetch('<?= site_url('tasks/due-soon') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (!updateCountOnly) {
                    dueTasksList.innerHTML = data.html;
                }
                // Atualiza o badge
                if (data.count > 0) {
                    dueTasksCountBadge.textContent = data.count > 9 ? '9+' : data.count;
                    dueTasksCountBadge.classList.remove('d-none');
                } else {
                    dueTasksCountBadge.classList.add('d-none');
                }
            }
        })
        .catch(err => {
            if (!updateCountOnly) dueTasksList.innerHTML = '<p class="text-danger text-center">Erro ao carregar as tarefas.</p>';
        });
    }

    if (openSidebarBtn) openSidebarBtn.addEventListener('click', (e) => { e.preventDefault(); toggleSidebar(true); });
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', () => toggleSidebar(false));
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', () => toggleSidebar(false));

    fetchDueTasks(true); // Busca a contagem inicial ao carregar a página
});
</script>
</body>
</html>