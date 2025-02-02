<?php

use App\Modules\Company\CompanyController;
use App\Modules\Company\UserController;

$router->get('/company/registerCompany', [CompanyController::class, 'showRegisterCompany']); // Cadastro de empresa
$router->post('/company/register', [CompanyController::class, 'processRegisterCompany']); // Processar cadastro

$router->get('/company/{slug}/dashboard', [CompanyController::class, 'showDashboard']); // Dashboard por empresa

$router->get('/company/{slug}/settings', [CompanyController::class, 'showSettings']); // Configurações da empresa
$router->post('/company/{slug}/settings', [CompanyController::class, 'updateSettings']); // Atualizar configurações

// $router->get('/company/{slug}/users', [UserController::class, 'listUsers']); // Listar usuários da empresa
// $router->post('/company/{slug}/users/add', [UserController::class, 'addUser']); // Criar usuário
// $router->post('/company/{slug}/users/update', [UserController::class, 'updateUser']); // Atualizar permissões
// $router->post('/company/{slug}/users/remove', [UserController::class, 'removeUser']); // Remover usuário
