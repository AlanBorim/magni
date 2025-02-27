<?php

use App\Modules\Agenda\AgendaController;

$router->get('/agenda/calendar', [AgendaController::class, 'showCalendar']);
$router->get('/agenda/delete', [AgendaController::class, 'deleteEvent']);

$router->post('/agenda/add', [AgendaController::class, 'addEvent']);