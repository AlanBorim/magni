<?php

namespace App\Modules\Agenda;

use App\Core\Database;
use PDO;

class Agenda
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllEvents(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM agenda_events WHERE user_id = :user_id ORDER BY start_time ASC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEvent(array $eventData): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO agenda_events (user_id, title, description, start_time, end_time, category, color) 
            VALUES (:user_id, :title, :description, :start_time, :end_time, :category, :color)"
        );
        return $stmt->execute($eventData);
    }

    public function deleteEvent(int $eventId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM agenda_events WHERE id = :id");
        return $stmt->execute(['id' => $eventId]);
    }
}
