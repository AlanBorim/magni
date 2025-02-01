<?php

namespace App\Core;

class SessionManager
{
    // Tempo de expiração da sessão em segundos (30 minutos)
    private const SESSION_EXPIRE_TIME = 1800;

    /**
     * Inicializa a sessão se ainda não estiver ativa.
     */
    private static function initializeSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            session_regenerate_id(true); // Regenera o ID da sessão para evitar fixation attacks
        }

        // Define o tempo de expiração da sessão se ainda não estiver definido
        if (!isset($_SESSION['session_expire'])) {
            $_SESSION['session_expire'] = time() + self::SESSION_EXPIRE_TIME;
        }

        // Define os dados de segurança do usuário
        if (!isset($_SESSION['user_ip'])) {
            $_SESSION['user_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        }
        if (!isset($_SESSION['user_agent'])) {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }
    }

    /**
     * Renova a sessão, atualizando o tempo de expiração e regenerando o ID.
     */
    public static function renewSession(): void
    {
        self::initializeSession();
        session_regenerate_id(true);
        $_SESSION['session_expire'] = time() + self::SESSION_EXPIRE_TIME;
    }

    /**
     * Verifica se a sessão expirou por inatividade.
     *
     * @return bool Retorna true se a sessão expirou.
     */
    public static function isSessionExpired(): bool
    {
        self::initializeSession();

        return time() > $_SESSION['session_expire'];
    }

    /**
     * Atualiza o tempo de atividade da sessão.
     * Apenas será chamado quando o usuário estiver na dashboard.
     */
    public static function refreshSession(): void
    {
        self::initializeSession();
        $_SESSION['session_expire'] = time() + self::SESSION_EXPIRE_TIME;
    }

    /**
     * Retorna o tempo restante da sessão em segundos.
     *
     * @return int Tempo restante até a expiração.
     */
    public static function getRemainingSessionTime(): int
    {
        self::initializeSession();
        return max(0, $_SESSION['session_expire'] - time());
    }

    /**
     * Inicializa a sessão do usuário após login bem-sucedido.
     */
    public static function initializeUserSession(array $user): void
    {
        self::initializeSession();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['roleName'] = $user['roleName'];
        $_SESSION['roleId'] = $user['roleId'];
        $_SESSION['two_factor_enabled'] = $user['two_factor_enabled'];
        $_SESSION['is_2fa_verified'] = false;
        $_SESSION['user_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Destrói a sessão e remove todas as variáveis.
     */
    public static function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }
}
