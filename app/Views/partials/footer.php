<footer class="container-fluid text-center text-muted small py-3 mt-4">
    Kanban v<?= config('App')->version ?? '1.0.0' ?>
</footer>

<?= $this->include('partials/theme_switcher') ?>
<?= $this->include('partials/fab') ?>
<?= $this->include('partials/due_tasks_sidebar') ?>
<?= $this->include('partials/toasts') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" async></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Clickable table rows
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't navigate if the click is on a link, button, or input inside the row
            if (e.target.closest('a, button, input')) {
                return;
            }
            window.location.href = this.dataset.href;
        });
    });

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

    // --- Lógica para o Seletor de Projetos Global ---
    const projectSearchEl = document.getElementById('globalProjectSearch');
    if (projectSearchEl) {
        new TomSelect(projectSearchEl, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'client_tag'], // Mantém a busca no lado do cliente para os dados pré-carregados
            preload: true, // Carrega os projetos ao iniciar
            // Custom rendering
            render: {
                option: function(data, escape) {
                    let tagHtml = '';
                    if (data.client_tag) {
                        tagHtml = `<span class="badge me-2" style="background-color: ${escape(data.client_color || '#6c757d')}; color: #fff;">${escape(data.client_tag)}</span>`;
                    }
                    return `<div>${tagHtml}${escape(data.name)}</div>`;
                },
                item: function(data, escape) {
                    // Para o item selecionado, mostramos apenas o nome para manter a navbar limpa.
                    return `<div>${escape(data.name)}</div>`;
                }
            },
            // Load data via AJAX
            load: function(query, callback) {
                const url = `<?= site_url('projects/list-for-select') ?>?search=${encodeURIComponent(query)}`;
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(json => {
                    if (json.success) callback(json.projects);
                    else callback();
                }).catch(()=> callback());
            },
            // Redirect on change
            onChange: function(value) {
                if (value) {
                    window.location.href = `<?= site_url('admin/projects/') ?>${value}`;
                }
            }
        });
    }
});
</script>
</body>
</html>