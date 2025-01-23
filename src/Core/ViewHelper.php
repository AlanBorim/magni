<?php

namespace App\Core;

class ViewHelper
{
    /**
     * Inclui um arquivo se ele for legível.
     *
     * @param string $filePath Caminho do arquivo a ser incluído.
     */
    public static function includeIfReadable(string $filePath)
    {
        if (is_readable($filePath)) {
            include $filePath;
        }
    }
}
