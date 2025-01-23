<footer class="footer mt-auto">
    <div class="container text-center">
        <p class="mb-0"><?= date('Y') ?> <?= _('Rodape') ?></p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Dark Mode -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const htmlElement = document.documentElement;
        if (document.getElementById('darkModeSwitch')) {
            const darkModeSwitch = document.getElementById('darkModeSwitch');
        }
        const navbar = document.querySelector('.navbar');

        const ThemeManager = (() => {
            const THEME_KEY = 'preferencesTheme';
            const DEFAULT_THEME = 'light';

            const getSavedTheme = () => {
                return localStorage.getItem(THEME_KEY) || DEFAULT_THEME;
            };

            const saveTheme = (theme) => {
                localStorage.setItem(THEME_KEY, theme);
            };

            const applyTheme = (theme) => {
                htmlElement.setAttribute('data-bs-theme', theme);
                if (navbar) {
                    navbar.setAttribute('data-bs-theme', theme);
                }
            };

            const toggleTheme = (isDarkMode) => {
                const newTheme = isDarkMode ? 'dark' : 'light';
                applyTheme(newTheme);
                saveTheme(newTheme);
            };

            return {
                getSavedTheme,
                applyTheme,
                toggleTheme
            };
        })();

        const initializeTheme = () => {
            // Recupera o tema salvo
            const savedTheme = ThemeManager.getSavedTheme();

            // Aplica o tema salvo
            ThemeManager.applyTheme(savedTheme);

            // Seleciona o elemento do switch
            const darkModeSwitch = document.getElementById('darkModeSwitch');

            // Verifica se o elemento existe antes de acessar a propriedade
            if (darkModeSwitch) {
                darkModeSwitch.checked = savedTheme === 'dark';
                const setupEventListeners = () => {
                    darkModeSwitch.addEventListener('change', () => {
                        ThemeManager.toggleTheme(darkModeSwitch.checked);
                    });
                };
                setupEventListeners();
            }
        };


        initializeTheme();
    });
</script>