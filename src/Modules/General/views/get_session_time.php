<?php

include __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\SessionManager;

// Retorna o tempo restante da sessão em formato JSON
echo json_encode([
    'remainingTime' => SessionManager::getRemainingSessionTime()
]);
?>