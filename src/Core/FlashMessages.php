<?php

namespace App\Core;

class FlashMessages
{
    public static function setFlash(string $key, string $message): void
    {
        session_start();
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        session_start();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // Remove a mensagem após recuperá-la
            return $message;
        }
        return null;
    }
}
