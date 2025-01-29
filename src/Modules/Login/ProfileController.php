<?php

namespace App\Modules\Login;

use App\Core\FlashMessages;
use App\Core\LanguageDetector;
use App\Core\Security;
use App\Core\Roles;
use App\Modules\Login\ProfileService;


use DateTime;

class ProfileController
{
    public function processUpdateProfile()
    {
        Security::initializeSessionSecurity();
        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        $rolesData = Roles::getRoles();
        $validRoleIds = array_column($rolesData, 'id');
        // Define o formato esperado da data
        $format = 'Y-m-d';

        if (empty($_REQUEST['name']) || !preg_match("/^[\p{L}\s]+$/u", $_REQUEST['name'])) {
            FlashMessages::setFlash('danger', 'invalid_name', 'Nome inválido. Apenas letras e espaços são permitidos.');
            header("Location: /{$currentLanguage}/profile");
            return;
        }

        if (!empty($_REQUEST['phone']) && !filter_var($_REQUEST['phone'], FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^\+?[0-9\s()-]{8,15}$/"]])) {
            FlashMessages::setFlash('danger', 'invalid_phone', 'Telefone inválido.');
            header("Location: /{$currentLanguage}/profile");
            return;
        }

        if (!is_null($_SESSION['roleId'])) {
            if (!in_array($_SESSION['roleId'], $validRoleIds)) {
                FlashMessages::setFlash('danger', 'invalid_permissions', 'Permissão inválida.');
                header("Location: /{$currentLanguage}/profile");
                return;
            }
        }

        // Tenta criar um objeto DateTime com a data fornecida
        $dateObject = DateTime::createFromFormat($format, $_REQUEST['nascimento']);

        // Verifica se a data é válida
        if (!$dateObject || $dateObject->format($format) !== $_REQUEST['nascimento']) {
            FlashMessages::setFlash('danger', 'date_error', 'Data inválida');
            header("Location: /{$currentLanguage}/profile");
            return;
        }

        $userUpdate = ProfileService::updateProfile(
            $_REQUEST['name'],
            $_REQUEST['phone'],
            $_REQUEST['nascimento'],
            $_SESSION['user_id'],
            $_REQUEST['role'] ?? $_SESSION['roleId'],
            $_SESSION['role'] == 'admin' ? $_SESSION['roleId'] : null
        );
        if ($userUpdate) {
            FlashMessages::setFlash('success', 'update_profile_success', 'Profile atualizado com sucesso');
            header("Location: /{$currentLanguage}/profile");
            return;
            
        } else {
            FlashMessages::setFlash('danger', 'update_profile_error', 'Erro ao atualizar o perfil');
            header("Location: /{$currentLanguage}/profile");
            return;
            
        }
    }

    public function processUpdateProfilePic()
    {
        echo "atualiza foto de perfil";
    }

    public function processUpdateProfilePass()
    {
        echo "atualiza Senha";
    }
}
