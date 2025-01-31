<?php

use App\Modules\Login\LoginController;
use App\Modules\Login\ProfileController;

// Definição das rotas para o módulo Login
$router->get('/', [LoginController::class, 'showLogin']);          // home e tela de login
$router->get('/logout', [LoginController::class, 'logout']);       // logout sistema
$router->get('/dashboard', [LoginController::class, 'showDashboard']); // dashboard
$router->get('/forgot-password', [LoginController::class,'showForgotPassword']); // recuperar senha
$router->get('/register', [LoginController::class,'showRegister']); // tela de registro de usuario
$router->get('/reset-password', [LoginController::class, 'showResetPasswordForm']);
$router->get('/two-factor-check', [LoginController::class, 'show2fa']); // apresenta a validação de 2fa
$router->get('/activateLogin', [LoginController::class, 'activateLogin']); //rota da ativação de login
$router->get('/profile', [LoginController::class, 'showProfile']); //apresenta o perfil do usuario
$router->get('/enable2fa', [LoginController::class, 'showEnable2fa']); //apresenta o perfil do usuario


// Definição das rotas para o módulo login método post
$router->post('/login', [LoginController::class, 'processLogin']); // processo de envio das informações para o login
$router->post('/forgotPassword', [LoginController::class, 'processForgotPassword']); // processo de envio do reset da senha
$router->post('/reset-password', [LoginController::class, 'processResetPassword']); // processo de reset de senha
$router->post('/two-factor-check', [LoginController::class, 'process2fa']); // processo de validação de 2fa
$router->post('/resendActivationEmail', [LoginController::class, 'resendActivationEmail']); // processo de validação de 2fa
$router->post('/register', [LoginController::class,'processRegister']); // tela de registro de usuario
$router->post('/process2fa', [LoginController::class, 'processEnable2fa']); // processamento e gravação de 2fa

$router->post('/update-profile', [ProfileController::class,'processUpdateProfile']);
$router->post('/update-profile-picture', [ProfileController::class,'processUpdateProfilePic']);
$router->post('/update-password', [ProfileController::class,'processUpdateProfilePass']);