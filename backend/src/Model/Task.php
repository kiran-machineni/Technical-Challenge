<?php

namespace App\Model;

class Task
{
    private string $taskId;
    private string $title;
    private string $description;
    private string $task_status;
    private string $dueDate;

    public function __construct(string $taskId, string $title, string $description, string $task_status, string $dueDate)
    {
        $this->taskId = $taskId;
        $this->title = $title;
        $this->description = $description;
        $this->task_status = $task_status;
        $this->dueDate = $dueDate;
    }

    public function getTaskId(): string
    {
        return $this->taskId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->task_status;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            "taskId" => $this->taskId,
            "title" => $this->title,
            "description" => $this->description,
            "task_status" => $this->task_status,
            "dueDate" => $this->dueDate,
        ];
    }
}
