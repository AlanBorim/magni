<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\ViewHelper;
use App\Core\LanguageDetector;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _('Recover title') ?></title>
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
                    <a href="/pt/forgot-password"><img src="../../../../public/assets/images/flags/pt.png" alt="Português" title="Português" width="20" height="20"></a>
                    <a href="/en/forgot-password" class="me-2"><img src="../../../../public/assets/images/flags/en.png" alt="English" title="English" width="20" height="20"></a>
                </div>

                <div class="d-flex justify-content-center align-items-center flex-grow-1">
                    <div class="w-75">
                        <h3 class="text-center"><?= _('Recover title') ?></h3>

                        <?php
                        ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php');
                        ?>
                        <form method="POST" action="/<?= $currentLanguage ?>/forgotPassword">
                            <div class="form-group">
                                <label for="email"><?= _('E-mail:') ?></label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <input type="hidden" name="submit" value="1">
                            <button type="submit" class="btn btn-primary w-100 mt-4"><?= _('Recover link') ?></button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="/<?= $currentLanguage ?>/" class="text-decoration-none"><?= _('Recover back') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
    <script src="../../../../public/assets/js/float-script.js"></script>

</html>