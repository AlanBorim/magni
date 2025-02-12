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
    <title><?= _('Login') ?></title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
    <link href="../../../../public/assets/css/float-style.css" rel="stylesheet">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
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
                        $resend = 0;
                        ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php', $resend);

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
                            <!-- Configuração do Google Sign-In -->
                            <div class="g-signin2" data-onsuccess="onSignIn" data-clientid="9085418514-56aalmhup0tj5eekk1u9ggu3a6rqvnt3.apps.googleusercontent.com"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
    <script src="../../../../public/assets/js/float-script.js"></script>
    <script>
        function onSignIn(googleUser) {
            // Obtém o ID token do Google
            var id_token = googleUser.getAuthResponse().id_token;

            // Envia o token para o backend PHP para autenticação
            fetch('backend.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'token=' + id_token
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Usuário autenticado com sucesso, redireciona ou faz login
                        window.location.href = "dashboard.php"; // Por exemplo
                    } else {
                        console.error('Erro:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro ao autenticar:', error);
                });
        }
    </script>
</body>

</html>