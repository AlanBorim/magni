<?php

namespace App\Core;

class App
{
    private static $language = 'pt';

    public static function setLanguage(string $language): void
    {
        self::$language = $language;
        
        setcookie('language', $language, time() + (3600 * 24 * 30), '/'); // Armazena no cookie
    }

    public static function getLanguage(): string
    {
        return self::$language;
    }
}
