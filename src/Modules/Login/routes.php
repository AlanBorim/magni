<?php

use App\Modules\Login\LoginController;

// Definição das rotas para o módulo Login
$router->get('/', [LoginController::class, 'showLogin']);          // home e tela de login
$router->get('/logout', [LoginController::class, 'logout']);       // logout sistema
$router->get('/dashboard', [LoginController::class, 'dashboard']); // dashboard
$router->get('/forgot-password', [LoginController::class,'forgotPassword']); // recuperar senha
$router->get('/register', [LoginController::class,'register']); // recuperar senha

// Definição das rotas para o módulo login método post
$router->post('/login', [LoginController::class, 'processLogin']); // processo de envio das informações para o login