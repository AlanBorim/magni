<?php

namespace App\Modules\Company;

use App\Core\Database;
use App\Core\SessionManager;
use App\Core\Helpers\Helper;
use App\Modules\Company\CompanyRepository;
use PDO;

class CompanyService
{
    
    public function registerCompany($data,$file)
    {
        
        $data['slug'] = Helper::slugify($data['companyName']); // Gera um slug a partir do nome
        $data['user_id'] = SessionManager::get('user_id'); // Pega o ID do usuário logado
        // Verifica se o slug já existe, se sim, adiciona um número ao final
        $slug = $this->ensureUniqueSlug($data['slug']);
        
        $repo = new CompanyRepository();
        $repo->insertCompany($data,$file); // Insere a empresa no banco de dados

        return $slug; // Retorna o slug da empresa cadastrada
    }

    private function ensureUniqueSlug($slug)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM company WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);

        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $slug .= '-' . ($count + 1); // Adiciona um número ao final para evitar duplicatas
        }
        return $slug;
    }

    public function findBySlug($slug)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM company WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCompaniesByAdmin($adminId)
    {
        $repo = new CompanyRepository();
        return $repo->getCompanies($adminId); 

    }
}
