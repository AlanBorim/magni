<?php

namespace App\Modules\ApiService;

class ApiService
{
    private ApiClient $apiClient;

    public function __construct()
    {
        $this->apiClient = new ApiClient();
    }

    /**
     * Envia requisição GET para API externa.
     */
    public function sendGetRequest(string $url)
    {
        return $this->apiClient->get($url);
    }

    /**
     * Envia requisição POST para API externa.
     */
    public function sendPostRequest(string $url, array $data)
    {
        return $this->apiClient->post($url, $data);
    }
}
