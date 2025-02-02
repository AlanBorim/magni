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
        <form id="empresaForm">
            <!-- Campo CNPJ no topo -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="Digite o CNPJ" required>
                    <button type="button" class="btn btn-info mt-2" id="buscarCNPJ">Pesquisar CNPJ</button>
                </div>
            </div>
            <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/messagesReturn.php'); ?>
            <!-- Campos preenchidos automaticamente pela API -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nomeFantasia" class="form-label">Nome Fantasia</label>
                    <input type="text" class="form-control" id="nomeFantasia" name="nomeFantasia" required>
                </div>
                <div class="col-md-6">
                    <label for="razaoSocial" class="form-label">Razão Social</label>
                    <input type="text" class="form-control" id="razaoSocial" name="razaoSocial" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" required>
                </div>
            </div>

            <!-- Campos restantes -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tipoEmpresa" class="form-label">Tipo de Empresa</label>
                    <input type="text" class="form-control" id="tipoEmpresa" name="tipoEmpresa">
                </div>
                <div class="col-md-6">
                    <label for="nomeRepresentante" class="form-label">Nome do Representante</label>
                    <input type="text" class="form-control" id="nomeRepresentante" name="nomeRepresentante">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone">
                </div>
                <div class="col-md-6">
                    <label for="whatsapp" class="form-label">Whatsapp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>

    <!-- Script para Chamada à API -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscarCNPJButton = document.getElementById("buscarCNPJ");
            const cnpjInput = document.getElementById("cnpj");
            const nomeFantasiaInput = document.getElementById("nomeFantasia");
            const razaoSocialInput = document.getElementById("razaoSocial");
            const enderecoInput = document.getElementById("endereco");

            buscarCNPJButton.addEventListener("click", function() {
                const cnpj = cnpjInput.value.trim();

                // Validação básica do CNPJ
                if (!cnpj || cnpj.length !== 14 || isNaN(cnpj)) {
                    alert("Por favor, insira um CNPJ válido.");
                    return;
                }

                // Chamada à API
                fetch("api/lookup/cnpj", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Authorization": "d9f11bda-1234-5678-9101-abcdefabcdef",
                        },
                        body: JSON.stringify({
                            cnpj
                        }),
                    })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error("Erro ao buscar dados do CNPJ.");
                        }
                        return response.json();
                    })
                    .then((data) => {
                        // Preencher os campos com os dados retornados pela API
                        nomeFantasiaInput.value = data.name || "";
                        razaoSocialInput.value = data.name || ""; // Use o campo correto da API
                        enderecoInput.value = data.address || "";
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