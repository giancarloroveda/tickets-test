<?php

namespace App\Controllers;

use App\Http\JsonResponse;
use App\Services\TaskService;
use App\Utils\Validator;

class TasksController
{
    public function __construct(private readonly TaskService $taskService)
    {
    }

    public function index()
    {
        $tasks = $this->taskService->getAll();
        JsonResponse::success($tasks);
    }

    public function tasksByTicket($ticketId)
    {
        $tasks = $this->taskService->getByTicketId($ticketId);
        JsonResponse::success($tasks);
    }

    public function show($id)
    {
        $task = $this->taskService->getByIdWithTicket($id);
        if (!$task) {
            JsonResponse::error("Task not found", 404);
        } else {
            JsonResponse::success($task);
        }
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $missing = Validator::requireFields(
            $data,
            [
                'description',
                'assignee_name',
                'ticket_id'
            ]
        );
        if ($missing) {
            JsonResponse::error(
                "Missing required fields: " . implode(', ', $missing),
                422
            );
        }

        $created = $this->taskService->create($data);
        JsonResponse::success($created, "Task created", 201);
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $missing = Validator::requireFields(
            $data,
            [
                'description',
                'assignee_name',
                'status',
            ]
        );
        if ($missing) {
            JsonResponse::error(
                "Missing required fields: " . implode(', ', $missing),
                422
            );
        }

        if (!Validator::isOneOf(
            $data['status'],
            ['pending', 'in_progress', 'done']
        )) {
            JsonResponse::error(
                "Status must be 'pending', 'in_progress' or 'done'",
                422
            );
        }

        $updated = $this->taskService->update($id, $data);
        if (!$updated) {
            JsonResponse::error("Task not found or update failed", 404);
        } else {
            JsonResponse::success($updated, "Task updated");
        }
    }

    public function destroy($id)
    {
        $deleted = $this->taskService->delete($id);
        if (!$deleted) {
            JsonResponse::error("Task not found or delete failed", 404);
        } else {
            JsonResponse::success(null, "Task deleted");
        }
    }
}
