<?php

namespace App\Modules\Login;

use App\Core\LanguageDetector;
use App\Core\MessageHandler;
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
            MessageHandler::redirectWithMessage('danger', 'invalid_name', 'Nome inválido. Apenas letras e espaços são permitidos.', "/{$currentLanguage}/profile");
            return;
        }

        if (!empty($_REQUEST['phone']) && !filter_var($_REQUEST['phone'], FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^\+?[0-9\s()-]{8,15}$/"]])) {
            MessageHandler::redirectWithMessage('danger', 'invalid_phone', 'Telefone inválido.', "/{$currentLanguage}/profile");
            return;
        }

        if (!is_null($_SESSION['roleId'])) {
            if (!in_array($_SESSION['roleId'], $validRoleIds)) {
                MessageHandler::redirectWithMessage('danger', 'invalid_permissions', 'Permissão inválida.', "/{$currentLanguage}/profile");
                return;
            }
        }

        // Tenta criar um objeto DateTime com a data fornecida
        $dateObject = DateTime::createFromFormat($format, $_REQUEST['nascimento']);

        // Verifica se a data é válida
        if (!$dateObject || $dateObject->format($format) !== $_REQUEST['nascimento']) {
            MessageHandler::redirectWithMessage('danger', 'date_error', 'Data inválida', "/{$currentLanguage}/profile");
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
            MessageHandler::redirectWithMessage('success', 'update_profile_success', 'Profile atualizado com sucesso', "/{$currentLanguage}/profile");
            return;
            
        } else {
            MessageHandler::redirectWithMessage('danger', 'update_profile_error', 'Erro ao atualizar o perfil', "/{$currentLanguage}/profile");
            return;
        }
    }

    public function processUpdateProfilePass()
    {
        Security::enforceSessionSecurity();
        $errors = [
            'user_not_found',
            'password_not_found',
            'password_wrong_type',
            'password_failure'
        ];

        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        // Captura os dados do formulário
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Valida se as novas senhas coincidem
        if ($newPassword !== $confirmPassword) {
            MessageHandler::redirectWithMessage('danger', 'password_dont_match', 'As novas senhas não coincidem.',"/{$currentLanguage}/profile");
            return;
        }

        // Chama o método de atualização de senha
        $result = ProfileService::updatePassword($_SESSION['user_id'], $currentPassword, $newPassword);

        // Verifica o resultado e apresenta mensagens apropriadas
        if ($result['success'] == 'password_ok') {
            MessageHandler::redirectWithMessage('success', $result['success'], $result['message'],"/{$currentLanguage}/profile");
            return;
        } else {
            if (in_array($result['success'], $errors)) {
                MessageHandler::redirectWithMessage('danger', $result['success'], $result['message'],"/{$currentLanguage}/profile");
                return;
            }
        }
    }

    public function processUpdateProfilePic()
    {
        Security::enforceSessionSecurity();
        $errors = [
            'no_picture',
            'wrong_type',
            'wrong_size',
            'save_file_error',
            'save_error'
        ];

        $currentLanguage = LanguageDetector::detectLanguage()['language'];

        $result = ProfileService::updateProfilePicture($_SESSION['user_id'], $_FILES['profile_picture']);

        // Verifica o resultado e apresenta mensagens apropriadas
        if ($result['success'] == 'save_ok') {
            MessageHandler::redirectWithMessage('success', $result['success'], $result['message'],"/{$currentLanguage}/profile");
            return;
            
        } else {
            if (in_array($result['success'], $errors)) {
                MessageHandler::redirectWithMessage('danger', $result['success'], $result['message'],"/{$currentLanguage}/profile");
                return;
                
            }
        }
    }
}
