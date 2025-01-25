<?php

namespace App\Core;

class FlashMessages
{
    public static function setFlash(string $type, string $message): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_messages'][$type][] = $message;
    }

    public static function getFlash(): array
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
}
