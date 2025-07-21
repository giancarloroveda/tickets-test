<?php

namespace App\Models;

use PDO;

class Task
{
    public static function all(PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM tasks");
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tasks as &$task) {
            $task['ticket'] = Ticket::find($db, $task['ticket_id']);
        }

        return $tasks;
    }

    public static function find(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($task) {
            $task['ticket'] = Ticket::find($db, $task['ticket_id']);
            return $task;
        }

        return null;
    }

    public static function findByTicketId(PDO $db, int $ticketId): array
    {
        $stmt = $db->prepare(
            "SELECT * FROM tasks WHERE ticket_id = :ticket_id"
        );
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO tasks (description, assignee_name, status, ticket_id) VALUES (:description, :assignee_name, :status, :ticket_id)"
        );
        $stmt->execute(
            [
                'description' => $data['description'],
                'assignee_name' => $data['assignee_name'],
                'status' => $data['status'],
                'ticket_id' => $data['ticket_id']
            ]
        );

        return (int)$db->lastInsertId();
    }

    public static function update(PDO $db, int $id, array $data): bool
    {
        $stmt = $db->prepare(
            "UPDATE tasks SET description = :description, assignee_name = :assignee_name, status = :status WHERE id = :id"
        );
        return $stmt->execute(
            [
                'id' => $id,
                'description' => $data['description'],
                'assignee_name' => $data['assignee_name'],
                'status' => $data['status']
            ]
        );
    }

    public static function delete(PDO $db, int $id): bool
    {
        $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
