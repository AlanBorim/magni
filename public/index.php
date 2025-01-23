<?php
ini_set('display_errors','1');
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\App;
use App\Core\LanguageDetector;
use App\Core\Translation;
use App\Core\Router;

// Inicia a sessão
session_start();

// Detecta o idioma pela URL ou sessão
if (isset($_SESSION['language'])) {
    $selectedLanguage = $_SESSION['language'];
} else {
    $languageData = LanguageDetector::detectLanguage();
    $selectedLanguage = $languageData['language'];
    $_SESSION['language'] = $selectedLanguage;
}

if (!preg_match('/^\/(pt|en)\//', $_SERVER['REQUEST_URI'])) {
    $cleanUri = preg_replace('/^\/(pt|en)/', '', $_SERVER['REQUEST_URI']); // Remove idioma duplicado, se existir
    header("Location: /$selectedLanguage$cleanUri");
    exit;
}

// Carrega as variáveis do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Inicializa traduções
Translation::init($selectedLanguage);

// Configura o roteador
$router = new Router();
$router->loadModuleRoutes(__DIR__ . '/../src/Modules');
$router->handleRequest();
