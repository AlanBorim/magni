<?php
include __DIR__ . '/../../../../vendor/autoload.php';

use App\Core\Helpers\Helper;
use App\Core\LanguageDetector;
use App\Core\Security;
use App\Core\SessionManager;
use App\Core\ViewHelper;

$currentLanguage = LanguageDetector::detectLanguage()['language'];

Security::initializeSessionSecurity();

// Recupera informações do usuário
$role = SessionManager::get('roleName', 'guest'); // Se não houver, assume 'guest'
$twoFactorEnabled = SessionManager::get('two_factor_enabled', false);

$ip = Helper::getClientIP();
$pais = Helper::getCountryByIPv6($ip);
?>
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Empresa</title>
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/headers.php'); ?>

</head>

<body data-country="<?= strtolower($pais) ?>" data-lang="<?= $currentLanguage ?>">
    <?php ViewHelper::includeIfReadable(__DIR__ . '/../../../inc/menu.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">📅 Minha Agenda</h2>

    <div class="d-flex justify-content-end mb-3">
        <a href="/agenda/add" class="btn btn-success">➕ Novo Evento</a>
    </div>

    <div id="calendar"></div>
</div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        events: <?= json_encode($events) ?>,
        eventClick: function(info) {
            if (confirm("Deseja excluir este evento?")) {
                window.location.href = '/agenda/delete?id=' + info.event.id;
            }
        }
    });
    calendar.render();
});
</script>

<?php require_once __DIR__ . '/../../../inc/footer.php'; ?>
</body>

</html>