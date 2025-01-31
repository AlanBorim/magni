<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\ViewHelper;
use App\Core\Security;
use App\Core\LanguageDetector;
use App\Core\SessionManager;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

Security::enforceSessionSecurity();
SessionManager::renewSession();

$role = $_SESSION['roleName']; // Permissões do usuário
$twoFactorEnabled = $_SESSION['two_factor_enabled']; // Adicionei essa variável para verificar se o 2FA está habilitado

?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
</head>

<body>

    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>

    <div class="container mt-5">
        <h3><?= _('Dash welcome') ?></h3>
        <p><?= _('Dash permission') ?> <strong><?= htmlspecialchars($role) ?></strong></p>

        <!-- Verificação e notificação de 2FA -->
        <?php if (!$twoFactorEnabled): ?>
            <div class="alert alert-warning">
                <strong><?= _('Dash Attention') ?></strong> <?= _('Dash 2fa') ?>
                <a href="/<?= $currentLanguage ?>/enable2fa" class="btn btn-warning btn-sm"><?= _('Dash 2fa button') ?></a>
            </div>
        <?php endif; ?>
        <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php'); ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="alert alert-info"><?= _('Dash permission desc') ?></div>

            <div class="container mt-4">
                <div class="row">
                    <!-- Cards de administração -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= _('Dash user') ?></h5>
                                <p class="card-text"><?= _('Dash user desc') ?></p>
                                <a href="/usuarios.php" class="btn btn-primary"><?= _('Dash button') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= _('Dash clients') ?></h5>
                                <p class="card-text"><?= _('Dash clients desc') ?></p>
                                <a href="empresas.php" class="btn btn-primary"><?= _('Dash button') ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= _('Dash modules') ?></h5>
                                <p class="card-text"><?= _('Dash modules desc') ?></p>
                                <a href="/admin/modulos.php" class="btn btn-primary"><?= _('Dash button') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div id="external-modals"></div>
            <div class="alert alert-warning"><?= _('Dash permission client') ?></div>
            <div class="container mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Criar Empresa</h5>
                                <p class="card-text">Cadastre sua empresa para acessar funcionalidades exclusivas.</p>
                                <a href="criarEmpresas.php" class="btn btn-primary">Criar Empresa</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>


    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
</body>

</html>