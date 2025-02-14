<?php

namespace App\Modules\Company;

use App\Core\Database;
use PDO;
use Exception;

class CompanyRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Insere uma nova empresa no banco de dados.
     *
     * @param string $name Nome da empresa
     * @param int $adminId ID do usuário administrador
     * @param string $slug Slug único da empresa
     * @return int Retorna o ID da empresa criada
     */
    public function insertCompany(array $data, array $files): int
    {
        // Diretório onde as logos serão armazenadas
        $uploadDir = __DIR__ . '/../../public/uploads/logos/';

        // Verifica se o diretório existe, senão cria
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Inicializa o caminho da logo como null
        $logoPath = null;

        // Verifica se há um arquivo sendo enviado
        if (!empty($files['logo']['name'])) {
            $file = $files['logo'];

            // Gera um nome de arquivo único baseado no timestamp
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'logo_' . time() . '_' . uniqid() . '.' . $fileExt;
            $filePath = $uploadDir . $newFileName;

            // Validações do arquivo (tipo e tamanho)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Formato de imagem inválido. Apenas JPG, PNG e GIF são permitidos.");
            }
            if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                throw new Exception("O tamanho da imagem excede o limite permitido de 2MB.");
            }

            // Move o arquivo para o diretório de uploads
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $logoPath = "/public/uploads/logos/" . $newFileName;
            } else {
                throw new Exception("Erro ao salvar a imagem no servidor.");
            }
        }

        // Query para inserir os dados no banco
        $stmt = $this->db->prepare("INSERT INTO company 
        (company_name, cnpj_cpf, email, site, phone_number, country, state, city, zipcode, address, address_number, neighborhood, logo, description, status, activity, activity_code, slug, admin_id) 
        VALUES (:company_name, :cnpj_cpf, :email, :site, :phone_number, :country, :state, :city, :zipcode, :address, :address_number, :neighborhood, :logo, :description, :status, :activity, :activity_code, :slug, :admin_id)
    ");

        $stmt->execute([
            ':company_name' => $data['companyName'],
            ':cnpj_cpf' => $data['cnpj'],
            ':email' => $data['email'],
            ':site' => $data['site'],
            ':phone_number' => $data['phoneNumber'],
            ':country' => $data['country'],
            ':state' => $data['state'],
            ':city' => $data['city'],
            ':zipcode' => $data['zipcode'],
            ':address' => $data['address'],
            ':address_number' => $data['addressNumber'],
            ':neighborhood' => $data['neighborhood'],
            ':logo' => $logoPath, // Salva o caminho correto da logo
            ':description' => $data['description'],
            ':status' => $data['status'],
            ':activity' => $data['atividade'],
            ':activity_code' => $data['atividadeCodigo'],
            ':slug' => $data['slug'],
            ':admin_id' => $data['user_id']
        ]);

        return $this->db->lastInsertId(); // Retorna o ID da empresa inserida
    }


    /**
     * Busca uma empresa pelo slug.
     *
     * @param string $slug Slug da empresa
     * @return array|null Retorna os dados da empresa ou null se não encontrada
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM companies WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);

        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        return $company ?: null;
    }

    /**
     * Atualiza as configurações de uma empresa.
     *
     * @param int $companyId ID da empresa
     * @param array $settings Configurações da empresa (logo, cores, layout)
     */
    public function updateSettings(int $companyId, array $settings): void
    {
        $stmt = $this->db->prepare("
            UPDATE company_settings 
            SET logo_path = :logo_path, theme_color = :theme_color, dashboard_layout = :dashboard_layout 
            WHERE company_id = :company_id
        ");
        $stmt->execute([
            'logo_path' => $settings['logo_path'] ?? null,
            'theme_color' => $settings['theme_color'] ?? null,
            'dashboard_layout' => json_encode($settings['dashboard_layout'] ?? []),
            'company_id' => $companyId
        ]);
    }

    /**
     * Cria a entrada de configurações padrão para uma empresa recém-criada.
     *
     * @param int $companyId ID da empresa
     */
    public function createDefaultSettings(int $companyId): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO company_settings (company_id, logo_path, theme_color, dashboard_layout) 
            VALUES (:company_id, NULL, '#ffffff', '[]')
        ");
        $stmt->execute(['company_id' => $companyId]);
    }

    public function getCompanies(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM company WHERE admin_id = :admin_id");
        $stmt->execute(['admin_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
