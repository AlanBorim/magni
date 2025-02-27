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
        <h2 class="mb-4">📋 Meus Eventos</h2>

        <div class="d-flex justify-content-end mb-3">
            <a href="/agenda/add" class="btn btn-success">➕ Adicionar Evento</a>
        </div>

        <?php if (empty($events)): ?>
            <div class="alert alert-info text-center">
                Nenhum evento cadastrado. Clique em <strong>Adicionar Evento</strong> para começar.
            </div>
        <?php else: ?>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= htmlspecialchars($event['description']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($event['start_time'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($event['end_time'])) ?></td>
                            <td>
                                <span class="badge" style="background-color: <?= $event['color'] ?>;">
                                    <?= htmlspecialchars($event['category']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/agenda/edit?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="/agenda/delete?id=<?= $event['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este evento?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/../../../inc/footer.php'; ?>
</body>

</html>