<?php

namespace App\Modules\ApiService;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout'  => 10.0, // Tempo limite de resposta
            'verify'   => false, // Evita problemas com SSL em desenvolvimento
        ]);
    }

    /**
     * Realiza uma requisição GET para um serviço externo.
     */
    public function get(string $url)
    {
        try {
            $response = $this->client->request('GET', $url);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => 'Falha na requisição: ' . $e->getMessage()];
        }
    }

    /**
     * Realiza uma requisição POST para um serviço externo.
     */
    public function post(string $url, array $data)
    {
        try {
            $response = $this->client->request('POST', $url, [
                'json' => $data
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => 'Falha na requisição: ' . $e->getMessage()];
        }
    }
}
