<?php

namespace App\Service;

use App\Model\Task;
use Aws\DynamoDb\DynamoDbClient;

class TaskService
{
    private DynamoDbClient $dynamoDb;
    private string $tableName = "Tasks";

    public function __construct()
    {
        $this->dynamoDb = new DynamoDbClient([
            "region" => "us-east-1",
            "version" => "latest",
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function listTasks(): array
    {
        /** @var \Aws\Result<array<string, mixed>> $result */
        $result = $this->dynamoDb->scan(["TableName" => $this->tableName]);

        /** @var array<int, array<string, array<string, string>>> $items */
        $items = $result['Items'] ?? [];

        $tasks = [];
        foreach ($items as $item) {
            /** @var array<string, array<string, string>> $item */
            $tasks[] = new Task(
                $item['taskId']['S'] ?? '',
                $item['title']['S'] ?? '',
                $item['description']['S'] ?? '',
                $item['task_status']['S'] ?? '',
                $item['dueDate']['S'] ?? ''
            );
        }

        return array_map(fn(Task $task): array => $task->toArray(), $tasks);
    }

    public function createTask(Task $task): void
    {
        $this->dynamoDb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'taskId' => ['S' => $task->getTaskId()],
                'title' => ['S' => $task->getTitle()],
                'description' => ['S' => $task->getDescription()],
                'task_status' => ['S' => $task->getStatus()],
                'dueDate' => ['S' => $task->getDueDate()],
            ],
        ]);
    }

    public function getTask(string $taskId): ?Task
    {
        /** @var \Aws\Result<array<string, mixed>> $result */
        $result = $this->dynamoDb->getItem([
            'TableName' => $this->tableName,
            'Key' => ['taskId' => ['S' => $taskId]],
        ]);

        /** @var array<string, array<string, string>>|null $item */
        $item = $result['Item'] ?? null;

        if (empty($item)) {
            return null;
        }

        return new Task(
            $item['taskId']['S'] ?? '',
            $item['title']['S'] ?? '',
            $item['description']['S'] ?? '',
            $item['task_status']['S'] ?? '',
            $item['dueDate']['S'] ?? ''
        );
    }

    public function updateTask(Task $task): void
    {
        $this->dynamoDb->updateItem([
            'TableName' => $this->tableName,
            'Key' => ['taskId' => ['S' => $task->getTaskId()]],
            'UpdateExpression' => 'SET title = :title, description = :description, task_status = :task_status, dueDate = :dueDate',
            'ExpressionAttributeValues' => [
                ':title' => ['S' => $task->getTitle()],
                ':description' => ['S' => $task->getDescription()],
                ':task_status' => ['S' => $task->getStatus()],
                ':dueDate' => ['S' => $task->getDueDate()],
            ],
        ]);
    }

    public function deleteTask(string $taskId): void
    {
        $this->dynamoDb->deleteItem([
            'TableName' => $this->tableName,
            'Key' => ['taskId' => ['S' => $taskId]],
        ]);
    }
}
