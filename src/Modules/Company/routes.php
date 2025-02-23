<?php

use App\Modules\Company\CompanyController;



// Rota para acessar a empresa pelo slug
$router->get('/company/registerCompany', [CompanyController::class, 'showRegisterCompany']); // Cadastro de empresa
$router->get('/{companySlug}/login', [CompanyController::class, 'handleCompanyAccess']);
$router->get('/{companySlug}/settings', [CompanyController::class, 'showCompanySettings']); // Configurações da empresa

$router->post('/company/register', [CompanyController::class, 'processRegisterCompany']); // Processar cadastro
$router->post('/{companySlug}/updateCompany', [CompanyController::class, 'processUpdateCompany']); // Configurações da empresa
