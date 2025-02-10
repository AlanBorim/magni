document.addEventListener("DOMContentLoaded", function () {
    // Obtendo o valor de data-country e data-lang
    const pais = document.body.getAttribute('data-country');
    const currentLanguage = document.body.getAttribute('data-lang');

    const buscarCNPJButton = document.getElementById("buscarCNPJ");
    const cnpjInput = document.getElementById("cnpj");
    const companyNameInput = document.getElementById("companyName");
    const phoneInput = document.getElementById("phoneNumber");
    const siteInput = document.getElementById("site");
    const countryInput = document.getElementById("country");

    // Quadro de informações da empresa
    const empresaInfoDiv = document.getElementById("empresaInfo");
    const statusEmpresa = document.getElementById("statusEmpresa");
    const atividadePrincipal = document.getElementById("atividadePrincipal");

    const hiddenActivity = document.getElementById("atividade");
    const hiddenActivityCode = document.getElementById("atividadeCodigo");
    const hiddenStatus = document.getElementById("status");

    // Evento para buscar os dados do CNPJ ao clicar no botão
    buscarCNPJButton.addEventListener("click", function () {
        let cnpj = cnpjInput.value.trim().replace(/\D/g, ""); // Remove caracteres não numéricos

        // Validação do CNPJ
        if (!/^\d{14}$/.test(cnpj)) {
            alert("Por favor, insira um CNPJ válido (14 números).");
            return;
        }

        // Exibe o spinner e desabilita o botão
        buscarCNPJButton.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>'; // Exibe o spinner
        buscarCNPJButton.disabled = true;

        // Monta a URL com a query string
        const apiUrl = '/' + currentLanguage + '/api/getData?url=https://www.receitaws.com.br/v1/cnpj/' + cnpj;

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
                siteInput.value = data.site || "";
                countryInput.value = "BR"; // Receita WS só retorna empresas brasileiras

                // Atualiza o status e exibe o quadro
                statusEmpresa.textContent = data.situacao === "ATIVA" ? "✅ Ativa" : "❌ Inativa";
                atividadePrincipal.textContent = data.atividade_principal[0]?.text || "Não informado";
                empresaInfoDiv.classList.remove("d-none");

                hiddenStatus.value = data.situacao || "";
                
                // Preenche o código e descrição da atividade principal
                const atividade = data.atividade_principal[0] || {}; // Pega o primeiro objeto de atividade principal
                hiddenActivity.value = atividade.text || "Não informado"; // Salva a descrição da atividade
                hiddenActivityCode.value = atividade.code || ""; // Salva o código da atividade principal

            })
            .catch((error) => {
                alert(error.message || "Erro ao consultar o CNPJ.");
            })
            .finally(() => {
                // Remove o spinner e habilita o botão novamente
                buscarCNPJButton.innerHTML = '<i class="bi bi-search"></i>'; // Remove o spinner
                buscarCNPJButton.disabled = false;
            });
    });

    // Alteração da bandeira do número de telefone ao alterar o país
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: pais,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });

    countryInput.addEventListener("change", function () {
        const selectedCountry = countryInput.value;
        iti.setCountry(selectedCountry);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const zipcodeInput = document.getElementById("zipcode");
    const stateInput = document.getElementById("state");
    const cityInput = document.getElementById("city");
    const addressInput = document.getElementById("address");
    const neighborhood = document.getElementById("neighborhood");

    zipcodeInput.addEventListener("blur", function () {
        let zipcode = zipcodeInput.value.trim().replace(/\D/g, ""); // Remove caracteres não numéricos

        // Validação básica do CEP
        if (!/^\d{8}$/.test(zipcode)) {
            alert("Por favor, insira um CEP válido (8 dígitos numéricos).");
            return;
        }

        // URL da API ViaCEP
        const viaCepUrl = `https://viacep.com.br/ws/${zipcode}/json/`;

        // Faz a requisição para a API ViaCEP
        fetch(viaCepUrl)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Erro ao buscar o CEP.");
                }
                return response.json();
            })
            .then((data) => {
                if (data.erro) {
                    alert("CEP não encontrado!");
                    return;
                }

                // Preenche os campos automaticamente com os dados retornados
                stateInput.value = data.uf || "";
                cityInput.value = data.localidade || "";
                addressInput.value = data.logradouro || "";
                neighborhood.value = data.bairro || "";
            })
            .catch((error) => {
                alert(error.message || "Erro ao consultar o CEP.");
            });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const criarDesc = document.getElementById("descriptionBtn");


    // Função para chamar a API do Magni e gerar a descrição da empresa
    async function gerarDescricaoEmpresa() {
        // Exibe o spinner e desabilita o botão
        criarDesc.innerHTML = '<i class="bi bi-arrow-repeat spinner-border spinner-border-sm"></i>'; // Exibe o spinner
        criarDesc.disabled = true;

        // Coleta os dados do formulário
        const companyName = document.getElementById("companyName").value;
        const cnpj = document.getElementById("cnpj").value;
        const email = document.getElementById("email").value;
        const site = document.getElementById("site").value;
        const phoneNumber = document.getElementById("phoneNumber").value;
        const country = document.getElementById("country").value;
        const state = document.getElementById("state").value;
        const city = document.getElementById("city").value;
        const zipcode = document.getElementById("zipcode").value;
        const address = document.getElementById("address").value;
        const addressNumber = document.getElementById("addressNumber").value;
        const neighborhood = document.getElementById("neighborhood").value;
        const descriptionAdditional = document.getElementById("Description").value;

        // Construção do prompt para enviar à API
        const prompt = `Gere uma descrição para uma empresa com os seguintes dados: 
                    Nome: ${companyName} 
                    CNPJ: ${cnpj}
                    Email: ${email}
                    Site: ${site}
                    Telefone: ${phoneNumber}
                    
                    Informações adicionais: ${descriptionAdditional}.
                    
                    A descrição deve ser clara, profissional, única e destacar os principais aspectos da empresa com no máximo 500 caracteres.`;


        try {
            // Chamada à API do Magni via fetch para o endpoint fornecido
            const response = await fetch('/pt/api/aiConnect', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Define o tipo de conteúdo como JSON
                },
                body: JSON.stringify({
                    prompt: prompt // Envia o prompt como parte do corpo da requisição
                })
            });

            const data = await response.json();
            if (data && data.response) {
                // Preencher o campo de descrição com a resposta da API
                document.getElementById("Description").value = data.response.trim();

                // Remove o spinner e habilita o botão novamente
                criarDesc.innerHTML = 'Gerar Descrição'; // Remove o spinner
                criarDesc.disabled = false;

            } else {
                alert("Erro ao gerar a descrição na opção desejada.");
            }
        } catch (error) {
            console.error("Erro ao chamar a API:", error);
            alert("Erro ao gerar a descrição não identificado.");
        }
    }

    // Associar o evento de clique ao botão
    document.getElementById("descriptionBtn").addEventListener("click", gerarDescricaoEmpresa);
});

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11) return false;
    let soma = 0;
    let resto;
    for (let i = 1; i <= 9; i++) {
        soma += parseInt(cpf.charAt(i - 1)) * (11 - i);
    }
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;

    soma = 0;
    for (let i = 1; i <= 10; i++) {
        soma += parseInt(cpf.charAt(i - 1)) * (12 - i);
    }
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;

    return true;
}

