<?php

namespace App\Tests\Model;

use App\Model\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskModel(): void
    {
        $taskId = "123";
        $title = "Test Task";
        $description = "Test Description";
        $task_status = "pending";
        $dueDate = "2024-12-31";

        // Create a new Task object
        $task = new Task($taskId, $title, $description, $task_status, $dueDate);

        // Test getters
        $this->assertEquals($taskId, $task->getTaskId());
        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($description, $task->getDescription());
        $this->assertEquals($task_status, $task->getStatus());
        $this->assertEquals($dueDate, $task->getDueDate());

        // Test toArray method
        $expectedArray = [
            "taskId" => $taskId,
            "title" => $title,
            "description" => $description,
            "task_status" => $task_status,
            "dueDate" => $dueDate,
        ];

        $this->assertEquals($expectedArray, $task->toArray());
    }
}
