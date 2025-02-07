<?php

namespace App\Modules\Company;

use App\Core\Database;
use App\Core\Helpers\Helper;

use PDO;

class CompanyService
{

    
    public function registerCompany($data)
    {
        $db = Database::getInstance();
        $slug = Helper::slugify($data['companyName']); // Gera um slug a partir do nome

        // Verifica se o slug já existe, se sim, adiciona um número ao final
        $slug = $this->ensureUniqueSlug($slug);

        $stmt = $db->prepare("
            INSERT INTO companies (name, slug, email, phone, website, country, state, city, address, logo, description, created_by)
            VALUES (:name, :slug, :email, :phone, :website, :country, :state, :city, :address, :logo, :description, :created_by)
        ");
        $stmt->execute([
            ':name' => $data['companyName'],
            ':slug' => $slug,
            ':email' => $data['email'],
            ':phone' => $data['phoneNumber'],
            ':website' => $data['site'],
            ':country' => $data['country'],
            ':state' => $data['state'],
            ':city' => $data['city'],
            ':address' => $data['address'],
            ':logo' => $data['logo'],
            ':description' => $data['description'],
            ':created_by' => $_SESSION['user_id']
        ]);

        return $slug; // Retorna o slug da empresa cadastrada
    }

    private function ensureUniqueSlug($slug)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM companies WHERE slug = :slug");
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
        $stmt = $db->prepare("SELECT * FROM companies WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
