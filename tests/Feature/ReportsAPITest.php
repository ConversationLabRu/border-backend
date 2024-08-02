<?php

namespace Tests\Feature;

use App\Http\directions\borderCrossings\cameras\Services\CameraService;
use App\Http\directions\borderCrossings\reports\Controllers\ReportController;
use App\Http\directions\borderCrossings\reports\Entities\Report;
use App\Http\directions\borderCrossings\reports\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class ReportsAPITest extends TestCase
{
    /**
     * Тест на успешное получение данных
     */
    public function test_get_last_report(): void
    {
        $mockBorderCrossingService = Mockery::mock(ReportService::class);

        $json = '[
            {
                "id": 13,
                "border_crossing_id": 1,
                "transport_id": 1,
                "user_id": 1,
                "checkpoint_queue": "2024-07-29 10:00:00",
                "checkpoint_entry": "2024-07-29 10:05:00",
                "checkpoint_exit": "2024-07-29 10:15:00",
                "comment": "Sample comment"
            },
            {
                "id": 1,
                "border_crossing_id": 1,
                "transport_id": 1,
                "user_id": 1,
                "checkpoint_queue": "2024-07-01 08:00:00",
                "checkpoint_entry": "2024-07-01 09:00:00",
                "checkpoint_exit": "2024-07-01 10:00:00",
                "comment": "Sample comment 1"
            }
        ]';

        // Преобразование JSON в ассоциативный массив PHP
        $mockData = json_decode($json, true);

        // Set up the expectation for the mock
        $mockBorderCrossingService
            ->shouldReceive('getLastReportByBorderCrossing')
            ->once()
            ->andReturn($mockData);

        // Bind the mock to the container
        $this->app->instance(ReportService::class, $mockBorderCrossingService);

        // Send a GET request to the endpoint
        $response = $this->get('/api/directions/borderCrossing/reports/last?borderCrossingId=1');

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response data matches the mock data
        $response->assertJson($mockData);

        $this->assertLessThan(7, $this->count($response->json()));
    }

    /**
     * Тест на не успешное получение данных без GET параметра
     */
    public function test_get_last_report_without_get_parametr(): void
    {
        // Отправка GET запроса без параметра
        $response = $this->get('/api/directions/borderCrossing/reports/last');

        // Проверка, что статус ответа 400
        $response->assertStatus(400);

        // Проверка, что ответ содержит правильное сообщение
        $response->assertJson([
            'message' => 'Не передан borderCrossingId'
        ]);
    }

    /**
     * Тест на успешное получение данных всех отчетов
     */
    public function test_get_all_report(): void
    {
        $mockBorderCrossingService = Mockery::mock(ReportService::class);

        $json = '[
            {
                "id": 13,
                "border_crossing_id": 1,
                "transport_id": 1,
                "user_id": 1,
                "checkpoint_queue": "2024-07-29 10:00:00",
                "checkpoint_entry": "2024-07-29 10:05:00",
                "checkpoint_exit": "2024-07-29 10:15:00",
                "comment": "Sample comment"
            },
            {
                "id": 1,
                "border_crossing_id": 1,
                "transport_id": 1,
                "user_id": 1,
                "checkpoint_queue": "2024-07-01 08:00:00",
                "checkpoint_entry": "2024-07-01 09:00:00",
                "checkpoint_exit": "2024-07-01 10:00:00",
                "comment": "Sample comment 1"
            }
        ]';

        $mockData = json_decode($json, true);

        // Set up the expectation for the mock
        $mockBorderCrossingService
            ->shouldReceive('getAllReportByBorderCrossing')
            ->once()
            ->andReturn($mockData);

        // Bind the mock to the container
        $this->app->instance(ReportService::class, $mockBorderCrossingService);

        // Send a GET request to the endpoint
        $response = $this->get('/api/directions/borderCrossing/reports?borderCrossingId=1');

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response data matches the mock data
        $response->assertJson($mockData);
    }

    /**
     * Тест на не успешное получение данных без GET параметра в методе получения всех отчетов
     */
    public function test_get_all_report_without_get_parametr(): void
    {
        // Отправка GET запроса без параметра
        $response = $this->get('/api/directions/borderCrossing/reports');

        // Проверка, что статус ответа 400
        $response->assertStatus(400);

        // Проверка, что ответ содержит правильное сообщение
        $response->assertJson([
            'message' => 'Не передан borderCrossingId'
        ]);
    }

    /**
     * Тест на успешное создание отчета
     */
    public function test_create_report_with_valid_data(): void
    {
        $mockReportService = Mockery::mock(ReportService::class);

        // Подготовка данных
        $data = [
            'border_crossing_id' => 1,
            'transport_id' => 1,
            'user_id' => 1,
            'checkpoint_queue' => '2024-07-30 10:00:00',
            'checkpoint_entry' => '2024-07-30 10:05:00',
            'checkpoint_exit' => '2024-07-30 10:15:00',
            'comment' => 'Sample comment4',
        ];

        // Создание mock-объекта Report
        $mockReport = Mockery::mock(Report::class);
        $mockReport->shouldReceive('fill')->with($data)->andReturnSelf();
        $mockReport->shouldReceive('toJson')->andReturn(json_encode($data));

        // Настройка mock-объекта ReportService
        $mockReportService
            ->shouldReceive('createReport')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn($mockReport);

        // Привязка mock-объекта к контейнеру
        $this->app->instance(ReportService::class, $mockReportService);

        // Подготовка запроса
        $response = $this->postJson('/api/directions/borderCrossing/reports', $data);

        // Проверка, что статус ответа 201
        $response->assertStatus(Response::HTTP_CREATED);

        // Проверка структуры ответа
        $response->assertJson($data);
    }

    /**
     * Тест на не успешное создание отчета без border_crossing_id
     */
    public function test_create_report_without_border_crossing_id_field(): void
    {
        // Подготовка данных
        $data = [
            'transport_id' => 1,
            'user_id' => 1,
            'checkpoint_queue' => '2024-07-30 10:00:00',
            'checkpoint_entry' => '2024-07-30 10:05:00',
            'checkpoint_exit' => '2024-07-30 10:15:00',
            'comment' => 'Sample comment4',
        ];

        // Подготовка запроса
        $response = $this->postJson('/api/directions/borderCrossing/reports', $data);

        // Проверка, что статус ответа 201
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Проверка, что ответ содержит правильное сообщение
        $response->assertJson([
            'message' => 'The border crossing id field is required.'
        ]);
    }

    /**
     * Тест на не успешное создание отчета с неправильным border_crossing_id
     */
    public function test_create_report_with_invalid_border_crossing_id_field(): void
    {
        // Подготовка данных
        $data = [
            'border_crossing_id' => "invalid",
            'transport_id' => 1,
            'user_id' => 1,
            'checkpoint_queue' => '2024-07-30 10:00:00',
            'checkpoint_entry' => '2024-07-30 10:05:00',
            'checkpoint_exit' => '2024-07-30 10:15:00',
            'comment' => 'Sample comment4',
        ];

        // Подготовка запроса
        $response = $this->postJson('/api/directions/borderCrossing/reports', $data);

        // Проверка, что статус ответа 201
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Проверка, что ответ содержит правильное сообщение
        $response->assertJson([
            'message' => 'The selected border crossing id is invalid.'
        ]);
    }
}
