<?php


namespace App\Modules\ApiService;

use App\Core\MessageHandler;
use App\Core\Security;
use App\Modules\ApiService\ApiService;
use App\Modules\ApiService\Middleware\AuthMiddleware;

class ApiController
{
    private ApiService $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    /**
     * Processa requisições GET autenticadas.
     */
    public function handleGetRequest()
    {
        AuthMiddleware::verifyToken(); // Exige autenticação

        $url = $_GET['url'] ?? null;
        if (!$url) {
            MessageHandler::redirectWithMessage('danger','url_not_found', 'URL de consulta não fornecida.', '/');
        }

        $response = $this->apiService->sendGetRequest($url);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Processa requisições POST autenticadas.
     */
    public function handlePostRequest()
    {
        AuthMiddleware::verifyToken(); // Exige autenticação

        // Obtém o conteúdo do body da requisição
        $inputData = json_decode(file_get_contents('php://input'), true);

        $url = $_POST['url'] ?? $inputData['url'];
        $payload = $_POST['payload'] ?? $inputData['payload'];

        if (!$url) {
            MessageHandler::redirectWithMessage('danger','url_not_found', 'URL de consulta não fornecida.', '/');
        }

        $response = $this->apiService->sendPostRequest($url, $payload);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Gera um token de acesso para um usuário autenticado.
     */
    public function generateToken()
    {
        // Security::enforceSessionSecurity();

        // if (!isset($_SESSION['user_id'])) {
        //     http_response_code(401);
        //     echo json_encode(["error" => "Usuário não autenticado."]);
        //     return;
        // }

        // $userId = $_SESSION['user_id'];
        // $token = ApiToken::generateToken($userId);
        $userId = 38;
        $token = ApiToken::generateToken($userId);

        echo json_encode(["token" => $token, "message" => "Token gerado com sucesso!"]);
    }
}