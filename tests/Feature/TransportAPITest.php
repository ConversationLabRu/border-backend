<?php

namespace Tests\Feature;

use App\Http\directions\borderCrossings\reports\transports\Services\TransportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransportAPITest extends TestCase
{
    /**
     * Тест на успешное получение данных
     */
    public function test_get_all_transport(): void
    {
        $mockTransportService = \Mockery::mock(TransportService::class);


        $json = '[
            {
                "id": 1,
                "name": "Truck",
                "icon": "icon-truck"
            },
            {
                "id": 2,
                "name": "Car",
                "icon": "icon-car"
            },
            {
                "id": 3,
                "name": "Bus",
                "icon": "icon-bus"
            },
            {
                "id": 4,
                "name": "Bicycle",
                "icon": "icon-bicycle"
            }
        ]';


        $mockData = json_decode($json, true);

        if (is_null($mockData)) {
            echo 'JSON Decode Error: ' . json_last_error_msg();
        }

        $mockTransportService
            ->shouldReceive('getAllTransport')
            ->once()
            ->andReturn($mockData);

        $this->app->instance(TransportService::class, $mockTransportService);

        $response = $this->get('/api/directions/borderCrossing/reports/transports');

        $response->assertStatus(200);
        $response->assertJson($mockData);
        $response->assertJsonCount(4);
    }
}
