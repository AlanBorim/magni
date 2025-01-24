<?php

namespace App\Modules\Login;

use App\Core\FlashMessages;
use App\Core\LanguageDetector;
use App\Core\TokenGenerator;
use App\Core\MailService;
use App\Modules\Login\LoginService;

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
        include __DIR__ . '/views/forgot-password.php';
    }

    public function showRegister()
    {
        include __DIR__ . '/views/register.php';
    }

    public function showResetPasswordForm()
    {
        $token = $_GET['token'] ?? null;
        
        if (!$token) {
            echo "Token inválido ou ausente.";
            return;
        }
    
        $loginService = new LoginService();
    
        // Verifique se o token é válido e não expirou
        $user = $loginService->findByResetToken($token);
    
        if (!$user) {
            echo "Token inválido ou expirado.";
            return;
        }
    
        // Renderize o formulário de redefinição de senha
        include __DIR__ . '/views/reset-password.php';
    }

    public function processForgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {


            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            if (!$email) {
                $this->message[] = "E-mail inválido!";
                return $this->message;
            }

            $user = new LoginService();
            $userData = $user->findByEmail($email);
            if (!$userData) {
                $this->error['status'] = false;
                $this->error['message'] = "E-mail não encontrado!";
                return $this->error;
            }

            // Gera um token único para redefinição de senha
            $token = TokenGenerator::generate();

            $user->insertResetToken($token, $userData['id']);

            $currentLanguage = LanguageDetector::detectLanguage()['language'];

            // Envia o e-mail com o link de redefinição
            $resetLink = "https://magni.apoio19.com.br/{$currentLanguage}/reset-password?token=$token";
            $subject = "Redefinição de senha";
            $message = "
                <h3>Olá,</h3>
                <p>Você solicitou a redefinição de sua senha.</p>
                <p>Clique no link abaixo para redefinir sua senha:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>Este link expira em 1 hora.</p>
            ";


            if (MailService::send($email, $subject, $message)) {

                FlashMessages::setFlash('forgot_password_success', 'Um link de redefinição foi enviado para o seu e-mail.');
                header("Location: /{$currentLanguage}/forgot-password");
                return;
            } else {

                FlashMessages::setFlash('forgot_password_error', 'Falha ao enviar o e-mail. Tente novamente mais tarde.');
                header("Location: /{$currentLanguage}/forgot-password");
                return;
            }
            return $this->message;
        }
    }

    public function processLogin()
    {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        $loginService = new LoginService();
        $user = $loginService->validateUser($email, $password);

        session_start();
        $language = $_SESSION['language'] ?? 'pt';

        if (!$user) {

            $_SESSION['login_error'] = "Usuário ou senha inválidos.";
            header("Location: /$language/");
            return;
        }
        self::initializeUserSession($user);

        // Verifica se o usuário possui 2FA habilitado
        if ($user['two_factor_enabled']) {


            include __DIR__ . '/views/two_factor.php';
        } else {
            // Redireciona para a dashboard
            header("Location: /{$language}/dashboard");
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

    public function verifyTwoFactor()
    {
        session_start();
        if (empty($_SESSION['2fa_pending']) || !$_SESSION['2fa_pending']) {
            echo "Acesso não autorizado.";
            return;
        }

        $code = $_POST['2fa_code'] ?? '';
        $loginService = new LoginService();

        if ($loginService->validateTwoFactorCode($_SESSION['user_id'], $code)) {
            $_SESSION['2fa_pending'] = false;
            header("Location: /dashboard");
        } else {
            echo "Código 2FA inválido.";
        }
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
}
