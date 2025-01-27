<?php

namespace App\Core;

class FlashMessages
{
    public array $messages = [];
    
    public static function setFlash(string $type, string $var, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Certifique-se de que a sessão está iniciada
        }
        $_SESSION['flash_messages'][$type][$var][] = $message;
    }

    public static function getFlash(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Certifique-se de que a sessão está iniciada
        }
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
}
