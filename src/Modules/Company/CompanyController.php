<?php

namespace App\Modules\Company;

use App\Core\Database;
use App\Core\MessageHandler;
use App\Core\Security;
use App\Modules\Company\CompanyService;
use App\Modules\Company\CompanyRepository;

class CompanyController
{
    private CompanyService $companyService;

    public function __construct()
    {
        $this->companyService = new CompanyService(new CompanyRepository());
    }

    /**
     * Exibe o formulário de cadastro de empresa
     */
    public function showRegisterCompany()
    {
        include __DIR__ . '/Views/register.php';
    }

    /**
     * Processa o cadastro da empresa e define o usuário logado como admin
     */
    public function processRegisterCompany()
    {
        Security::enforceSessionSecurity();
        var_dump($_POST,$_SESSION);exit;
        
        if (!isset($_SESSION['user_id'])) {
            MessageHandler::redirectWithMessage('danger','company_not_logged', 'Você precisa estar logado para criar uma empresa.', '/login');
        }

        $companyName = trim($_POST['company_name'] ?? '');
        if (empty($companyName)) {
            MessageHandler::redirectWithMessage('danger','company_name_error', 'Nome da empresa é obrigatório.', '/company/register');
        }

        $adminUserId = $_SESSION['user_id'];
        $slug = $this->companyService->generateCompanySlug($companyName);

        $companyId = $this->companyService->createCompany($companyName, $adminUserId, $slug);

        MessageHandler::redirectWithMessage('success','company_success', 'Empresa cadastrada com sucesso!', "/company/$slug/dashboard");
    }

    /**
     * Exibe a dashboard específica da empresa
     */
    public function showDashboard($slug)
    {
        $company = $this->companyService->getCompanyBySlug($slug);
        if (!$company) {
            MessageHandler::redirectWithMessage('danger','comapny_not_found', 'Empresa não encontrada.', '/');
        }

        include __DIR__ . '/../Views/dashboard.php';
    }

    /**
     * Exibe a página de configurações da empresa
     */
    public function showSettings($slug)
    {
        $company = $this->companyService->getCompanyBySlug($slug);
        if (!$company) {
            MessageHandler::redirectWithMessage('danger','comapny_not_found', 'Empresa não encontrada.', '/');
        }

        include __DIR__ . '/../Views/settings.php';
    }

    /**
     * Atualiza as configurações da empresa
     */
    public function updateSettings($slug)
    {
        $company = $this->companyService->getCompanyBySlug($slug);
        if (!$company) {
            MessageHandler::redirectWithMessage('danger','comapny_not_found', 'Empresa não encontrada.', '/');
        }

        $this->companyService->updateCompanySettings($company['id'], $_POST);
        MessageHandler::redirectWithMessage('success','company_update_success', 'Configurações atualizadas!', "/company/$slug/settings");
    }
}
