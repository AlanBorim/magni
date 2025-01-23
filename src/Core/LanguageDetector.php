<?php

namespace App\Core;

class LanguageDetector
{
    private static $supportedLanguages = ['en' => 'en', 'pt' => 'pt'];
    private static $defaultLanguage = 'pt';

    /**
     * Detecta o idioma com base na URL e redireciona se necessário.
     */
    public static function detectLanguage(): array
    {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $segments = explode('/', $uri);

        $languageCode = $segments[0] ?? null;

        if (in_array($languageCode, ['pt', 'en'])) {
            return [
                'language' => $languageCode,
                'basePath' => '/' . $languageCode,
            ];
        }

        // Redirecionar para o idioma padrão
        self::redirectToDefaultLanguage($uri);
    }

    private static function redirectToDefaultLanguage($uri): void
    {
        $cleanUri = preg_replace('/^\/(pt|en)/', '', $uri); // Remove idioma duplicado
        header("Location: /pt$cleanUri");
        exit;
    }
}
