<?php
include __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\Helpers\Helper;
use App\Core\LanguageDetector;
use App\Core\Security;
use App\Core\SessionManager;
use App\Core\ViewHelper;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

Security::initializeSessionSecurity();

// Recupera informações do usuário
$role = SessionManager::get('roleName', 'guest'); // Se não houver, assume 'guest'
$twoFactorEnabled = SessionManager::get('two_factor_enabled', false);

$ip = Helper::getClientIP();
$pais = Helper::getCountryByIPv6($ip);
?>
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Empresa</title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>

</head>

<body data-country="<?= strtolower($pais) ?>" data-lang="<?= $currentLanguage ?>">
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>

    <div class="container mt-5">
        <h2>➕ Adicionar Novo Evento</h2>

        <form action="/agenda/add" method="POST">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" class="form-control" name="title" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea class="form-control" name="description"></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Início</label>
                    <input type="datetime-local" class="form-control" name="start_time" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fim</label>
                    <input type="datetime-local" class="form-control" name="end_time" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <select class="form-select" name="category">
                    <option value="Pessoal">Pessoal</option>
                    <option value="Trabalho">Trabalho</option>
                    <option value="Lembrete">Lembrete</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Cor do Evento</label>
                <input type="color" class="form-control" name="color" value="#007bff">
            </div>

            <button type="submit" class="btn btn-primary w-100">Salvar Evento</button>
        </form>
    </div>

    <?php require_once __DIR__ . '/../../../inc/footer.php'; ?>
</body>

</html>