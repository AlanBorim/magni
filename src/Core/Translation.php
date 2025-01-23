<?php

namespace App\Core;

class Translation
{
    /**
     * Inicializa o sistema de tradução com base no idioma fornecido.
     *
     * @param string $locale Código do idioma (ex.: 'pt', 'en').
     */
    public static function init(string $locale): void
    {
        $domain = 'messages';
        $localesDir = realpath(__DIR__ . '/../../locale'); // Diretório de traduções

        if (!$localesDir || !is_dir($localesDir)) {
            error_log("Erro: Diretório de traduções '{$localesDir}' não encontrado.");
            return;
        }

        // Mapear locale para os valores válidos
        $localeMap = [
            'pt' => 'pt_BR.UTF-8',
            'en' => 'en_US.UTF-8',
        ];
        $locale = $localeMap[$locale] ?? 'en_US.UTF-8';

        // Verificar se o arquivo .mo existe
        $moFile = "{$localesDir}/{$locale}/LC_MESSAGES/{$domain}.mo";
        if (!file_exists($moFile)) {
            error_log("Erro: O arquivo de tradução '{$moFile}' não foi encontrado.");
            return;
        }

        // Configurar locale e carregar os arquivos MO
        putenv("LC_ALL={$locale}");
        setlocale(LC_ALL, $locale);

        // Apontar para a pasta de traduções e configurar codificação
        bindtextdomain($domain, $localesDir);
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    }
}
