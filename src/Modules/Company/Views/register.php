<?php
include __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\Helpers\Helper;
use App\Core\LanguageDetector;
use App\Core\Security;
use App\Core\SessionManager;
use App\Core\ViewHelper;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

Security::initializeSessionSecurity();

// Recupera informações do usuário
$role = SessionManager::get('roleName', 'guest'); // Se não houver, assume 'guest'
$twoFactorEnabled = SessionManager::get('two_factor_enabled', false);

$ip = Helper::getClientIP();
$pais = Helper::getCountryByIPv6($ip);
?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Empresa</title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
    
</head>

<body data-country="<?= strtolower($pais) ?>" data-lang="<?= $currentLanguage ?>">
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>
    <div class="container mt-5">
        <h2>Cadastro de Empresa</h2>
        <form id="empresaForm" enctype="multipart/form-data" method="post" action="/<?= $currentLanguage ?>/company/register">
            <div class="row mb-3">
                <!-- Company Name -->
                <div class="col-md-6">
                    <label for="companyName" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="companyName" name="companyName" required>
                </div>
                <!-- CNPJ e botão de consulta -->
                <div class="col-md-6">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <div class="d-flex">
                        <input type="text" class="form-control me-2" id="cnpj" name="cnpj" placeholder="Digite o CNPJ">
                        <button type="button" class="btn btn-info" id="buscarCNPJ"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>

            <!-- Quadro de Status da Empresa -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div id="empresaInfo" class="d-none">
                        <h5>Status: <span id="statusEmpresa" class="fw-bold"></span></h5>
                        <p><strong>Atividade Principal:</strong> <span id="atividadePrincipal"></span></p>
                    </div>
                </div>
            </div>

            <!-- Email e Site -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="col-md-6">
                    <label for="site" class="form-label">Website</label>
                    <input type="text" class="form-control" id="site" name="site">
                </div>
            </div>

            <!-- Telefone e País -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber">
                </div>
                <div class="col-md-3">
                    <label for="country" class="form-label">Country</label>
                    <select id="country" class="form-control">
                        <!-- Lista de países -->
                        <option value="AU" <?= $pais === "AU" ? "selected" : "" ?>>Australia</option>
                        <option value="BR" <?= $pais === "BR" ? "selected" : "" ?>>Brazil</option>
                        <option value="US" <?= $pais === "US" ? "selected" : "" ?>>United States</option>
                        <option value="FR" <?= $pais === "FR" ? "selected" : "" ?>>France</option>
                        <option value="DE" <?= $pais === "DE" ? "selected" : "" ?>>Germany</option>
                        <!-- Adicione mais opções conforme necessário -->
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="state" class="form-label">State</label>
                    <input type="text" class="form-control" id="state" name="state">
                </div>
                <div class="col-md-2">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city">
                </div>
                <div class="col-md-2">
                    <label for="zipcode" class="form-label">Zipcode</label>
                    <input type="text" class="form-control" id="zipcode" name="zipcode">
                </div>
            </div>

            <!-- Cidade e Endereço -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address">
                </div>
                <div class="col-md-2">
                    <label for="addressNumber" class="form-label">Number</label>
                    <input type="text" class="form-control" id="addressNumber" name="addressNumber">
                </div>
                <div class="col-md-6">
                    <label for="neighborhood" class="form-label">Neighborhood</label>
                    <input type="text" class="form-control" id="neighborhood" name="neighborhood">
                </div>
            </div>

            <!-- Upload de Logo -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="logo" class="form-label">Company Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                </div>
            </div>

            <!-- Descrição -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="Description" class="form-label">Description</label>
                    <textarea class="form-control" id="Description" name="Description" rows="5" readonly></textarea>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn btn-info mt-2" id="descriptionBtn">
                        Gerar Descrição
                    </button>
                </div>
            </div>

            <!-- Botão de Envio -->
            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">Salvar</button>
            </div>
        </form>
    </div>

    <!-- Script para Chamada à API -->
    <script src="../../../../public/assets/js/company.js"></script>

    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>

    <!-- intl-tel-input CSS e JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
</body>

</html>