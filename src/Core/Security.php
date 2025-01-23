<?php

namespace App\Core;

use RobThree\Auth\TwoFactorAuth;

class Security
{
    /**
     * Inicia a validação de 2FA para o usuário logado.
     *
     * @param string $userId ID do usuário logado.
     * @param string|null $twoFactorSecret Chave secreta do 2FA ou null se não habilitado.
     */
    public static function startTwoFactorValidation(string $userId, ?string $twoFactorSecret = null): void
    {
        self::initializeSession();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $userId) {
            self::redirectTo('/');
        }

        if ($twoFactorSecret) {
            $_SESSION['two_factor_secret'] = $twoFactorSecret;
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

        if (!isset($_SESSION['two_factor_secret'])) {
            return false;
        }

        $tfa = new TwoFactorAuth();
        $twoFactorSecret = $_SESSION['two_factor_secret'];

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
     * Garante a segurança da sessão do usuário (IP e User-Agent).
     */
    public static function enforceSessionSecurity(): void
    {
        self::initializeSession();

        $currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $publicPages = ['login', 'verify-2fa'];

        if (!isset($_SESSION['user_id'])) {
            if (!in_array($currentPage, $publicPages)) {
                self::redirectTo('/');
            }
            return;
        }

        // Verifica inconsistências na sessão
        if (
            $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] ||
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
        ) {
            session_destroy();
            self::redirectTo('/');
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
