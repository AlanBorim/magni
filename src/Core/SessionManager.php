<?php

namespace App\Core;

use App\Core\Helpers\Helper;

class SessionManager
{
    private const COOKIE_LIFETIME = 1800; // 30 minutos
    private const SESSION_NAME = 'MAGNI_SESSION';

    
    /**
     * Define um valor na sessão (salva nos cookies).
     */
    public static function set(string $key, $value): void
    {
        $sessionData = self::getSessionData() ?? [
            'session_id' => bin2hex(random_bytes(32)),
            'user_ip' => Helper::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'session_expire' => time() + self::COOKIE_LIFETIME,
            'data' => []
        ];

        $sessionData['data'][$key] = $value;
        
        self::setSessionCookie($sessionData);
    }

    /**
     * Obtém um valor da sessão.
     */
    public static function get(string $key, $default = null)
    {
        $sessionData = self::getSessionData();
        return $sessionData['data'][$key] ?? $default;
    }

    /**
     * Remove um valor da sessão.
     */
    public static function remove(string $key): void
    {
        $sessionData = self::getSessionData();

        if ($sessionData && isset($sessionData['data'][$key])) {
            unset($sessionData['data'][$key]);
            self::setSessionCookie($sessionData);
        }
    }

    /**
     * Destroi a sessão.
     */
    public static function destroySession(): void
    {
        setcookie(self::SESSION_NAME, "", time() - 3600, "/", "", true, true);
    }

    /**
     * Verifica se a sessão está ativa e válida.
     */
    public static function isSessionValid(): bool
    {
        $sessionData = self::getSessionData();

        if (!$sessionData) {
            return false;
        }

        // Proteção contra hijacking
        if ($sessionData['user_ip'] !== Helper::getClientIP() || $sessionData['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            return false;
        }

        return time() <= $sessionData['session_expire'];
    }

    /**
     * Obtém os dados da sessão a partir do cookie.
     */
    private static function getSessionData(): ?array
    {
        if (!isset($_COOKIE[self::SESSION_NAME])) {
            return null;
        }

        $sessionData = json_decode($_COOKIE[self::SESSION_NAME], true);

        return is_array($sessionData) ? $sessionData : null;
    }

    /**
     * Retorna o tempo restante da sessão em segundos.
     */
    public static function getRemainingSessionTime(): int
    {
        $sessionData = self::getSessionData();

        if (!$sessionData || !isset($sessionData['session_expire'])) {
            return 0; // Sessão não existe ou já expirou
        }

        return max(0, $sessionData['session_expire'] - time());
    }

    /**
     * Define o cookie de sessão.
     */
    private static function setSessionCookie(array $sessionData): void
    {
        setcookie(
            self::SESSION_NAME,
            json_encode($sessionData),
            time() + self::COOKIE_LIFETIME,
            "/",
            "",
            true,
            true
        );
    }

    /**
     * Inicializa a sessão do usuário após login bem-sucedido.
     */
    public static function initializeUserSession(array $user): void
    {
        $sessionData = self::getSessionData() ?? [
            'session_id' => bin2hex(random_bytes(32)),
            'user_ip' => Helper::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'session_expire' => time() + self::COOKIE_LIFETIME,
            'data' => []
        ];

        // Define os dados do usuário na sessão via cookies
        $sessionData['data']['user_id'] = $user['id'];
        $sessionData['data']['role'] = $user['role'];
        $sessionData['data']['roleName'] = $user['roleName'];
        $sessionData['data']['roleId'] = $user['roleId'];
        $sessionData['data']['two_factor_enabled'] = $user['two_factor_enabled'];
        $sessionData['data']['is_2fa_verified'] = false;
        $sessionData['data']['user_ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $sessionData['data']['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        // Atualiza o tempo de expiração da sessão
        $sessionData['session_expire'] = time() + self::COOKIE_LIFETIME;

        self::setSessionCookie($sessionData);
    }

    /**
     * Verifica se a sessão expirou.
     */
    public static function isSessionExpired(): bool
    {
        $sessionData = self::getSessionData();
        return !$sessionData || time() > $sessionData['session_expire'];
    }

    /**
     * Renova a sessão do usuário.
     */
    public static function renewSession(): void
    {
        $sessionData = self::getSessionData();

        if ($sessionData) {
            $sessionData['session_expire'] = time() + self::COOKIE_LIFETIME;
            self::setSessionCookie($sessionData);
        }
    }
}
