<?php

namespace App\Modules\Company;

class CompanyService
{
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function createCompany(string $name, int $adminId, string $slug): int
    {
        return $this->companyRepository->insertCompany($name, $adminId, $slug);
    }

    public function getCompanyBySlug(string $slug)
    {
        return $this->companyRepository->findBySlug($slug);
    }

    public function updateCompanySettings(int $companyId, array $settings): void
    {
        $this->companyRepository->updateSettings($companyId, $settings);
    }

    public function generateCompanySlug(string $name): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($name)));
    }
}
