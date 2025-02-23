<?php

namespace App\Core;

class Router
{
    protected $routes = [];
    protected $languageBasePath;

    public function __construct()
    {
        $languageData = LanguageDetector::detectLanguage();
        $this->languageBasePath = $languageData['basePath']; // Define o caminho base com o idioma
    }

    /**
     * Registra uma rota GET com o prefixo de idioma.
     */
    public function get($uri, $action)
    {
        $normalizedUri = rtrim($this->languageBasePath . $uri, '/'); // Remove barra final
        $this->routes['GET'][$normalizedUri] = $action;
        $this->routes['GET'][$normalizedUri . '/'] = $action; // Adiciona a rota com barra final
    }

    public function post($uri, $action)
    {
        $normalizedUri = rtrim($this->languageBasePath . $uri, '/'); // Remove barra final
        $this->routes['POST'][$normalizedUri] = $action;
        $this->routes['POST'][$normalizedUri . '/'] = $action; // Adiciona a rota com barra final
    }

    /**
     * Processa a requisição e executa a rota correspondente.
     */
    public function handleRequest()
    {
        // Obtém a URI completa (path e query string)
        $fullUri = $_SERVER['REQUEST_URI'];

        // Separa o caminho e a query string usando parse_url()
        $path = parse_url($fullUri, PHP_URL_PATH);
        $query = parse_url($fullUri, PHP_URL_QUERY);

        // Verifica se a URL contém o idioma no início (pt ou en)
        if (!preg_match('/^\/(pt|en)\//', $path)) {
            $language = LanguageDetector::detectLanguage()['language'] ?? 'pt';
            // Reconstrói a URL de redirecionamento, anexando a query string se existir
            if ($query) {
                // Substitui quaisquer "?" por "&"
                $cleanQuery = str_replace('?', '&', $query);
                // Remove um possível "&" inicial, caso exista
                $cleanQuery = ltrim($cleanQuery, '&');
                $redirectUrl = "/$language" . $path . "?$cleanQuery";
            } else {
                $redirectUrl = "/$language" . $path;
            }
            header("Location: " . $redirectUrl);
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        // Remove a barra final do caminho
        $path = rtrim($path, '/');

        // Percorre as rotas registradas para encontrar correspondência
        foreach ($this->routes[$method] as $routePattern => $action) {
            // Converte padrões de rota dinâmicos para regex
            $pattern = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $routePattern);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove o primeiro item (path completo)
                [$class, $method] = $action;
                // Os parâmetros dinâmicos são passados para o método; a query string fica disponível via $_GET
                (new $class())->$method(...$matches);
                return;
            }
        }

        // Caso a rota não seja encontrada, retorna 404
        http_response_code(404);
        include __DIR__ . '/../inc/error404.php';
    }




    /**
     * Carrega as rotas de todos os módulos.
     */
    public function loadModuleRoutes($modulesPath)
    {
        $modules = glob($modulesPath . '/*', GLOB_ONLYDIR);

        foreach ($modules as $module) {
            $routesFile = $module . '/routes.php';

            if (file_exists($routesFile)) {
                $router = $this;
                include $routesFile;
            }
        }
    }
}
