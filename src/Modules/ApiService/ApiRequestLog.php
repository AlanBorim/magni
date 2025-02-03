<?php

namespace App\Modules\ApiService;

use App\Core\Database;
use PDO;

class ApiRequestLog
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registra uma consulta realizada via API.
     */
    public function logRequest(string $url, string $method, array $payload = [], string $response = '')
    {
        $stmt = $this->db->prepare("
            INSERT INTO api_request_logs (url, method, payload, response, created_at)
            VALUES (:url, :method, :payload, :response, NOW())
        ");
        $stmt->execute([
            'url' => $url,
            'method' => strtoupper($method),
            'payload' => json_encode($payload),
            'response' => $response
        ]);
    }
}
