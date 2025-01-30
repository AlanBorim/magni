<?php

namespace App\Core;

class SessionManager
{
    // Tempo de expiração da sessão em segundos (exemplo: 30 minutos)
    private const SESSION_EXPIRE_TIME = 1800; // 1800 segundos = 30 minutos

    // Renova o tempo de expiração da sessão
    public static function renewSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true); // Regera o ID da sessão para evitar fixation attacks
            $_SESSION['session_expire'] = time() + self::SESSION_EXPIRE_TIME;
        }
    }

    // Inicializa a sessão e define o tempo de expiração
    private static function initializeSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
            session_regenerate_id(true); // Regera o ID da sessão para evitar fixation attacks
        }

        // Define o tempo de expiração da sessão
        if (!isset($_SESSION['session_expire'])) {
            $_SESSION['session_expire'] = time() + self::SESSION_EXPIRE_TIME;
        }
    }

    // Verifica se a sessão expirou
    public static function isSessionExpired(): bool
    {
        self::initializeSession();

        // Verifica se o tempo atual é maior que o tempo de expiração
        return time() > $_SESSION['session_expire'];
    }

    // Retorna o tempo restante da sessão em segundos
    public static function getRemainingSessionTime(): int
    {
        self::initializeSession();

        // Calcula o tempo restante
        return max(0, $_SESSION['session_expire'] - time());
    }

    // Inicializa a sessão do usuário com os dados fornecidos
    public static function initializeUserSession(array $user): void
    {
        self::initializeSession(); // Garante que a sessão está ativa e com tempo de expiração definido

        // Define os dados do usuário na sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['roleName'] = $user['roleName'];
        $_SESSION['roleId'] = $user['roleId'];
        $_SESSION['two_factor_enabled'] = $user['two_factor_enabled'];
        $_SESSION['is_2fa_verified'] = false;
        $_SESSION['user_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }

    // Destrói a sessão (logout)
    public static function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset(); // Remove todas as variáveis de sessão
            session_destroy(); // Destrói a sessão
        }
    }
}
