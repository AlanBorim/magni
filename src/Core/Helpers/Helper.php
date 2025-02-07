<?php

namespace App\Core\Helpers;

class Helper
{
    /**
     * Obtém o IP real do usuário considerando proxies e VPNs.
     */
    public static function getClientIP(): string
    {
        $keys = [
            'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'
        ];

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ipList = explode(',', $_SERVER[$key]);
                foreach ($ipList as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }
        return 'IP não encontrado';
    }

    /**
     * Gera um UUID v4 aleatório para identificação única.
     */
    public static function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Verifica se uma string é um JSON válido.
     */
    public static function isValidJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Converte uma string para formato slug (URL amigável).
     */
    public static function slugify(string $string): string
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Obtém os dados de localização através do ip do usuário
     */
    public static function getCountryByIPv6($ip)
    {
        $apiKey = "9a655cb6aae09c"; // Opcional (ipinfo.io pode ter limite de requisições)
        $url = "https://ipinfo.io/{$ip}/json?token={$apiKey}";
    
        $response = @file_get_contents($url);
        if ($response) {
            $data = json_decode($response, true);
            return $data['country'] ?? 'Não encontrado';
        }
    
        return 'Erro ao consultar API';
    }

}
