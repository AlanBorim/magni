<?php
ini_set('display_errors', '1');
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
    <title>Redefinir Senha</title>
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
            <!-- Coluna da direita (Formulário de Registro) -->
            <div class="col-md-6 d-flex flex-column">
                <!-- Barra de seleção de idioma -->
                <div class="w-100 py-2 border-bottom text-end">
                    <a href="/pt/reset-password?token=<?php echo htmlspecialchars($token); ?>"><img src="../../../../public/assets/images/flags/pt.png" alt="Português" title="Português" width="20" height="20"></a>
                    <a href="/en/reset-password?token=<?php echo htmlspecialchars($token); ?>" class="me-2"><img src="../../../../public/assets/images/flags/en.png" alt="English" title="English" width="20" height="20"></a>
                </div>

                <div class="d-flex justify-content-center align-items-center flex-grow-1">
                    <div class="w-75">
                        <h3 class="text-center">Redefinir Senha</h3>
                        <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php');?>

                        <form method="POST" action="/<?= $currentLanguage ?>/reset-password">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                            <div class="form-group mb-3">
                                <label for="password">Nova Senha:</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="confirm_password">Confirmar Nova Senha:</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
                            <div class="text-center mt-3">
                                <a href="/<?= $currentLanguage ?>/" class="text-decoration-none">Voltar para o Login</a>
                            </div>
                        </form>
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