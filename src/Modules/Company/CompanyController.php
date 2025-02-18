<?php

namespace App\Modules\Company;

use App\Core\MessageHandler;
use App\Core\Security;
use App\Core\SessionManager;
use App\Core\Helpers\Helper;

use App\Modules\Company\CompanyService;
use App\Modules\Company\CompanyRepository;
use App\Modules\Login\LoginController;
use App\Modules\Dashboard\DashboardController;

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

    public function showCompanySettings()
    {
        include __DIR__ . '/Views/settings.php';
    }

    /**
     * Processa o cadastro da empresa e define o usuário logado como admin
     */
    public function processRegisterCompany()
    {
        Security::initializeSessionSecurity();

        $adminUserId = SessionManager::get('user_id');
        $slug = Helper::slugify($_REQUEST['companyName']);


        $companyId = $this->companyService->registerCompany($_REQUEST,$_FILES);

        MessageHandler::redirectWithMessage('success', 'company_success', 'Empresa cadastrada com sucesso!', "/$slug/dashboard");
    }

    public function processUpdateCompany()
    {
        var_dump($_REQUEST);
    }



    public function getCompanies()
    {
        $adminUserId = SessionManager::get('user_id');
        $companies = $this->companyService->getCompaniesByAdmin($adminUserId);
    }

    public function handleCompanyAccess($companySlug)
    {
        // Obtém o idioma da URL
        $uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $language = $uriParts[0] ?? 'pt'; // Padrão: 'pt' se não encontrado

        // Verifica se a empresa existe
        $company = $this->companyService->findBySlug($companySlug);

        if (!$company) {
            http_response_code(404);
            echo "Empresa não encontrada!";
            exit;
        }

        // Se o usuário estiver logado, carrega a dashboard
        if (!empty(SessionManager::get('user_id'))) {
            $dashboardController = new DashboardController();
            $dashboardController->showDashboard($language, $companySlug);
            return;
        }

        // Se não estiver logado, direciona para a tela de login da empresa
        $loginController = new LoginController();
        $loginController->showLogin($language, $companySlug);
    }
}
