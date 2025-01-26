<?php

use App\Modules\Login\LoginController;

// Definição das rotas para o módulo Login
$router->get('/', [LoginController::class, 'showLogin']);          // home e tela de login
$router->get('/logout', [LoginController::class, 'logout']);       // logout sistema
$router->get('/dashboard', [LoginController::class, 'showDashboard']); // dashboard
$router->get('/forgot-password', [LoginController::class,'showForgotPassword']); // recuperar senha
$router->get('/register', [LoginController::class,'showRegister']); // recuperar senha
$router->get('/reset-password', [LoginController::class, 'showResetPasswordForm']);
$router->get('/two-factor-check', [LoginController::class, 'show2fa']); // apresenta a validação de 2fa

// Definição das rotas para o módulo login método post
$router->post('/login', [LoginController::class, 'processLogin']); // processo de envio das informações para o login
$router->post('/forgotPassword', [LoginController::class, 'processForgotPassword']); // processo de envio do reset da senha
$router->post('/reset-password', [LoginController::class, 'processResetPassword']); // processo de reset de senha
$router->post('/two-factor-check', [LoginController::class, 'process2fa']); // processo de validação de 2fa
$router->post('/resendActivationEmail', [LoginController::class, 'resendActivationEmail']); // processo de validação de 2fa
