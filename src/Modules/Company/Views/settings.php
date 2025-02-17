<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\ViewHelper;
use App\Core\Security;
use App\Core\LanguageDetector;
use App\Core\SessionManager;
use App\Modules\Company\CompanyService;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

Security::initializeSessionSecurity();

// Recupera ID da empresa via URL
$slug = base64_decode($_GET['s']) ?? null;
$companyService = new CompanyService();
$company = $companyService->findBySlug($slug);

if (!$company) {
    echo 'Empresa não encontrada.';
    exit;
}

// Verifica se o usuário tem permissão para editar a empresa
if ($company['admin_id'] !== SessionManager::get('user_id')) {
    die('Acesso negado.');
}

SessionManager::renewSession();
?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações da Empresa</title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
    <link href="../../../../public/assets/css/custom-settings.css" type="text/css" rel="stylesheet">
    
</head>

<body>

    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Menu Lateral -->
            <nav id="sidebar" class="sidebar">
                <ul class="nav flex-column text-center w-100">
                    <li class="nav-item">
                        <a class="nav-link py-3" href="?tab=empresa&s=<?= $_GET['s'] ?>" title="Empresa">
                            <i class="bi bi-building fs-4"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" href="?tab=usuarios&s=<?= $_GET['s'] ?>" title="Usuários">
                            <i class="bi bi-people fs-4"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" href="?tab=modulos&s=<?= $_GET['s'] ?>" title="Módulos">
                            <i class="bi bi-box fs-4"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3" href="?tab=plano&s=<?= $_GET['s'] ?>" title="Plano">
                            <i class="bi bi-graph-up fs-4"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Conteúdo Principal -->
            <main class="col-md-10 ms-sm-auto mx-auto mt-4">
                <h4>Configurações de <?= htmlspecialchars($company['company_name']) ?></h4>

                <?php
                $tab = $_GET['tab'] ?? 'empresa';

                switch ($tab):
                    case 'empresa':
                ?>
                        <h6>Dados da Empresa</h6>
                        <form action="/<?= $currentLanguage ?>/company/updateCompany" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="company_id" value="<?= $company['id'] ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="companyName" class="form-label">Nome da Empresa</label>
                                    <input type="text" class="form-control" id="companyName" name="companyName" value="<?= htmlspecialchars($company['company_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cnpj" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" value="<?= htmlspecialchars($company['cnpj_cpf']) ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($company['email']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phoneNumber" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= htmlspecialchars($company['phone_number']) ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="country" class="form-label">País</label>
                                    <input type="text" class="form-control" id="country" name="country" value="<?= htmlspecialchars($company['country']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="state" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($company['state']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="city" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($company['city']) ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="zipcode" class="form-label">CEP</label>
                                    <input type="text" class="form-control" id="zipcode" name="zipcode" value="<?= htmlspecialchars($company['zipcode']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($company['address']) ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="neighborhood" class="form-label">Bairro</label>
                                    <input type="text" class="form-control" id="neighborhood" name="neighborhood" value="<?= htmlspecialchars($company['neighborhood']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="site" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="site" name="site" value="<?= htmlspecialchars($company['site']) ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="logo" class="form-label">Logotipo</label>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Descrição da Empresa</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($company['description']) ?></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                        </form>



                    <?php
                        break;
                    case 'usuarios':
                    ?>
                        <h4>Usuários da Empresa</h4>
                        <p>Gerencie os usuários que têm acesso à empresa.</p>
                        <a href="/<?= $currentLanguage ?>/company/manageUsers" class="btn btn-info">Gerenciar Usuários</a>
                    <?php
                        break;
                    case 'modulos':
                    ?>
                        <h4>Módulos Adicionais</h4>
                        <p>Ative ou desative funcionalidades adicionais para sua empresa.</p>
                        <form action="/<?= $currentLanguage ?>/company/updateModules" method="post">
                            <input type="hidden" name="company_id" value="<?= $company['id'] ?>">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="modules[]" value="financeiro" id="modFinanceiro" checked>
                                <label class="form-check-label" for="modFinanceiro">Módulo Financeiro</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="modules[]" value="vendas" id="modVendas">
                                <label class="form-check-label" for="modVendas">Módulo de Vendas</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="modules[]" value="estoque" id="modEstoque">
                                <label class="form-check-label" for="modEstoque">Módulo de Estoque</label>
                            </div>

                            <button type="submit" class="btn btn-success mt-3">Salvar Módulos</button>
                        </form>
                    <?php
                        break;
                    case 'plano':
                    ?>
                        <h4>Upgrade de Plano</h4>
                        <p>Atualmente no plano: <strong>Básico</strong></p>
                        <a href="/<?= $currentLanguage ?>/company/upgradePlan" class="btn btn-warning">Upgrade de Plano</a>
                <?php
                        break;
                endswitch;
                ?>
            </main>
        </div>
    </div>

    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
    <!-- intl-tel-input para formatação do telefone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.querySelector("#phoneNumber");
            window.intlTelInput(phoneInput, {
                initialCountry: "br",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
            });
        });
    </script>
</body>

</html>