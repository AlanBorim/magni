<?php

use App\Modules\General\GeneralController;

// Definição das rotas para o módulo Login
$router->get('/sessionTime', [GeneralController::class, 'getSessionTime']); 