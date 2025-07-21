<?php

namespace App\Models;

use PDO;

class Ticket
{
    public static function all(PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM tickets");
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tickets as &$ticket) {
            $ticket['tasks'] = Task::findByTicketId($db, $ticket['id']);
        }

        return $tickets;
    }

    public static function find(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        return $ticket ?: null;
    }

    public static function findWithTasks(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {
            $ticket['tasks'] = Task::findByTicketId($db, $ticket['id']);
            return $ticket;
        }

        return null;
    }

    public static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO tickets (title, description, status) VALUES (:title, :description, :status)"
        );
        $stmt->execute(
            [
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status']
            ]
        );

        return (int)$db->lastInsertId();
    }

    public static function update(PDO $db, int $id, array $data): bool
    {
        $stmt = $db->prepare(
            "UPDATE tickets SET title = :title, description = :description, status = :status WHERE id = :id"
        );
        return $stmt->execute(
            [
                'id' => $id,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status']
            ]
        );
    }

    public static function delete(PDO $db, int $id): bool
    {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM tasks WHERE ticket_id = :id"
        );
        $stmt->execute(['id' => $id]);

        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = $db->prepare("DELETE FROM tickets WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
