<?php

namespace App\Modules\Company;

use App\Core\Database;
use App\Core\MessageHandler;
use App\Core\Security;
use App\Modules\Company\CompanyService;
use App\Modules\Company\CompanyRepository;
use App\Modules\Login\LoginController;

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
