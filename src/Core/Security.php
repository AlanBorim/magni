<?php

namespace App\Core;

use App\Core\SessionManager;
use App\Modules\Login\LoginService;
use RobThree\Auth\TwoFactorAuth;

class Security
{

    public static function initializeSessionSecurity(): void
    {
   
        // Verifica se o usuário está autenticado
        if (!SessionManager::get('user_id')) {
            MessageHandler::redirectWithMessage('danger','not_logged', 'Você precisa estar logado para executar essa ação.', '/');
            exit;
        }
    }

    /**
     * Inicia a validação de 2FA para o usuário logado.
     *
     * @param string $userId ID do usuário logado.
     * @param string|null $twoFactorCode Código enviado pelo usuário.
     */
    public static function startTwoFactorValidation(string $userId, ?string $twoFactorCode = null): void
    {
        self::initializeSessionSecurity();

        if ($userId != SessionManager::get('user_id'))  {
            MessageHandler::redirectWithMessage('danger', 'start_2fa_error', 'Ocorreu um erro no start 2fa','/');
        }

        if ($twoFactorCode) {
            SessionManager::set('two_factor_code', $twoFactorCode);
            SessionManager::set('two_factor_start_time', time()); // Marca o início da validação
        } else {
            SessionManager::set('two_factor_validated', true); // Permite acesso direto se 2FA não estiver habilitado
        }
    }

    /**
     * Verifica o código de 2FA fornecido pelo usuário.
     *
     * @param string $userInputCode Código digitado pelo usuário.
     * @return bool Retorna true se a validação for bem-sucedida.
     */
    public static function validateTwoFactorCode(string $userInputCode): bool
    {
        self::initializeSessionSecurity();

        $tfa = new TwoFactorAuth();
        $user = new LoginService();
        $userData = $user->findById(SessionManager::get('user_id'));

        $twoFactorSecret = $userData['two_factor_secret'];

        if ($tfa->verifyCode($twoFactorSecret, $userInputCode)) {
            SessionManager::set('two_factor_validated',true);
            SessionManager::remove('two_factor_secret');
            SessionManager::remove('two_factor_start_time');
            return true;
        }

        return false;
    }

}
