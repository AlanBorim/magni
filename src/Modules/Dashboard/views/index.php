<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\ViewHelper;
use App\Core\LanguageDetector;
use App\Modules\Company\CompanyService;

// Obtém o idioma e o slug da empresa a partir da URL
$currentLanguage = LanguageDetector::detectLanguage()['language'];
$slug = getCompanySlugFromUrl();

if (!$slug) {
    die('Erro: Empresa não encontrada.');
}

// Verifica se a empresa existe no banco de dados
$companyService = new CompanyService();
$company = $companyService->findBySlug($slug);

if (!$company) {
    die('Erro: Empresa não encontrada no sistema.');
}

// Se a empresa existir, exibe a tela de login personalizada para a empresa
?>
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars($company['company_name']) ?></title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
</head>
<body>

    <div class="container mt-5">
        <h3>Bem-vindo à <?= htmlspecialchars($company['company_name']) ?></h3>
        <p>Faça login para acessar sua empresa.</p>

        <form action="/<?= $currentLanguage ?>/company/login" method="post">
            <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
            
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>

    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
</body>
</html>

<?php
/**
 * Captura o slug da empresa a partir da URL.
 */
function getCompanySlugFromUrl(): ?string
{
    $currentUrl = $_SERVER['REQUEST_URI'];
    $cleanedUrl = strtok($currentUrl, '?'); // Remove query strings
    $urlParts = explode('/', trim($cleanedUrl, '/'));
    
    // Verifica se o formato é /{idioma}/{slug}/
    return isset($urlParts[0]) && isset($urlParts[1]) ? $urlParts[1] : null;
}
?>
