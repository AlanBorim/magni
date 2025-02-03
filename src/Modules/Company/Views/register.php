<?php
include __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\LanguageDetector;
use App\Core\Security;
use App\Core\SessionManager;
use App\Core\ViewHelper;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

Security::enforceSessionSecurity();
SessionManager::renewSession();

$role = $_SESSION['roleName']; // Permissões do usuário
$twoFactorEnabled = $_SESSION['two_factor_enabled']; // Adicionei essa variável para verificar se o 2FA está habilitado

?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Empresa</title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>
</head>

<body>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>
    <div class="container mt-5">
    <h2>Cadastro de Empresa</h2>
    <form id="empresaForm" enctype="multipart/form-data">
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
                    <input type="text" class="form-control me-2" id="cnpj" name="cnpj" placeholder="Digite o CNPJ" required>
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
                <input type="url" class="form-control" id="site" name="site">
            </div>
        </div>

        <!-- Telefone e País -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="phoneCountry" class="form-label">Phone Country</label>
                <input type="text" class="form-control" id="phoneCountry" name="phoneCountry" placeholder="+XX">
            </div>
            <div class="col-md-3">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber">
            </div>
            <div class="col-md-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control" id="country" name="country">
            </div>
            <div class="col-md-3">
                <label for="state" class="form-label">State</label>
                <input type="text" class="form-control" id="state" name="state">
            </div>
        </div>

        <!-- Cidade e Endereço -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city">
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address">
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
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
        </div>

        <!-- Botão de Envio -->
        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">Salvar</button>
        </div>
    </form>
</div>



    <!-- Script para Chamada à API -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const buscarCNPJButton = document.getElementById("buscarCNPJ");
    const cnpjInput = document.getElementById("cnpj");
    const companyNameInput = document.getElementById("companyName");
    const emailInput = document.getElementById("email");
    const phoneNumberInput = document.getElementById("phoneNumber");
    const phoneCountryInput = document.getElementById("phoneCountry");
    const siteInput = document.getElementById("site");
    const countryInput = document.getElementById("country");
    const stateInput = document.getElementById("state");
    const cityInput = document.getElementById("city");
    const addressInput = document.getElementById("address");

    // Quadro de informações da empresa
    const empresaInfoDiv = document.getElementById("empresaInfo");
    const statusEmpresa = document.getElementById("statusEmpresa");
    const atividadePrincipal = document.getElementById("atividadePrincipal");

    buscarCNPJButton.addEventListener("click", function () {
        let cnpj = cnpjInput.value.trim().replace(/\D/g, ""); // Remove caracteres não numéricos

        // Validação do CNPJ
        if (!/^\d{14}$/.test(cnpj)) {
            alert("Por favor, insira um CNPJ válido (14 números).");
            return;
        }

        // Monta a URL com a query string
        const apiUrl = `/<?=$currentLanguage?>/api/getData?url=https://www.receitaws.com.br/v1/cnpj/${cnpj}`;

        // Chamada à API via GET
        fetch(apiUrl, {
            method: "GET",
            headers: {
                "Authorization": "d40f92bbc08924f8c99f1a6e3b7a9d171c7870bf24b2210e4591fea92311ae64",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Erro ao buscar dados do CNPJ.");
                }
                return response.json();
            })
            .then((data) => {
                // Preenche os campos com os dados retornados pela API
                companyNameInput.value = data.nome || "";
                emailInput.value = data.email || "";
                phoneNumberInput.value = data.telefone || "";
                siteInput.value = data.site || "";
                countryInput.value = "Brazil"; // Receita WS só retorna empresas brasileiras
                stateInput.value = data.uf || "";
                cityInput.value = data.municipio || "";
                addressInput.value = `${data.logradouro}, ${data.numero} - ${data.bairro}`;

                // Atualiza o status e exibe o quadro
                statusEmpresa.textContent = data.situacao === "ATIVA" ? "✅ Ativa" : "❌ Inativa";
                atividadePrincipal.textContent = data.atividade_principal[0]?.text || "Não informado";
                empresaInfoDiv.classList.remove("d-none");
            })
            .catch((error) => {
                alert(error.message || "Erro ao consultar o CNPJ.");
            });
    });
});

    </script>

    <!-- Rodapé -->
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/footer.php'); ?>
</body>

</html>