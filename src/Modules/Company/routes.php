<?php

use App\Modules\Company\CompanyController;

// Rota para cadastro de empresa
$router->get('/company/registerCompany', [CompanyController::class, 'showRegisterCompany']);

// Rota para verificação de acesso à empresa via slug
$router->get('/{companySlug}/login', [CompanyController::class, 'accessCompany']);

// Rota para exibição do dashboard da empresa
$router->get('/{companySlug}/dashboard', [CompanyController::class, 'showCompanyDashboard']);

// Rota para exibição das configurações da empresa
$router->get('/{companySlug}/settings', [CompanyController::class, 'showCompanySettings']);

// Processa o cadastro da empresa
$router->post('/company/register', [CompanyController::class, 'processRegisterCompany']);

// Atualiza os dados da empresa (rotina existente)
$router->post('/{companySlug}/updateCompany', [CompanyController::class, 'processUpdateCompany']);