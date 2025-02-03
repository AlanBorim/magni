<?php

namespace App\Modules\ApiService;

use App\Core\Database;
use PDO;

class ApiToken
{
    private static string $secretKey = "CHAVE_SECRETA_SEGURA"; // Altere para uma chave segura

    /**
     * Gera um novo token para um usuário ou serviço.
     */
    public static function generateToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32)); // Gera um token seguro
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day')); // Expira em 24h

        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO api_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $stmt->execute([
            'user_id' => $userId,
            'token' => hash_hmac('sha256', $token, self::$secretKey),
            'expires_at' => $expiresAt
        ]);

        return $token;
    }

    /**
     * Valida um token fornecido no cabeçalho da requisição.
     */
    public static function validateToken(string $token): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM api_tokens WHERE token = :token AND expires_at > NOW() LIMIT 1");
        $stmt->execute(['token' => hash_hmac('sha256', $token, self::$secretKey)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return !empty($result);
    }
}
