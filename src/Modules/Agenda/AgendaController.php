<?php

namespace App\Modules\Agenda;

use App\Modules\Agenda\AgendaService;

use App\Core\SessionManager;

class AgendaController
{
    private AgendaService $agendaService;

    public function __construct()
    {
        $this->agendaService = new AgendaService();
    }

    public function showCalendar()
    {
        $userId = SessionManager::get('user_id');
        $events = $this->agendaService->listEvents($userId);
        include __DIR__ . '/views/calendar.php';
    }

    public function addEvent()
    {
        $userId = SessionManager::get('user_id');

        $eventData = [
            'user_id' => $userId,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'category' => $_POST['category'],
            'color' => $_POST['color'] ?? '#007bff'
        ];

        $this->agendaService->addEvent($eventData);
        header("Location: /agenda/calendar");
    }

    public function deleteEvent()
    {
        $eventId = $_GET['id'] ?? 0;
        $this->agendaService->removeEvent($eventId);
        header("Location: /agenda/calendar");
    }
}
