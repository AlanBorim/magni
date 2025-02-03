<?php

use App\Modules\ApiService\ApiController;

$router->get('/api/getData', [ApiController::class, 'handleGetRequest']); // Consulta via GET

$router->post('/api/postData', [ApiController::class, 'handlePostRequest']); // Consulta via POST
$router->post('/api/token', [ApiController::class, 'generateToken']); // Geração de token
