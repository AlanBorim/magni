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

    public static function handleAccess(bool $isLoggedIn = false): void
    {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $segments = explode('/', $uri);

        $languageCode = $segments[0] ?? self::$defaultLanguage;

        if (array_key_exists($languageCode, self::$supportedLanguages)) {
            // Retorna o idioma detectado e a basePath
            $language = [
                'language' => $languageCode,
                'basePath' => '/' . $languageCode,
            ];
        }else{
            $language = [
                'language' => self::$defaultLanguage,
                'basePath' => '/' . self::$defaultLanguage . '/',
            ];
        }

        // Se o idioma não for suportado, redirecionar para o padrão
        self::manageAccess($language, $isLoggedIn);
    }

    /**
     * Redireciona para o idioma padrão ou dashboard com base no estado de login.
     */
    private static function manageAccess(array $uri, bool $isLoggedIn = false): void
    {
        echo $uri['basePath'];
        exit;

        if ($isLoggedIn) {
            // Usuário logado: redireciona para o dashboard no idioma padrão
            header("Location: " . $uri['basePath'] . "dashboard");
        } else {
            // Usuário não logado: redireciona para o idioma padrão
            header("Location: " . $uri['basePath']);
        }

        exit;
    }
}

