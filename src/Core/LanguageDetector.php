<?php

namespace App\Core;

class LanguageDetector
{
    private static $supportedLanguages = ['en' => 'en', 'pt' => 'pt'];
    private static $defaultLanguage = 'pt';

    /**
     * Detecta o idioma com base na URL e redireciona conforme o estado de login do usuário.
     */
    public static function detectLanguage(): array
    {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $segments = explode('/', $uri);

        $languageCode = $segments[0] ?? self::$defaultLanguage;

        if (array_key_exists($languageCode, self::$supportedLanguages)) {
            // Retorna o idioma detectado e a basePath
            return [
                'language' => $languageCode,
                'basePath' => '/' . $languageCode,
            ];
        }else{
            return [
                'language' => self::$defaultLanguage,
                'basePath' => '/' . self::$defaultLanguage . '/',
            ];
        }
    }

}

