<?php

namespace App\Modules\Login;

use App\Core\LanguageDetector;
use App\Core\TokenGenerator;
use App\Core\MailService;
use App\Core\Security;
use App\Core\SessionManager;
use App\Modules\Login\LoginService;
use App\Core\MessageHandler;

use Exception;
use RobThree\Auth\TwoFactorAuth;
use RuntimeException;

class LoginController
{
    public array $message;
    public array $error;

    public function showLogin()
    {
        include __DIR__ . '/views/login.php';
    }

    public function showDashboard()
    {
        // Verifica se o usuário está autenticado
        if (!SessionManager::get('user_id')) {
            MessageHandler::redirectWithMessage('danger', 'not_connected', 'Usuario não conectado.', '/');
            return;
        }
        
        include __DIR__ . '/views/dashboard.php';
    }

    public function showForgotPassword()
    {
        include __DIR__ . '/views/forgotPassword.php';
    }

    public function showRegister()
    {
        include __DIR__ . '/views/register.php';
    }

    public function showProfile()
    {
        include __DIR__ . '/views/profile.php';
    }

    public function showResetPasswordForm()
    {

        $token = $_REQUEST['token'] ?? null;

        if (!$token) {
            MessageHandler::redirectWithMessage('danger', 'forgot_password_token_not_present', 'Token ausente.', '/login');
            return;
        }

        $loginService = new LoginService();

        // Verifique se o token é válido e não expirou
        $user = $loginService->findByResetToken($token);

        if (!$user) {
            MessageHandler::redirectWithMessage('danger', 'forgot_password_token_expired', 'Token inválido.', '/login');
            return;
        }

        // Renderize o formulário de redefinição de senha
        include __DIR__ . '/views/resetPassword.php';
    }

    public function show2fa()
    {
        include __DIR__ . '/views/twoFactor.php';
    }

    public function showEnable2fa()
    {
        include __DIR__ . '/views/enableTwoFactor.php';
    }

    public function processResetPassword()
    {

        $currentLanguage = LanguageDetector::detectLanguage()['language'];
        $resetData = new LoginService();
        $errors = [];

        $token = $_REQUEST['token'] ?? null;
        $password = $_REQUEST['password'] ?? null;
        $confirmPassword = $_REQUEST['confirm_password'] ?? null;

        if (!$token) {
            MessageHandler::redirectWithMessage('danger', 'forgot_password_invalid_token', 'Token não fornecido!', "/{$currentLanguage}/");
            return;
        }

        if (!$resetData->findByResetToken($token)) {
            MessageHandler::redirectWithMessage('danger', 'forgot_password_invalid_token', 'Token inválido ou expirado.', "/{$currentLanguage}/");
            return;
        }

        if (empty($password)) {
            MessageHandler::redirectWithMessage('danger', 'general', 'A senha está vazia.', "/{$currentLanguage}/reset-password?token={$token}");
            return;
        }

        if (strlen($password) < 8) {
            MessageHandler::redirectWithMessage('danger', 'general', 'A senha deve ter pelo menos 8 caracteres.', "/{$currentLanguage}/reset-password?token={$token}");
            return;
        }

        if ($password !== $confirmPassword) {
            MessageHandler::redirectWithMessage('danger', 'general', 'As senhas não coincidem.', "/{$currentLanguage}/reset-password?token={$token}");
            return;
        }

        try {
            // Tenta redefinir a senha usando o serviço
            $resetData->resetPassword($token, $password);
        } catch (Exception $e) {
            MessageHandler::redirectWithMessage('danger', 'change_error', 'Ocorreu um erro ao redefinir a senha: ' . $e->getMessage(), "/{$currentLanguage}/reset-password?token={$token}");
            return;
        }
    }

    public function processForgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentLanguage = LanguageDetector::detectLanguage()['language'];

            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if (!$email) {
                MessageHandler::redirectWithMessage('danger', 'forgot_password_invalid_mail', 'E-mail inválido!', "/{$currentLanguage}/forgot-password");
                return;
            }

