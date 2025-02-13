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
                            <hr noshade="noshade" size="1" color="#818181">
                            <button onclick="trySampleRequest();" style="background-color: #DB4437;border: 1px solid #DB4437;padding: 10px; border-radius: 10px;"><i class="bi bi-google"></i> Fazer Login com Google</button>
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
        var YOUR_CLIENT_ID = '302734718909-nuh7oh4ftsnismh9ke11e2g1oiv87vjc.apps.googleusercontent.com';
        var YOUR_REDIRECT_URI = 'https://magni.apoio19.com.br/';

        // Parse query string to see if page request is coming from OAuth 2.0 server.
        var fragmentString = location.hash.substring(1);
        var params = {};
        var regex = /([^&=]+)=([^&]*)/g,
            m;
        while (m = regex.exec(fragmentString)) {
            params[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
        }
        if (Object.keys(params).length > 0 && params['state']) {
            if (params['state'] == localStorage.getItem('state')) {
                localStorage.setItem('oauth2-test-params', JSON.stringify(params));

                trySampleRequest();
            } else {
                console.log('State mismatch. Possible CSRF attack');
            }
        }

        // Function to generate a random state value
        function generateCryptoRandomState() {
            const randomValues = new Uint32Array(2);
            window.crypto.getRandomValues(randomValues);

            // Encode as UTF-8
            const utf8Encoder = new TextEncoder();
            const utf8Array = utf8Encoder.encode(
                String.fromCharCode.apply(null, randomValues)
            );

            // Base64 encode the UTF-8 data
            return btoa(String.fromCharCode.apply(null, utf8Array))
                .replace(/\+/g, '-')
                .replace(/\//g, '_')
                .replace(/=+$/, '');
        }

        // If there's an access token, try an API request.
        // Otherwise, start OAuth 2.0 flow.
        function trySampleRequest() {
            var params = JSON.parse(localStorage.getItem('oauth2-test-params'));
            if (params && params['access_token']) {
                // User authorized the request. Now, check which scopes were granted.
                if (params['scope'].includes('https://www.googleapis.com/auth/drive.metadata.readonly')) {
                    // User authorized read-only Drive activity permission.
                    // Calling the APIs, etc.
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET',
                        'https://www.googleapis.com/drive/v3/about?fields=user&' +
                        'access_token=' + params['access_token']);
                    xhr.onreadystatechange = function(e) {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            console.log(xhr.response);
                        } else if (xhr.readyState === 4 && xhr.status === 401) {
                            // Token invalid, so prompt for user permission.
                            oauth2SignIn();
                        }
                    };
                    xhr.send(null);
                } else {
                    // User didn't authorize read-only Drive activity permission.
                    // Update UX and application accordingly
                    console.log('User did not authorize read-only Drive activity permission.');
                }

                // Check if user authorized Calendar read permission.
                if (params['scope'].includes('https://www.googleapis.com/auth/calendar.readonly')) {
                    // User authorized Calendar read permission.
                    // Calling the APIs, etc.
                    console.log('User authorized Calendar read permission.');
                } else {
                    // User didn't authorize Calendar read permission.
                    // Update UX and application accordingly
                    console.log('User did not authorize Calendar read permission.');
                }
            } else {
                oauth2SignIn();
            }
        }

        /*
         * Create form to request access token from Google's OAuth 2.0 server.
         */
        function oauth2SignIn() {
            // create random state value and store in local storage
            var state = generateCryptoRandomState();
            localStorage.setItem('state', state);

            // Google's OAuth 2.0 endpoint for requesting an access token
            var oauth2Endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';

            // Create element to open OAuth 2.0 endpoint in new window.
            var form = document.createElement('form');
            form.setAttribute('method', 'GET'); // Send as a GET request.
            form.setAttribute('action', oauth2Endpoint);

            // Parameters to pass to OAuth 2.0 endpoint.
            var params = {
                'client_id': YOUR_CLIENT_ID,
                'redirect_uri': YOUR_REDIRECT_URI,
                'scope': 'https://www.googleapis.com/auth/drive.metadata.readonly https://www.googleapis.com/auth/calendar.readonly',
                'state': state,
                'include_granted_scopes': 'true',
                'response_type': 'token'
            };

            // Add form parameters as hidden input values.
            for (var p in params) {
                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', p);
                input.setAttribute('value', params[p]);
                form.appendChild(input);
            }

            // Add form to page and submit it to open the OAuth 2.0 endpoint.
            document.body.appendChild(form);
            form.submit();
        }
    </script>


</body>

</html>