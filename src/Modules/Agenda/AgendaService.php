<?php

namespace App\Modules\Agenda;

use App\Modules\Agenda\Agenda;

class AgendaService
{
    private Agenda $agenda;

    public function __construct()
    {
        $this->agenda = new Agenda();
    }

    public function listEvents(int $userId): array
    {
        return $this->agenda->getAllEvents($userId);
    }

    public function addEvent(array $eventData): bool
    {
        return $this->agenda->createEvent($eventData);
    }

    public function removeEvent(int $eventId): bool
    {
        return $this->agenda->deleteEvent($eventId);
    }
}
