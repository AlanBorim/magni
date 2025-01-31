<?php
ini_set('display_errors', 1);
include __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\LanguageDetector;
use App\Core\Security;
use App\Core\SessionManager;
use App\Core\ViewHelper;
use App\Modules\Login\LoginController;

$currentLanguage = LanguageDetector::detectLanguage()['language'];
$loginController = new LoginController();

Security::enforceSessionSecurity();
SessionManager::renewSession();

$role = $_SESSION['roleName']; // Permissões do usuário
$twoFactorEnabled = $_SESSION['two_factor_enabled']; // Adicionei essa variável para verificar se o 2FA está habilitado

$qrCodeUrl = $loginController->getQrCode2fa();
?>
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _('Config 2fa title') ?></title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
</head>

<body>
    <!-- Menu -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>

    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php'); ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center bg-primary text-white" style="background-color: #ff8a00!important;">
                        <h3><?= _('Config 2fa title') ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($twoFactorEnabled) && $twoFactorEnabled == 1): ?>
                            <p class="text-success">A autenticação de dois fatores está habilitada.</p>
                            <a href="/<?= $currentLanguage ?>/dashboard" class="btn btn-link">Acessar a dashboard</a>
                        <?php else: ?>
                            <?php if (!$twoFactorEnabled && isset($qrCodeUrl['qrCode'])): ?>
                                <form method="post" action="/<?= $currentLanguage ?>/process2fa">
                                    <div class="row">
                                        <!-- Coluna Direita -->
                                        <div class="col-md-6 text-center d-flex flex-column justify-content-center">
                                            <h5 class="mb-3"><?= _('Config 2fa subtitle') ?></h5>
                                            <p><?= _('Config 2fa desc') ?></p>
                                            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" style="width: 150px;">
                                            </a>
                                        </div>

                                        <!-- Coluna Esquerda -->
                                        <div class="col-md-6 text-center">
                                            <p><?= _('Config 2fa qr title') ?></p>
                                            <img src="<?= $qrCodeUrl['qrCode'] ?>" alt="QRCode" class="img-fluid mb-3" />
                                            <p><?= _('Config 2fa qr text') ?></p>
                                            <p><strong><?= htmlspecialchars($qrCodeUrl['secret']) ?></strong></p>
                                        </div>
                                    </div>
                                    <!-- Linha inferior ocupando ambas as colunas -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="form-group text-center">
                                                <label for="two_factor_code"><?= _('Config 2fa code validate') ?></label>
                                                <input type="text" id="two_factor_code" name="two_factor_code" class="form-control text-center" maxlength="6" required />
                                                <input type="hidden" name="secret" value="<?= $qrCodeUrl['secret'] ?>" />
                                            </div>
                                            <button type="submit" name="enable_2fa" class="btn btn-success w-100 mt-3"><?= _('Config 2fa button') ?></button>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
</body>

</html>