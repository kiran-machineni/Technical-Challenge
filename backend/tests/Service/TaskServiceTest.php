<?php

namespace App\Tests\Service;

use App\Model\Task;
use App\Service\TaskService;
use Aws\DynamoDb\DynamoDbClient;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

class TaskServiceTest extends MockeryTestCase
{
    private MockInterface $dynamoDbMock;
    private TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Mockery mock for DynamoDbClient
        $this->dynamoDbMock = Mockery::mock(DynamoDbClient::class);

        // Instantiate the TaskService
        $this->taskService = new TaskService();

        // Inject the mock into the TaskService using Reflection
        $reflection = new \ReflectionClass($this->taskService);
        $property = $reflection->getProperty('dynamoDb');
        $property->setAccessible(true);
        $property->setValue($this->taskService, $this->dynamoDbMock);
    }

    public function testListTasks()
    {
        // Arrange
        $expectedItems = [
            [
                'taskId' => ['S' => '1'],
                'title' => ['S' => 'Task 1'],
                'description' => ['S' => 'Description 1'],
                'task_status' => ['S' => 'Pending'],
                'dueDate' => ['S' => '2023-12-31'],
            ],
        ];

        $this->dynamoDbMock->shouldReceive('scan')
            ->once()
            ->with(['TableName' => 'Tasks'])
            ->andReturn(['Items' => $expectedItems]);

        // Act
        $tasks = $this->taskService->listTasks();

        // Assert
        $this->assertNotEmpty($tasks);
        $this->assertEquals('1', $tasks[0]['taskId']);
        $this->assertEquals('Task 1', $tasks[0]['title']);
        $this->assertEquals('Description 1', $tasks[0]['description']);
        $this->assertEquals('Pending', $tasks[0]['task_status']);
        $this->assertEquals('2023-12-31', $tasks[0]['dueDate']);
    }

    public function testCreateTask()
    {
        // Arrange
        $task = new Task('1', 'Task 1', 'Description 1', 'Pending', '2023-12-31');

        $this->dynamoDbMock->shouldReceive('putItem')
            ->once()
            ->with([
                'TableName' => 'Tasks',
                'Item' => [
                    'taskId' => ['S' => '1'],
                    'title' => ['S' => 'Task 1'],
                    'description' => ['S' => 'Description 1'],
                    'task_status' => ['S' => 'Pending'],
                    'dueDate' => ['S' => '2023-12-31'],
                ],
            ]);

        // Act
        $this->taskService->createTask($task);

        // Assert
        $this->assertTrue(true);
    }

    public function testGetTaskWhenExists()
    {
        // Arrange
        $taskId = '1';
        $expectedItem = [
            'taskId' => ['S' => '1'],
            'title' => ['S' => 'Task 1'],
            'description' => ['S' => 'Description 1'],
            'task_status' => ['S' => 'Pending'],
            'dueDate' => ['S' => '2023-12-31'],
        ];

        $this->dynamoDbMock->shouldReceive('getItem')
            ->once()
            ->with([
                'TableName' => 'Tasks',
                'Key' => ['taskId' => ['S' => $taskId]],
            ])
            ->andReturn(['Item' => $expectedItem]);

        // Act
        $task = $this->taskService->getTask($taskId);

        // Assert
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('1', $task->getTaskId());
        $this->assertEquals('Task 1', $task->getTitle());
        $this->assertEquals('Description 1', $task->getDescription());
        $this->assertEquals('Pending', $task->getStatus());
        $this->assertEquals('2023-12-31', $task->getDueDate());
    }

    public function testGetTaskWhenNotExists()
    {
        // Arrange
        $taskId = '999';

        $this->dynamoDbMock->shouldReceive('getItem')
            ->once()
            ->with([
                'TableName' => 'Tasks',
                'Key' => ['taskId' => ['S' => $taskId]],
            ])
            ->andReturn([]);

        // Act
        $task = $this->taskService->getTask($taskId);

        // Assert
        $this->assertNull($task);
    }

    public function testUpdateTask()
    {
        // Arrange
        $task = new Task('1', 'Updated Task', 'Updated Description', 'Completed', '2023-12-31');

        $this->dynamoDbMock->shouldReceive('updateItem')
            ->once()
            ->with([
                'TableName' => 'Tasks',
                'Key' => ['taskId' => ['S' => '1']],
                'UpdateExpression' => 'SET title = :title, description = :description, task_status = :task_status, dueDate = :dueDate',
                'ExpressionAttributeValues' => [
                    ':title' => ['S' => 'Updated Task'],
                    ':description' => ['S' => 'Updated Description'],
                    ':task_status' => ['S' => 'Completed'],
                    ':dueDate' => ['S' => '2023-12-31'],
                ],
            ]);

        // Act
        $this->taskService->updateTask($task);

        // Assert
        $this->assertTrue(true);
    }

    public function testDeleteTask()
    {
        // Arrange
        $taskId = '1';

        $this->dynamoDbMock->shouldReceive('deleteItem')
            ->once()
            ->with([
                'TableName' => 'Tasks',
                'Key' => ['taskId' => ['S' => $taskId]],
            ]);

        // Act
        $this->taskService->deleteTask($taskId);

        // Assert
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
