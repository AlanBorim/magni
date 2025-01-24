<?php
namespace App\Core;

class TokenGenerator
{
    /**
     * Gera um token seguro para recuperação de senha ou outras finalidades.
     *
     * @param int $length Comprimento do token (padrão: 32).
     * @return string Token gerado.
     */
    public static function generate(int $length = 32): string
    {
        // Gera bytes aleatórios e os converte em um hash hexadecimal seguro.
        return bin2hex(random_bytes($length / 2));
    }
}
