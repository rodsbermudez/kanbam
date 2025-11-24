    
    <!-- Bootstrap JS (necessário para funcionalidades como dropdowns, modais, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Lógica para o Seletor de Tema do Portal do Cliente ---
        const themeLink = document.getElementById('bootstrap-theme');
        const themeSwitcher = document.getElementById('clientThemeSwitcher');
        // O tema padrão para o cliente é 'sandstone'
        const storageKey = 'kanban_theme_client';
        const defaultTheme = 'sandstone';
        const currentTheme = localStorage.getItem(storageKey) || defaultTheme;

        function applyTheme(themeName) {
            const themeUrl = `https://bootswatch.com/5/${themeName}/bootstrap.min.css`;
            if (themeLink) {
                themeLink.setAttribute('href', themeUrl);
            }
            localStorage.setItem(storageKey, themeName);
        }

        if (themeSwitcher) {
            themeSwitcher.addEventListener('click', function(e) {
                const themeItem = e.target.closest('[data-theme]');
                if (themeItem) {
                    e.preventDefault();
                    applyTheme(themeItem.dataset.theme);
                }
            });
        }

        // Aplica o tema salvo ou o padrão ao carregar a página
        applyTheme(currentTheme);
    });
    </script>
</body>
</html>