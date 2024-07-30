<?php

namespace Tests\Feature;

use App\Http\directions\borderCrossings\cameras\Services\CameraService;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class CamerasAPITest extends TestCase
{
    /**
     * Тест на успешное получение данных
     */
    public function test_get_all_cameras(): void
    {
        $mockBorderCrossingService = Mockery::mock(CameraService::class);

        $json = '[
            {
                "id": 1,
                "border_crossing_id": 1,
                "url": "http://example.com/camera1",
                "description": "Camera at checkpoint 1"
            }
        ]';

        // Преобразование JSON в ассоциативный массив PHP
        $mockData = json_decode($json, true);

        // Set up the expectation for the mock
        $mockBorderCrossingService
            ->shouldReceive('getAllByBorderCrossings')
            ->once()
            ->andReturn($mockData);

        // Bind the mock to the container
        $this->app->instance(CameraService::class, $mockBorderCrossingService);

        // Send a GET request to the endpoint
        $response = $this->get('/api/directions/borderCrossing/cameras?borderCrossingId=1');

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response data matches the mock data
        $response->assertJson($mockData);

        $response->assertJsonCount(1);
    }

    /**
     * Тест на не успешное получение данных без GET параметра
     */
    public function test_get_all_cameras_without_get_parametr(): void
    {
        // Отправка GET запроса без параметра
        $response = $this->get('/api/directions/borderCrossing/cameras');

        // Проверка, что статус ответа 400
        $response->assertStatus(400);

        // Проверка, что ответ содержит правильное сообщение
        $response->assertJson([
            'message' => 'Не передан borderCrossingId'
        ]);
    }
}
