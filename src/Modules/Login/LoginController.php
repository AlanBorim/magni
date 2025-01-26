<?php

namespace App\Modules\Login;

use App\Core\FlashMessages;
use App\Core\LanguageDetector;
use App\Core\TokenGenerator;
use App\Core\MailService;
use App\Core\Security;
use App\Modules\Login\LoginService;

use Exception;

class LoginController
{
    public array $message;
    public array $error;

    public function showLogin()
    {
        session_start();
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']); // Limpa a mensagem após exibir
        include __DIR__ . '/views/login.php';
    }

    public function showDashboard()
    {
        session_start();
        if (empty($_SESSION['user_id'])) {
            header("Location: /");
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

    public function showResetPasswordForm()
    {

        $token = $_REQUEST['token'] ?? null;

        if (!$token) {
            FlashMessages::setFlash('danger', 'forgot_password_token_not_present', 'Token inválido ou ausente.');
        }

        $loginService = new LoginService();

        // Verifique se o token é válido e não expirou
        $user = $loginService->findByResetToken($token);

        if (!$user) {
            FlashMessages::setFlash('danger', 'forgot_password_token_expired', 'Token inválido ou ausente.');
        }

        // Renderize o formulário de redefinição de senha
        include __DIR__ . '/views/resetPassword.php';
    }

    public function show2fa()
    {
        include __DIR__ . '/views/twoFactor.php';
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
            FlashMessages::setFlash('danger', 'forgot_password_invalid_token', 'Token não fornecido!');
            header("Location: /{$currentLanguage}/");
            return;
        }

        if (!$resetData->findByResetToken($token)) {
            FlashMessages::setFlash('danger', 'forgot_password_invalid_token', 'Token inválido ou expirado.');
            header("Location: /{$currentLanguage}/");
            return;
        }

        if (empty($password)) {
            FlashMessages::setFlash('danger', 'general', 'A senha está vazia.');
            // Redireciona para a página de redefinição com o token intacto
            header("Location: /{$currentLanguage}/reset-password?token=$token");
            return;
        }

        if (strlen($password) < 8) {
            FlashMessages::setFlash('danger', 'general', 'A senha deve ter pelo menos 8 caracteres.');
            // Redireciona para a página de redefinição com o token intacto
            header("Location: /{$currentLanguage}/reset-password?token=$token");
            return;
        }

        if ($password !== $confirmPassword) {
            FlashMessages::setFlash('danger', 'general', 'As senhas não coincidem.');
            // Redireciona para a página de redefinição com o token intacto
            header("Location: /{$currentLanguage}/reset-password?token=$token");
            return;
        }

        try {
            // Tenta redefinir a senha usando o serviço
            $resetData->resetPassword($token, $password);

            FlashMessages::setFlash('success', 'change_successfull', 'Senha redefinida com sucesso. Você já pode fazer login.');
            header("Location: /{$currentLanguage}/");
            return;
        } catch (Exception $e) {
            FlashMessages::setFlash('danger', 'change_error', 'Ocorreu um erro ao redefinir a senha: ' . $e->getMessage());
            header("Location: /{$currentLanguage}/reset-password?token=$token");
            return;
        }
    }

    public function processForgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentLanguage = LanguageDetector::detectLanguage()['language'];

            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if (!$email) {
                FlashMessages::setFlash('danger', 'forgot_password_invalid_mail', 'E-mail inválido!');
                header("Location: /{$currentLanguage}/forgot-password");
                return;
            }

            $user = new LoginService();
            $userData = $user->findByEmail($email);
            if (!$userData) {
                FlashMessages::setFlash('danger', 'forgot_password_mail_not_found', 'E-mail não encontrado');
                header("Location: /{$currentLanguage}/forgot-password");
                return;
            }

            // Gera um token único para redefinição de senha
            $token = TokenGenerator::generate();

            if (!$user->insertResetToken($token, $userData['id'])) {
                FlashMessages::setFlash('danger', 'forgot_password_token_not_inserted', 'Impossível criar token de redefinição de senha!');
                header("Location: /{$currentLanguage}/forgot-password");
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

                FlashMessages::setFlash('success', 'forgot_password_success', 'Um link de redefinição foi enviado para o seu e-mail.');
                header("Location: /{$currentLanguage}/forgot-password");
                return;
            } else {

                FlashMessages::setFlash('danger', 'forgot_password_error', 'Falha ao enviar o e-mail. Tente novamente mais tarde.');
                header("Location: /{$currentLanguage}/forgot-password");
                return;
            }
        }
    }

    public function processLogin()
    {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        $loginService = new LoginService();
        $user = $loginService->validateUser($email, $password);

        if (!$user) {

            FlashMessages::setFlash('danger', 'access_error', 'Usuário ou senha inválidos.');
            header("Location: /{$currentLanguage}/");
            return;
        }

        self::initializeUserSession($user);

        if ($user['activated'] == '0') {
            FlashMessages::setFlash('danger', 'not_activated', 'Conta não ativada. Verifique seu e-mail ou clique no botão para reenviar o link de ativação.');
            header("Location: /{$currentLanguage}/");
            return;
        }

        // Verifica se o usuário possui 2FA habilitado
        if ($user['two_factor_enabled']) {
            include __DIR__ . '/views/twoFactor.php';
        } else {
            // Redireciona para a dashboard
            header("Location: /{$currentLanguage}/dashboard");
        }
    }

    public function process2fa()
    {
        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        session_start();

        $code = $_REQUEST['two_factor_code'] ?? '';

        if (empty($code)) {
            FlashMessages::setFlash('danger', 'empty_2fa', 'Código 2FA vazio.');
            header("Location: /{$currentLanguage}/");
            return;
        }

        Security::startTwoFactorValidation($_SESSION['user_id'], $code);

        if (!Security::validateTwoFactorCode($code)) {
            FlashMessages::setFlash('danger', 'invalid_2fa', 'Código 2FA inválido.');
            header("Location: /{$currentLanguage}/two-factor-check");
            return;
        }

        $_SESSION['2fa_pending'] = false;
        header("Location: /{$currentLanguage}/dashboard");
    }

    private function initializeUserSession(array $user)
    {
        session_start();
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['roleName'] = $user['roleName'];
        $_SESSION['roleId'] = $user['roleId'];
        $_SESSION['two_factor_enabled'] = $user['two_factor_enabled'];
        $_SESSION['is_2fa_verified'] = false;
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
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
            $activationLink = $_ENV['APP_URL'] . "/activate.php?token=" . $user['activation_token'];
            $message = "
                <p>Olá, {$user['name']}!</p>
                <p>Por favor, clique no link abaixo para ativar sua conta:</p>
                <p><a href='{$activationLink}'>{$activationLink}</a></p>
                <p>Se você não solicitou este e-mail, por favor ignore-o.</p>
            ";

            if (MailService::send($user['email'], 'Reenvio de Ativação da Conta', $message)) {
                FlashMessages::setFlash('success', 'resent_activation_email', 'E-mail de ativação reenviado. Verifique sua caixa de entrada.');
                header("Location: /{$currentLanguage}/");
                return;
            } else {
                FlashMessages::setFlash('danger', 'resent_activation_email_failure', 'Erro ao reenviar o e-mail de ativação. Tente novamente mais tarde.');
                header("Location: /{$currentLanguage}/");
                return;
            }
        } else {
            FlashMessages::setFlash('danger', 'resent_activation_email_not_found', 'Falha ao localizar o token entre em contato com o suporte.');
            header("Location: /{$currentLanguage}/");
            return;

        }
    }
}
