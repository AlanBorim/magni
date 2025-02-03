<?php

namespace App\Modules\ApiService\Middleware;

use App\Modules\ApiService\ApiToken;

class AuthMiddleware
{
    /**
     * Verifica se o token de autenticação é válido.
     */
    public static function verifyToken(): bool
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
   
        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Token de acesso ausente."]);
            exit;
        }

        return ApiToken::validateToken($token);
    }
}
