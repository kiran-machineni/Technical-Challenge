<?php

namespace App\Tests\Controller;

use App\Controller\TaskController;
use App\Model\Task;
use App\Service\TaskService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends MockeryTestCase
{
    private $taskServiceMock;
    private TaskController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Mockery mock for TaskService
        $this->taskServiceMock = Mockery::mock(TaskService::class);

        // Instantiate the TaskController with the mocked TaskService
        $this->controller = new TaskController($this->taskServiceMock);
    }

    public function testHello()
    {
        // Act
        $response = $this->controller->hello();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => 'PHP Task Manager App'], $content);
    }

    public function testListTasks()
    {
        // Arrange
        $expectedTasks = [
            [
                'taskId' => '1',
                'title' => 'Task 1',
                'description' => 'Description 1',
                'task_status' => 'Pending',
                'dueDate' => '2023-12-31',
            ],
            [
                'taskId' => '2',
                'title' => 'Task 2',
                'description' => 'Description 2',
                'task_status' => 'Completed',
                'dueDate' => '2023-12-31',
            ],
        ];

        $this->taskServiceMock->shouldReceive('listTasks')
            ->once()
            ->andReturn($expectedTasks);

        // Act
        $response = $this->controller->listTasks();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(['data' => $expectedTasks], $content);
    }

    public function testCreateTaskWithValidData()
    {
        // Arrange
        $data = [
            'title' => 'New Task',
            'description' => 'New Description',
            'dueDate' => '2023-12-31',
        ];
        $content = json_encode($data);
        $request = new Request([], [], [], [], [], [], $content);

        $this->taskServiceMock->shouldReceive('createTask')
            ->once()
            ->with(Mockery::on(function (Task $task) use ($data) {
                $this->assertEquals($data['title'], $task->getTitle());
                $this->assertEquals($data['description'], $task->getDescription());
                $this->assertEquals('pending', $task->getStatus());
                $this->assertEquals($data['dueDate'], $task->getDueDate());
                return true;
            }));

        // Act
        $response = $this->controller->createTask($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Task created', $content['message']);
        $this->assertArrayHasKey('taskId', $content);
        $this->assertNotEmpty($content['taskId']);
    }

    public function testCreateTaskWithInvalidData()
    {
        // Arrange
        $data = [
            // Missing 'title' field
            'description' => 'New Description',
            'dueDate' => '2023-12-31',
        ];
        $content = json_encode($data);
        $request = new Request([], [], [], [], [], [], $content);

        // Act
        $response = $this->controller->createTask($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Invalid input', $content['error']);
    }

    public function testCreateTaskWithInvalidJson()
    {
        // Arrange
        $content = 'invalid json';
        $request = new Request([], [], [], [], [], [], $content);

        // Act
        $response = $this->controller->createTask($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Invalid input', $content['error']);
    }

    public function testGetTaskWhenExists()
    {
        // Arrange
        $taskId = '1';
        $task = new Task($taskId, 'Task 1', 'Description 1', 'Pending', '2023-12-31');

        $this->taskServiceMock->shouldReceive('getTask')
            ->once()
            ->with($taskId)
            ->andReturn($task);

        // Act
        $response = $this->controller->getTask($taskId);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertEquals($task->toArray(), $content['data']);
    }

    public function testGetTaskWhenNotExists()
    {
        // Arrange
        $taskId = '999';

        $this->taskServiceMock->shouldReceive('getTask')
            ->once()
            ->with($taskId)
            ->andReturn(null);

        // Act
        $response = $this->controller->getTask($taskId);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Task not found', $content['error']);
    }

    public function testUpdateTaskWithValidData()
    {
        // Arrange
        $taskId = '1';
        $data = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'dueDate' => '2023-12-31',
            'task_status' => 'Completed',
        ];
        $content = json_encode($data);
        $request = new Request([], [], [], [], [], [], $content);

        $this->taskServiceMock->shouldReceive('updateTask')
            ->once()
            ->with(Mockery::on(function (Task $task) use ($taskId, $data) {
                $this->assertEquals($taskId, $task->getTaskId());
                $this->assertEquals($data['title'], $task->getTitle());
                $this->assertEquals($data['description'], $task->getDescription());
                $this->assertEquals($data['dueDate'], $task->getDueDate());
                $this->assertEquals($data['task_status'], $task->getStatus());
                return true;
            }));

        // Act
        $response = $this->controller->updateTask($taskId, $request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Task updated', $content['message']);
    }

    public function testUpdateTaskWithInvalidData()
    {
        // Arrange
        $taskId = '1';
        $content = 'invalid json';
        $request = new Request([], [], [], [], [], [], $content);

        // Act
        $response = $this->controller->updateTask($taskId, $request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $content);
        $this->assertEquals('Invalid input', $content['error']);
    }

    public function testDeleteTask()
    {
        // Arrange
        $taskId = '1';

        $this->taskServiceMock->shouldReceive('deleteTask')
            ->once()
            ->with($taskId);

        // Act
        $response = $this->controller->deleteTask($taskId);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Task deleted', $content['message']);
    }

    public function testCreateTaskWithNonStringTitle()
    {
        // Arrange
        $data = [
            'title' => ['Not a string'],
            'description' => 'New Description',
            'dueDate' => '2023-12-31',
        ];
        $content = json_encode($data);
        $request = new Request([], [], [], [], [], [], $content);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected string, got array');

        // Act
        $this->controller->createTask($request);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
