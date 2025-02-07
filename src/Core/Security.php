<?php

namespace App\Core;

use App\Core\SessionManager;

class Security
{

    public static function initializeSessionSecurity(): void
    {
   
        // Verifica se o usuário está autenticado
        if (!SessionManager::get('user_id')) {
            header("Location: /");
            exit;
        }
    }
}
