<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\App;
use App\Core\ViewHelper;

$currentLanguage = App::getLanguage();


// use App\Utils\Translation;
// use App\Utils\LanguageDetector;
// use App\Utils\Security;

// use App\Controllers\AuthController;

// // Detectar idioma pelo caminho da URL
// $languageData = LanguageDetector::detectLanguage();
// $currentLanguage; = $languageData['languages']; // 'pt' ou 'en'

// Translation::init($currentLanguage;);

// $authController = new AuthController();

// // Verificar envio do formulário
// if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['resend_activation']) && $_POST['resend_activation'] == 0)) {
//     $authController->accessLogin($_POST);
// }
// if (isset($_GET['error'])) {
//     $error = $_GET['error'];
// }

// if (isset($_GET['success'])) {
//     $success = $_GET['success'];
// }

// if(isset($_POST['resend_activation']) && $_POST['resend_activation'] == 1)  {
//     $authController->resendActivationEmail($_POST['email']);
// }


// Security::initializeSessionSecurity();


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
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center"><?= _($error) ?>
                                <?php if (isset($_GET['error']) && strpos($_GET['error'], 'não ativada') !== false): ?>
                                    <form action="" method="POST">
                                        <button type="submit" class="btn btn-primary">Reenviar E-mail de Ativação</button>
                                        <input type="hidden" name="resend_activation" value="1">
                                        <input type="hidden" name="email" value="<?= base64_decode($_GET['cli']) ?>">
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success text-center"><?= _($success) ?></div>
                        <?php endif; ?>

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
                                    <a href="/<?= $currentLanguage; ?>/forgot-password.php" class="text-decoration-none"><?= _('Esqueceu sua senha?') ?></a>
                                </div>
                                <div class="col">
                                    <a href="/<?= $currentLanguage; ?>/register.php" class="text-decoration-none"><?= _('Registrar novo usuário') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle via jsDelivr -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../../public/assets/js/float-script.js"></script>
</body>

</html>