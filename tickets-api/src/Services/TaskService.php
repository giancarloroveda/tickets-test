<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use PDO;

class TaskService
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function getAll()
    {
        return Task::all($this->db);
    }

    public function getByIdWithTicket($id)
    {
        return Task::find($this->db, $id);
    }

    public function create(array $data)
    {
        $data['status'] = TaskStatus::PENDING->value;
        return Task::create($this->db, $data);
    }

    public function update($id, array $data)
    {
        return Task::update($this->db, $id, $data);
    }

    public function delete($id)
    {
        $task = Task::find($this->db, $id);

        if (!$task) {
            return false;
        }

        Task::delete($this->db, $id);

        return $task;
    }

    public function getByTicketId(int $ticketId)
    {
        return Task::findByTicketId($this->db, $ticketId);
    }
}
