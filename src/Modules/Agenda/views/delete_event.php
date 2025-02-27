<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';

use App\Modules\Agenda\AgendaService;

$eventId = $_GET['id'] ?? null;

if ($eventId) {
    $agendaService = new AgendaService();
    $agendaService->removeEvent($eventId);
}

// Redireciona de volta à listagem
header("Location: /agenda/list");
exit;
