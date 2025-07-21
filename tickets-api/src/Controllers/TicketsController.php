<?php

namespace App\Controllers;


use App\Enums\TaskStatus;
use App\Enums\TicketStatus;
use App\Http\JsonResponse;
use App\Services\TaskService;
use App\Services\TicketService;
use App\Utils\Validator;

class TicketsController
{
    public function __construct(
        private readonly TicketService $ticketService,
        private readonly TaskService   $taskService
    ) {
    }

    public function index()
    {
        $tickets = $this->ticketService->getAll();
        JsonResponse::success($tickets);
    }

    public function show($id)
    {
        $ticket = $this->ticketService->getByIdWithTasks($id);
        if (!$ticket) {
            JsonResponse::error("Ticket not found", 404);
        }
        JsonResponse::success($ticket);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $missing = Validator::requireFields(
            $data,
            ['title', 'description']
        );
        if ($missing) {
            JsonResponse::error(
                "Missing required fields: " . implode(', ', $missing),
                422
            );
        }

        $created = $this->ticketService->create($data);
        JsonResponse::success($created, "Ticket created", 201);
    }

    public function update(int $id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $missing = Validator::requireFields(
            $data,
            ['title', 'description', 'status']
        );
        if ($missing) {
            JsonResponse::error(
                "Missing required fields: " . implode(', ', $missing),
                422
            );
        }

        if (!Validator::isOneOf(
            $data['status'],
            ['open', 'in_progress', 'closed']
        )) {
            JsonResponse::error(
                "Status must be 'open', 'in_progress' or 'closed'",
                422
            );
        }

        if ($data['status'] == TicketStatus::CLOSED->value) {
            $tasks = $this->taskService->getByTicketId($id);

            $canFinish = true;

            foreach ($tasks as $task) {
                if ($task['status'] !== TaskStatus::DONE->value) {
                    $canFinish = false;
                    break;
                }
            }

            if (!$canFinish) {
                JsonResponse::error(
                    "Ticket cannot be closed until all tasks are done",
                    422
                );
            }
        }


        $updated = $this->ticketService->update($id, $data);
        if (!$updated) {
            JsonResponse::error("Ticket not found or update failed", 404);
        } else {
            JsonResponse::success($updated, "Ticket updated");
        }
    }

    public function destroy($id)
    {
        $deleted = $this->ticketService->delete($id);
        if (!$deleted) {
            JsonResponse::error("Ticket not found or delete failed", 404);
        } else {
            JsonResponse::success($deleted, "Ticket deleted");
        }
    }
}

