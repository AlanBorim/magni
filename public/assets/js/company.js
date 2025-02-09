document.addEventListener("DOMContentLoaded", function () {
    // Obtendo o valor de data-country
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

    buscarCNPJButton.addEventListener("click", function () {
        let cnpj = cnpjInput.value.trim().replace(/\D/g, ""); // Remove caracteres não numéricos

        // Validação do CNPJ
        if (!/^\d{14}$/.test(cnpj)) {
            alert("Por favor, insira um CNPJ válido (14 números).");
            return;
        }

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
            })
            .catch((error) => {
                alert(error.message || "Erro ao consultar o CNPJ.");
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
    // Função para chamar a API do Magni e gerar a descrição da empresa
    async function gerarDescricaoEmpresa() {
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
                    
                    A descrição deve ser clara, profissional e destacar os principais aspectos da empresa com no máximo 500 caracteres.`;

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