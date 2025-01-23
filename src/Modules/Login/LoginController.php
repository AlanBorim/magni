<?php

namespace App\Modules\Login;

class LoginController
{
    public function showLogin()
    {
        session_start();
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']); // Limpa a mensagem após exibir
        include __DIR__ . '/views/login.php';
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

    public function dashboard()
    {
        session_start();
        if (empty($_SESSION['user_id'])) {
            header("Location: /");
            return;
        }

        include __DIR__ . '/views/dashboard.php';
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
}
