<?php

namespace App\Controller;

use App\Model\Task;
use App\Service\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route("/", methods: ["GET"])]
    public function hello(): JsonResponse
    {
        return new JsonResponse(
            ["message" => "PHP Task Manager App"],
            200,
            ["Content-Type" => "application/json"]
        );
    }

    #[Route("/tasks", methods: ["GET"])]
    public function listTasks(): JsonResponse
    {
        $tasks = $this->taskService->listTasks();
        return new JsonResponse(["data" => $tasks], 200);
    }

    #[Route("/tasks", methods: ["POST"])]
    public function createTask(Request $request): JsonResponse
    {
        /** @var array<string, mixed>|null $data */
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data["title"], $data["description"], $data["dueDate"])) {
            return new JsonResponse(["error" => "Invalid input"], 400);
        }

        $title = $this->validateString($data["title"]);
        $description = $this->validateString($data["description"]);
        $dueDate = $this->validateString($data["dueDate"]);
        $taskStatus = isset($data["task_status"]) ? $this->validateString($data["task_status"]) : "pending";

        $task = new Task(
            uniqid(),
            $title,
            $description,
            $taskStatus,
            $dueDate
        );

        $this->taskService->createTask($task);

        return new JsonResponse(["message" => "Task created", "taskId" => $task->getTaskId()], 201);
    }

    #[Route("/tasks/{taskId}", methods: ["GET"])]
    public function getTask(string $taskId): JsonResponse
    {
        $task = $this->taskService->getTask($taskId);

        if (!$task) {
            return new JsonResponse(["error" => "Task not found"], 404);
        }
        return new JsonResponse(["data" => $task->toArray()], 200);
    }

    #[Route("/tasks/{taskId}", methods: ["PUT"])]
    public function updateTask(string $taskId, Request $request): JsonResponse
    {
        /** @var array<string, mixed>|null $data */
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(["error" => "Invalid input"], 400);
        }

        $title = isset($data["title"]) ? $this->validateString($data["title"]) : "";
        $description = isset($data["description"]) ? $this->validateString($data["description"]) : "";
        $taskStatus = isset($data["task_status"]) ? $this->validateString($data["task_status"]) : "pending";
        $dueDate = isset($data["dueDate"]) ? $this->validateString($data["dueDate"]) : "";

        $task = new Task(
            $taskId,
            $title,
            $description,
            $taskStatus,
            $dueDate
        );

        $this->taskService->updateTask($task);

        return new JsonResponse(["message" => "Task updated"], 200);
    }

    #[Route("/tasks/{taskId}", methods: ["DELETE"])]
    public function deleteTask(string $taskId): JsonResponse
    {
        $this->taskService->deleteTask($taskId);
        return new JsonResponse(["message" => "Task deleted"], 200);
    }

    /**
     * Validates and returns a string.
     *
     * @param mixed $value The value to validate
     * @return string
     * @throws \InvalidArgumentException If the value is not a string
     */
    private function validateString($value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException("Expected string, got " . gettype($value));
        }
        return $value;
    }
}
