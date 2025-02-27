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
use RuntimeException;

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
        include __DIR__ . '/views/register.php';
    }

    public function showCompanySettings()
    {
        include __DIR__ . '/views/settings.php';
    }

    public function showCompanyDashboard()
    {
        include __DIR__ . '/views/dashboard.php';
    }

    /**
     * Processa o cadastro da empresa e define o usuário logado como admin
     */
    public function processRegisterCompany()
    {
        Security::initializeSessionSecurity();

        if ($this->companyService->findByCNPJ($_REQUEST['cnpj'])) {
            MessageHandler::redirectWithMessage('error', 'company_error', 'Empresa já cadastrada!', '/company/registerCompany');
        }

        $adminUserId = SessionManager::get('user_id');
        $slug = Helper::slugify($_REQUEST['companyName']);

        $companyId = $this->companyService->registerCompany($_REQUEST, $_FILES);

        MessageHandler::redirectWithMessage('success', 'company_success', 'Empresa cadastrada com sucesso!', "/$slug/dashboard");
    }

    public function processUpdateCompany()
    {
        Security::initializeSessionSecurity();

        try {

            // Valida se a sessão foi realmente criada
            if (!$this->companyService->updateCompany($_REQUEST)) {
                throw new RuntimeException("Falha ao atualizar os dados da empresa.");
            }

            MessageHandler::redirectWithMessage('success', 'company_update_success', 'Dados da empresa alterados com sucesso!', "/" . $_REQUEST['slug'] . "/settings?s=" . base64_encode($_REQUEST['slug']));
            return;
        } catch (RuntimeException $e) {

            MessageHandler::redirectWithMessage('danger', 'company_update_error', 'Erro ao atualizar os dados. ' . $e->getMessage() . ' Tente novamente.', "/" . $_REQUEST['slug'] . "/settings?s=" . base64_encode($_REQUEST['slug']));

            return;
        }
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

    /**
     * Verifica o acesso à empresa e redireciona conforme o status do usuário.
     * 
     * Quando o link <em>magni.apoio19.com.br/pt/slug-da-empresa</em> é acessado:
     * - Se o usuário estiver logado, redireciona para o dashboard da empresa.
     * - Caso contrário, exibe a tela de login da empresa.
     *
     * @param string $slug O identificador da empresa na URL.
     */
    public function accessCompany(string $slug)
    {
        Security::initializeSessionSecurity();
        $user = SessionManager::get('user');

        if ($user) {
            // Usuário logado, redireciona para o dashboard da empresa
            header("Location: /{$slug}/dashboard/");
            exit;
        } else {
            // Usuário não logado, exibe a tela de login da empresa
            include __DIR__ . '/views/login.php';
        }
    }
}