            $user = new LoginService();
            $userData = $user->findByEmail($email);
            if (!$userData) {
                MessageHandler::redirectWithMessage('danger', 'forgot_password_mail_not_found', 'E-mail não encontrado', "/{$currentLanguage}/forgot-password");
                return;
            }

            // Gera um token único para redefinição de senha
            $token = TokenGenerator::generate();

            if (!$user->insertResetToken($token, $userData['id'])) {
                MessageHandler::redirectWithMessage('danger', 'forgot_password_token_not_inserted', 'Impossível criar token de redefinição de senha!', "/{$currentLanguage}/forgot-password");
                return;
            }

            // Envia o e-mail com o link de redefinição
            $resetLink = $_ENV['APP_URL'] . "/{$currentLanguage}/reset-password?token=$token";
            $subject = "Redefinição de senha";
            $message = "
                <h3>Olá,</h3>
                <p>Você solicitou a redefinição de sua senha.</p>
                <p>Clique no link abaixo para redefinir sua senha:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>Este link expira em 1 hora.</p>
            ";


            if (MailService::send($email, $subject, $message)) {
                MessageHandler::redirectWithMessage('success', 'forgot_password_success', 'Um link de redefinição foi enviado para o seu e-mail.', "/{$currentLanguage}/forgot-password");
                return;
            } else {
                MessageHandler::redirectWithMessage('danger', 'forgot_password_error', 'Falha ao enviar o e-mail. Tente novamente mais tarde.', "/{$currentLanguage}/forgot-password");
                return;
            }
        }
    }

    public function processLogin(): void
    {
        // Sanitiza os dados de entrada
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        // Valida o usuário e senha
        $loginService = new LoginService();
        $user = $loginService->validateUser($email, $password);

        if (!$user) {
            MessageHandler::redirectWithMessage('danger', 'access_error', 'Usuário ou senha inválidos.', "/{$currentLanguage}/");
            return;
        }

        // Verifica se a conta está ativada antes de criar a sessão
        if ($user['activated'] == '0') {
            MessageHandler::redirectWithMessage('danger', 'not_activated', 'Conta não ativada. Verifique seu e-mail ou reenvie o link de ativação.', "/{$currentLanguage}/");
            return;
        }

        try {
            // Define os dados da sessão usando SessionManager
            SessionManager::initializeUserSession($user);

            // Valida se a sessão foi realmente criada
            if (!SessionManager::get('user_id')) {
                throw new RuntimeException("Falha ao definir a sessão do usuário.");
            }

            // Atualiza o último login do usuário
            LoginService::updateLastLogin($user['id']);

            // Teste para verificar se os cookies armazenaram os dados corretamente
            error_log("Sessão Criada: " . json_encode($_COOKIE));
        } catch (RuntimeException $e) {
            MessageHandler::redirectWithMessage('danger', 'session_error', 'Erro ao iniciar a sessão. ' . $e->getMessage(), "/{$currentLanguage}/");
            return;
        }

        // Controle de 2FA
        if ($user['two_factor_enabled']) {
            header("Location: /{$currentLanguage}/two-factor");
        } else {
            header("Location: /{$currentLanguage}/dashboard");
        }

        exit; // Sempre interrompe após redirecionamento
    }


    public function process2fa()
    {
        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        #Security::enforceSessionSecurity();

        $code = $_REQUEST['two_factor_code'] ?? '';

        if (empty($code)) {
            MessageHandler::redirectWithMessage('danger', 'empty_2fa', 'Código 2FA vazio.', "/{$currentLanguage}/");
            return;
        }

        // Security::startTwoFactorValidation($_SESSION['user_id'], $code);

        // if (Security::validateTwoFactorCode($code)) {
        //     $_SESSION['2fa_pending'] = false;
        //     header("Location: /{$currentLanguage}/dashboard");
        // } else {
        //     MessageHandler::redirectWithMessage('danger', 'invalid_2fa', 'Código 2FA inválido.', "/{$currentLanguage}/two-factor");
        //     return;
        // }
    }

    public function processRegister()
    {
        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userData = [];
            $userData['name'] = trim($_POST['name']);
            $userData['email'] = trim($_POST['email']);
            $userData['password'] = $_POST['password'];
            $userData['confirmPassword'] = $_POST['confirm_password'];

            // Validações de nome
            if (empty($userData['name'])) {
                MessageHandler::redirectWithMessage('danger', 'required_name', 'O nome é obrigatório.', "/{$currentLanguage}/register");
                return;
            }

            if (empty($userData['name']) || !preg_match("/^[\p{L}\s]+$/u", $userData['name'])) {
                MessageHandler::redirectWithMessage('danger', 'invalid_name', 'Nome inválido. Apenas letras e espaços são permitidos.', "/{$currentLanguage}/register");
                return;
            }

            // Validação de e-mail
            if (empty($userData['email'])) {
                MessageHandler::redirectWithMessage('danger', 'required_name', 'O e-mail é obrigatório.', "/{$currentLanguage}/register");
                return;
            } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                MessageHandler::redirectWithMessage('danger', 'invalid_email', 'E-mail inválido.', "/{$currentLanguage}/register");
                return;
            }

            // Validação de senha
            if (empty($userData['password'])) {
                MessageHandler::redirectWithMessage('danger', 'password_required', 'Senha é obrigatória', "/{$currentLanguage}/register");
                return;
            } elseif ($userData['password'] !== $userData['confirmPassword']) {
                MessageHandler::redirectWithMessage('danger', 'password_not_match', 'As senhas não coincidem.', "/{$currentLanguage}/register");
                return;
            }


            // Cria o serviço de usuário e registra o usuário
            try {

                $loginService = new LoginService();

                if ($loginService->findByEmail($userData['email'])) {
                    MessageHandler::redirectWithMessage('danger', 'email_exist', 'E-mail já está registrado.', "/{$currentLanguage}/register");
                    return;
                } else {

                    $userData['activationToken'] = bin2hex(random_bytes(32)); // Gerar token de ativação

                    if ($loginService->addUserWithRoles($userData)) {
                        // Envia o e-mail de ativação
                        $activationLink = $_ENV['APP_URL'] . "/" . $currentLanguage . "/activateLogin?token=" . $userData['activationToken'];
                        $emailSent = MailService::send(
                            $userData['email'],
                            "Ativação da sua conta",
                            "<p>Olá {$userData['name']},</p>
                                <p>Obrigado por se registrar. Por favor, ative sua conta clicando no link abaixo:</p>
                                <p><a href='$activationLink'>Ativar Conta</a></p>
                                <p>Se você não se cadastrou, ignore este e-mail.</p>"
                        );

                        if (!$emailSent) {
                            MessageHandler::redirectWithMessage('danger', 'email_activation_error', 'Erro ao enviar o e-mail de ativação.', "/{$currentLanguage}/register");
                            return;
                        }
                    }
                    MessageHandler::redirectWithMessage('success', 'register_success', 'Conta criada com sucesso. Verifique seu e-mail para ativar sua conta.', "/{$currentLanguage}/");
                    return;
                }
            } catch (Exception $e) {
                MessageHandler::redirectWithMessage('danger', 'register_error', 'Erro ao criar a conta: ' . $e->getMessage(), "/{$currentLanguage}/");
                return;
            }
        }
    }

    public function processEnable2fa()
    {
        $currentLanguage = LanguageDetector::detectLanguage()['language'];
        #Security::enforceSessionSecurity();

        $userInputCode = $_REQUEST['two_factor_code'];

        // Carregar a biblioteca RobThree

        $tfa = new TwoFactorAuth(new \RobThree\Auth\Providers\Qr\QRServerProvider());

        // Validar o código 2FA fornecido
        if ($tfa->verifyCode($_REQUEST['secret'], $userInputCode)) {
            $loginService = new LoginService();
            if ($loginService->updateEnable2fa($_SESSION['user_id'])) {

                $_SESSION['two_factor_enabled'] = 1;
                MessageHandler::redirectWithMessage('success', 'secret_ok', '2FA habilitado com sucesso.', "/{$currentLanguage}/enable2fa");
                return;
            }
        } else {
            MessageHandler::redirectWithMessage('danger', 'invalid_secret', 'Código inválido. Tente novamente.', "/{$currentLanguage}/enable2fa");
            return;
        }
    }

    public function logout()
    {
        session_start();
        $language = $_SESSION['language'] ?? 'pt';
        // Destrói todos os dados da sessão
        $_SESSION = [];
        session_destroy();

        // Redireciona o usuário para a página de login
        header("Location: /{$language}/");
        exit;
    }
    /**
     * Undocumented function
     *
     * @param [type] $email
     * @return void
     */
    public function resendActivationEmail()
    {
        $id = $_REQUEST['id'];

        $currentLanguage = LanguageDetector::detectLanguage()['language'];
        $loginService = new LoginService();
        $user = $loginService->findById($id);

        if (isset($user['activation_token']) && $user['activation_token'] != '') {
            // Reenvia o e-mail de ativação
            $activationLink = $_ENV['APP_URL'] . "/" . $currentLanguage . "/activateLogin?token=" . $user['activation_token'];
            $message = "
                <p>Olá, {$user['name']}!</p>
                <p>Por favor, clique no link abaixo para ativar sua conta:</p>
                <p><a href='{$activationLink}'>{$activationLink}</a></p>
                <p>Se você não solicitou este e-mail, por favor ignore-o.</p>
            ";

            if (MailService::send($user['email'], 'Reenvio de Ativação da Conta', $message)) {

                MessageHandler::redirectWithMessage('success', 'resent_activation_email', 'E-mail de ativação reenviado. Verifique sua caixa de entrada.', "/{$currentLanguage}/");
                return;
            } else {
                MessageHandler::redirectWithMessage('danger', 'resent_activation_email_failure', 'Erro ao reenviar o e-mail de ativação. Tente novamente mais tarde.', "/{$currentLanguage}/");
                return;
            }
        } else {
            MessageHandler::redirectWithMessage('danger', 'resent_activation_email_not_found', 'Falha ao localizar o token entre em contato com o suporte.', "/{$currentLanguage}/");
            return;
        }
    }

    public function activateLogin()
    {
        $currentLanguage = LanguageDetector::detectLanguage()['language'];
        if (isset($_REQUEST['token'])) {

            $loginService = new LoginService();

            if ($loginService->findByToken($_GET['token'])) {
                MessageHandler::redirectWithMessage('success', 'activation_token_success', 'Conta ativada com sucesso.', "/{$currentLanguage}/");
                return;
            } else {
                MessageHandler::redirectWithMessage('danger', 'activation_token_error', 'Erro ao ativar a conta.', "/{$currentLanguage}/");
                return;
            }
        } else {
            MessageHandler::redirectWithMessage('danger', 'activation_token_not_found', 'Token não fornecido.', "/{$currentLanguage}/");
            return;
        }
    }

    public function getQrCode2fa()
    {

        #Security::enforceSessionSecurity();

        $tfa = new TwoFactorAuth();
        // Gerar chave secreta única para o usuário
        $secret = $tfa->createSecret();

        if (LoginService::updateSecret($_SESSION['user_id'], $secret)) {
            $retorno = [];
            $retorno['qrCode'] = $tfa->getQRCodeImageAsDataUri('Magni', $secret);
            $retorno['secret'] = $secret;
            return $retorno;
        }

        return false;
    }
}
