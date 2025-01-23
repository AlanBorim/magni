<?php

use App\Modules\Login\LoginController;

// Definição das rotas para o módulo Login
$router->get('/', [LoginController::class, 'showLogin']);          // /{idioma}/
$router->get('/logout', [LoginController::class, 'logout']);       // /{idioma}/logout
$router->get('/dashboard', [LoginController::class, 'dashboard']); // /{idioma}/dashboard

// Definição das rotas para o módulo login método post
$router->post('/login', [LoginController::class, 'processLogin']); // /{idioma}/login