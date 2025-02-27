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
        <h2>✏️ Editar Evento</h2>

        <form action="/agenda/update" method="POST">
            <input type="hidden" name="id" value="<?= $event['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea class="form-control" name="description"><?= htmlspecialchars($event['description']) ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Início</label>
                    <input type="datetime-local" class="form-control" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($event['start_time'])) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fim</label>
                    <input type="datetime-local" class="form-control" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($event['end_time'])) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <select class="form-select" name="category">
                    <option value="Pessoal" <?= $event['category'] === 'Pessoal' ? 'selected' : '' ?>>Pessoal</option>
                    <option value="Trabalho" <?= $event['category'] === 'Trabalho' ? 'selected' : '' ?>>Trabalho</option>
                    <option value="Lembrete" <?= $event['category'] === 'Lembrete' ? 'selected' : '' ?>>Lembrete</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Cor do Evento</label>
                <input type="color" class="form-control" name="color" value="<?= $event['color'] ?>">
            </div>

            <button type="submit" class="btn btn-warning w-100">Atualizar Evento</button>
        </form>
    </div>

    <?php require_once __DIR__ . '/../../../inc/footer.php'; ?>
</body>

</html>