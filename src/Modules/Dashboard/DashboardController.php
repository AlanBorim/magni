<?php
namespace App\Modules\Dashboard;

use App\Modules\Company\CompanyService;

class DashboardController
{
    public function showDashboard($language, $companySlug)
    {
        // Verifica se a empresa existe antes de exibir a dashboard
        $companyService = new CompanyService;
        $company = $companyService->findBySlug($companySlug);

        if (!$company) {
            http_response_code(404);
            echo "Empresa não encontrada!";
            exit;
        }

        // Inclui a view da dashboard correspondente
        include __DIR__ . "/views/index.php";
    }
}
