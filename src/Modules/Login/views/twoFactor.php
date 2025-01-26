<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\FlashMessages;
use App\Core\ViewHelper;
use App\Core\LanguageDetector;

$currentLanguage = LanguageDetector::detectLanguage()['language'];
$messages = FlashMessages::getFlash();

?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _("Twofa title") ?></title>
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
                <div class="w-100 py-2 text-end">
                    
                </div>

                <div class="d-flex justify-content-center align-items-center flex-grow-1">
                    <div class="w-75">
                        <h3 class="text-center mb-4"><?= _("Twofa title") ?></h3>
                        <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php'); ?>
                        <form method="POST" action="/<?= $currentLanguage; ?>/two-factor-check">
                            <div class="mb-3">
                                <label for="code" class="form-label"><?= _("Twofa subtitle") ?></label>
                                <input type="text" id="code" name="two_factor_code" class="form-control" required maxlength="6" pattern="\d{6}" placeholder="<?= _("Twofa placeholder") ?>">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3"><?= _("Twofa button") ?></button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="/<?= $currentLanguage ?>/logout" class="text-decoration-none"><?= _("Twofa out link") ?></a>
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