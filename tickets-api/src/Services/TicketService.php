<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use PDO;

class TicketService
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    public function getAll()
    {
        return Ticket::all($this->db);
    }

    public function getByIdWithTasks($id)
    {
        return Ticket::findWithTasks($this->db, $id);
    }

    public function create(array $data)
    {
        $data['status'] = TicketStatus::OPEN->value;
        return Ticket::create($this->db, $data);
    }

    public function update($id, array $data)
    {
        return Ticket::update($this->db, $id, $data);
    }

    public function delete($id)
    {
        $ticket = Ticket::find($this->db, $id);

        if (!$ticket) {
            return false;
        }

        $deleted = Ticket::delete($this->db, $id);

        if (!$deleted) {
            return false;
        }

        return $ticket;
    }
}
