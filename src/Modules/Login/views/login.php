<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\FlashMessages;
use App\Core\ViewHelper;
use App\Core\LanguageDetector;


$currentLanguage = LanguageDetector::detectLanguage()['language'];
$messages = FlashMessages::getFlash();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _('Login') ?></title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
    <link href="../../../../public/assets/css/float-style.css" rel="stylesheet">

</head>

<body>

    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Coluna da esquerda (Logo) -->
            <div class="col-md-6 d-flex justify-content-center align-items-center" style="background: linear-gradient(90deg, #818181, #ffffff);">
                <div class="container-float">
                    <div class="image-wrapper">
                        <img src="../../../../public/assets/images/logo.png" alt="Logo" class="img-fluid">
                        <div class="shadow"></div>
                    </div>
                </div>

            </div>

            <!-- Coluna da direita (Formulário de Login) -->
            <div class="col-md-6 d-flex flex-column">
                <!-- Barra de seleção de idioma -->
                <div class="w-100 py-2 border-bottom text-end">
                    <a href="/pt/"><img src="../../../../public/assets/images/flags/pt.png" alt="Português" title="Português" width="20" height="20"></a>
                    <a href="/en/" class="me-2"><img src="../../../../public/assets/images/flags/en.png" alt="English" title="English" width="20" height="20"></a>
                </div>

                <div class="d-flex justify-content-center align-items-center flex-grow-1">
                    <div class="w-75">
                        <h3 class="text-center mb-4"><?= _('Bem-vindo') ?></h3>
                        <?php
                        ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php');

                        $resend = null;

                        if ($resend == 1) {
                        ?>
                            <div class="text-center align-items-center flex-grow-1">
                                <form method="POST" action="/<?= $currentLanguage; ?>/resendActivationEmail">
                                    <button type="submit" class="btn btn-primary">Reenviar E-mail de Ativação</button>
                                    <input type="hidden" name="resend_activation" value="1">
                                    <input type="hidden" name="id" value="<?= $_SESSION['user_id'] ?>">
                                </form>
                            </div>
                        <?php } ?>
                        <form method="POST" action="/<?= $currentLanguage; ?>/login">
                            <div class="mb-3">
                                <label for="email" class="form-label"><?= _('E-mail:') ?></label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><?= _('Senha:') ?></label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <input type="hidden" name="resend_activation" value="0">
                            <button type="submit" class="btn btn-primary w-100 mt-3" id="btnEntrar"><?= _('Entrar') ?></button>
                        </form>

                        <div class="text-center mt-3">
                            <div class="row">
                                <div class="col">
                                    <a href="/<?= $currentLanguage; ?>/forgot-password" class="text-decoration-none"><?= _('Esqueceu sua senha?') ?></a>
                                </div>
                                <div class="col">
                                    <a href="/<?= $currentLanguage; ?>/register" class="text-decoration-none"><?= _('Registrar novo usuário') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
    <script src="../../../../public/assets/js/float-script.js"></script>
</body>

</html>