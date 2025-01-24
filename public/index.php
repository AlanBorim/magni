<?php
ini_set('display_errors', '1');

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\App;
use App\Core\LanguageDetector;
use App\Core\Translation;
use App\Core\Router;

// Carrega as variáveis do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Inicializa traduções
Translation::init(LanguageDetector::detectLanguage()['language']);

// Configura o roteador
$router = new Router();
$router->loadModuleRoutes(__DIR__ . '/../src/Modules');
$router->handleRequest();