function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g, '');
    if (cnpj.length !== 14) return false;
    let soma = 0;
    let peso = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    for (let i = 0; i < 12; i++) {
        soma += parseInt(cnpj.charAt(i)) * peso[i + 1];
    }
    let resto = soma % 11;
    if (resto < 2) resto = 0;
    else resto = 11 - resto;
    if (resto !== parseInt(cnpj.charAt(12))) return false;

    soma = 0;
    for (let i = 0; i < 13; i++) {
        soma += parseInt(cnpj.charAt(i)) * peso[i];
    }
    resto = soma % 11;
    if (resto < 2) resto = 0;
    else resto = 11 - resto;
    if (resto !== parseInt(cnpj.charAt(13))) return false;

    return true;
}

function validarDocumento() {
    const cnpjInput = document.getElementById('cnpj').value.replace(/[^\d]+/g, '');
    const erroMensagem = document.getElementById('erroMensagem');
    const botaoBuscar = document.getElementById('buscarCNPJ');

    if (cnpjInput.length === 11) {
        if (!validarCPF(cnpjInput)) {
            erroMensagem.style.display = 'block';
            botaoBuscar.disabled = true;
            erroMensagem.textContent = 'Por favor, insira um CPF válido.';
        } else {
            erroMensagem.style.display = 'none';
            botaoBuscar.disabled = true;
        }
    } else if (cnpjInput.length === 14) {
        if (!validarCNPJ(cnpjInput)) {
            erroMensagem.style.display = 'block';
            botaoBuscar.disabled = true;
            erroMensagem.textContent = 'Por favor, insira um CNPJ válido.';
        } else {
            erroMensagem.style.display = 'none';
            botaoBuscar.disabled = false;
            botaoBuscar.title = "Clique para consultar o CNPJ";
        }
    } else {
        erroMensagem.style.display = 'block';
        botaoBuscar.disabled = true;
        erroMensagem.textContent = 'Por favor, insira um CPF ou CNPJ válido.';
    }
}
