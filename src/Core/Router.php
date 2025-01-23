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
       

        // Verifica se o idioma está na URL
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        if (!preg_match('/^\/(pt|en)\//', $uri)) {
            $language = LanguageDetector::detectLanguage()['language'] ?? 'pt';
            header("Location: /$language$uri");
            exit;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = rtrim($uri, '/'); // Remove barra final

        $action = $this->routes[$method][$uri] ?? null;

        if ($action) {
            [$class, $method] = $action;
            (new $class())->$method();
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
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
