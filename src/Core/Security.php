<?php

namespace App\Core;

use App\Modules\Login\LoginService;
use RobThree\Auth\TwoFactorAuth;
use App\Core\SessionManager;

class Security
{
    /**
     * Inicia a validação de 2FA para o usuário logado.
     *
     * @param string $userId ID do usuário logado.
     * @param string|null $twoFactorCode Código enviado pelo usuário.
     */
    public static function startTwoFactorValidation(string $userId, ?string $twoFactorCode = null): void
    {
        self::initializeSession();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId) {
            FlashMessages::setFlash('danger', 'start_2fa_error', 'Ocorreu um erro no start 2fa');
            self::redirectTo('/');
        }

        if ($twoFactorCode) {
            $_SESSION['two_factor_code'] = $twoFactorCode;
            $_SESSION['two_factor_start_time'] = time(); // Marca o início da validação
        } else {
            $_SESSION['two_factor_validated'] = true; // Permite acesso direto se 2FA não estiver habilitado
        }
    }

    /**
     * Verifica o código de 2FA fornecido pelo usuário.
     *
     * @param string $userInputCode Código digitado pelo usuário.
     * @return bool Retorna true se a validação for bem-sucedida.
     */
    public static function validateTwoFactorCode(string $userInputCode): bool
    {
        self::initializeSession();

        $tfa = new TwoFactorAuth();
        $user = new LoginService();
        $userData = $user->findById($_SESSION['user_id']);

        $twoFactorSecret = $userData['two_factor_secret'];

        if ($tfa->verifyCode($twoFactorSecret, $userInputCode)) {
            $_SESSION['two_factor_validated'] = true;
            unset($_SESSION['two_factor_secret'], $_SESSION['two_factor_start_time']);
            return true;
        }

        return false;
    }

    /**
     * Garante que o usuário tenha validado o 2FA antes de acessar uma página sensível.
     */
    public static function enforceTwoFactorValidation(): void
    {
        self::initializeSession();

        if (empty($_SESSION['two_factor_validated'])) {
            self::redirectTo('/verify-2fa');
        }

        // Verifica o tempo limite do 2FA
        if (isset($_SESSION['two_factor_start_time']) && time() - $_SESSION['two_factor_start_time'] > 300) { // 5 minutos
            session_destroy();
            self::redirectTo('/');
        }
    }

    /**
     * Garante a segurança da sessão do usuário (IP, User-Agent) e controla tempo de inatividade.
     */
    public static function enforceSessionSecurity(): void
    {
        self::initializeSession();

        // Define páginas públicas onde a segurança da sessão NÃO será aplicada
        $publicPages = ['login', 'verify-2fa', 'forgot-password', 'register'];

        // Obtém a página atual sem parâmetros da URL
        $currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        // Se o usuário NÃO estiver logado
        if (!isset($_SESSION['user_id'])) {
            if (!in_array($currentPage, $publicPages)) {
                self::redirectTo('/');
            }
            return;
        }

        // **Iniciar a verificação de tempo de sessão APENAS na dashboard**
        if ($currentPage === 'dashboard') {
            if (SessionManager::isSessionExpired()) {
                SessionManager::destroySession();
                FlashMessages::setFlash('error', 'session_expired', 'Sua sessão expirou. Faça login novamente.');
                self::redirectTo('/');
            } else {
                SessionManager::refreshSession(); // Renova o tempo de inatividade
            }
        }
    }


    /**
     * Define a segurança da sessão no momento do login.
     */
    public static function initializeSessionSecurity(): void
    {
        self::initializeSession();

        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Inicializa a sessão se ainda não estiver ativa.
     */
    private static function initializeSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Redireciona o usuário para uma URL específica.
     *
     * @param string $url URL de destino.
     */
    private static function redirectTo(string $url): void
    {
        header("Location: $url");
        exit;
    }
}
