<?php

namespace App\Modules\Company;

use App\Core\MessageHandler;
use App\Core\Security;
use App\Core\SessionManager;
use App\Modules\Company\CompanyService;
use App\Modules\Company\CompanyRepository;
use App\Modules\Login\LoginController;
use App\Core\Helpers\Helper;

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
        Security::initializeSessionSecurity();

        $adminUserId = SessionManager::get('user_id');
        $slug = Helper::slugify($_REQUEST['companyName']);

        $companyId = $this->companyService->registerCompany($_REQUEST);

        MessageHandler::redirectWithMessage('success','company_success', 'Empresa cadastrada com sucesso!', "/company/$slug/dashboard");
    }

    public function handleCompanyAccess($companySlug)
    {
        session_start();

        // Verifica se a empresa existe
        $companyService = new CompanyService();
        $company = $companyService->findBySlug($companySlug);

        if (!$company) {
            http_response_code(404);
            echo "Empresa não encontrada!";
            exit;
        }

        // Se o usuário estiver logado, redireciona para a dashboard da empresa
        if (!empty($_SESSION['user_id'])) {
            header("Location: /{$companySlug}/dashboard");
            exit;
        }

        // Se não estiver logado, direciona para a tela de login da empresa
        $loginController = new LoginController();
        $loginController->showLogin($companySlug);
    }
}
