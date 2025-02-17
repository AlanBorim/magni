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
        const navbar = document.querySelector(".navbar");
        const sidebar = document.getElementById("sidebar"); // Obtém o sidebar

        const ThemeManager = (() => {
            const THEME_KEY = "preferencesTheme";
            const DEFAULT_THEME = "light";

            const getSavedTheme = () => {
                return localStorage.getItem(THEME_KEY) || DEFAULT_THEME;
            };

            const saveTheme = (theme) => {
                localStorage.setItem(THEME_KEY, theme);
            };

            const applyTheme = (theme) => {
                htmlElement.setAttribute("data-bs-theme", theme);

                if (navbar) {
                    navbar.setAttribute("data-bs-theme", theme);
                }

                if (sidebar) {
                    sidebar.setAttribute("data-theme", theme);
                }
            };

            const toggleTheme = (isDarkMode) => {
                const newTheme = isDarkMode ? "dark" : "light";
                applyTheme(newTheme);
                saveTheme(newTheme);
            };

            return {
                getSavedTheme,
                applyTheme,
                toggleTheme,
            };
        })();

        const initializeTheme = () => {
            // Recupera o tema salvo
            const savedTheme = ThemeManager.getSavedTheme();

            // Aplica o tema salvo
            ThemeManager.applyTheme(savedTheme);

            // Seleciona o elemento do switch
            const darkModeSwitch = document.getElementById("darkModeSwitch");

            // Verifica se o elemento existe antes de acessar a propriedade
            if (darkModeSwitch) {
                darkModeSwitch.checked = savedTheme === "dark";

                darkModeSwitch.addEventListener("change", () => {
                    ThemeManager.toggleTheme(darkModeSwitch.checked);
                });
            }
        };

        initializeTheme();
    });


    // Lista de páginas onde o timer NÃO deve rodar
    const publicPages = ['pt', 'en', 'login', 'register', 'forgot-password', 'verify-2fa', 'two-factor', 'reset-password'];

    // Obtém a URL atual e extrai a última parte (exemplo: 'dashboard')
    const currentPage = window.location.pathname.split('/').filter(Boolean).pop();

    // Verifica se a página atual está na lista de páginas públicas
    if (!publicPages.includes(currentPage)) {
        // Função para atualizar a contagem regressiva da sessão
        function updateSessionTimer() {
            fetch('/pt/sessionTime')
                .then(response => response.json())
                .then(data => {
                    const remainingTime = data.remainingTime; // Tempo restante em segundos

                    if (remainingTime <= 0) {
                        // Sessão expirada
                        document.getElementById('session-timer').textContent = 'Sessão expirada!';
                        window.location.href = '/'; // Redireciona para a página de login
                    } else {
                        // Converte o tempo restante para minutos e segundos
                        const minutes = Math.floor(remainingTime / 60);
                        const seconds = remainingTime % 60;

                        // Exibe o tempo restante no elemento correto
                        document.getElementById('session-timer').textContent =
                            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    }
                })
                .catch(error => console.error('Erro ao obter o tempo da sessão:', error));
        }

        // Atualiza o timer a cada segundo
        setInterval(updateSessionTimer, 1000);

        // Executa a função imediatamente ao carregar a página
        updateSessionTimer();
    }
</script>