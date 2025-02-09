<?php

namespace App\Modules\ApiService;


use GuzzleHttp\Client;

class AiConnection
{
    public function handleAiConnection()
    {

        // Defina sua chave da API do OpenAI aqui (se não estiver usando variáveis de ambiente)
        $apiKey = $_ENV['OPENAI_API_KEY']; // Use uma variável de ambiente para segurança
        $inputData = json_decode(file_get_contents('php://input'), true);

        // Verifique se o método HTTP é POST e o prompt foi enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Captura o corpo da requisição
            
            if (isset($inputData['prompt']) && !empty($inputData['prompt'])) {
                // Cria uma instância do cliente Guzzle
                $client = new Client();
                try {
                    // Chama a API da OpenAI usando o endpoint correto para chat
                    $response = $client->post('https://api.openai.com/v1/chat/completions', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $apiKey
                        ],
                        'json' => [
                            'model' => 'gpt-3.5-turbo', // O modelo de chat desejado
                            'messages' => [
                                ['role' => 'system', 'content' => 'Você é um assistente útil para gerar descrições de empresas.'],
                                ['role' => 'user', 'content' => $inputData['prompt']]
                            ],
                            'max_tokens' => 150,
                            'temperature' => 0.7
                        ]
                    ]);

                    // Processa a resposta da OpenAI
                    $responseBody = json_decode($response->getBody(), true);

                    if (isset($responseBody['choices'][0]['message']['content'])) {
                        // Retorna a resposta gerada
                        echo json_encode([
                            'success' => true,
                            'response' => $responseBody['choices'][0]['message']['content']
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Erro ao gerar a resposta'
                        ]);
                    }
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    // Caso ocorra um erro ao fazer a requisição
                    echo json_encode([
                        'success' => false,
                        'message' => 'Erro ao conectar com a API: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Prompt não fornecido.'
                ]);
            }
        } else {
            // Se a requisição não for POST
            echo json_encode([
                'success' => false,
                'message' => 'Método não permitido.'
            ]);
        }
    }
}
