<?php

namespace Tests\Feature;

use App\Http\directions\Services\DirectionService;
use Mockery;
use Tests\TestCase;

class DirectionAPITest extends TestCase
{

    /**
     * Test that the getAll method returns the correct data.
     *
     * @return void
     */
    public function test_get_all_directions(): void
    {
        $mockDirectionService = Mockery::mock(DirectionService::class);

        $mockData = [
            ['id' => 1, 'name' => 'North',
                'logo' => 'logo-north',
                'image' => 'image-north',
                'info' => "{\"details\": \"North direction\"}"],
            ['id' => 2,
                'name' => 'South',
                'logo' => 'logo-south',
                'image' => 'image-south',
                'info' => "{\"details\": \"South direction\"}"],
        ];

        // Set up the expectation for the mock
        $mockDirectionService
            ->shouldReceive('getAllDirections')
            ->once()
            ->andReturn($mockData);

        // Bind the mock to the container
        $this->app->instance(DirectionService::class, $mockDirectionService);

        // Send a GET request to the endpoint
        $response = $this->get('/api/directions');

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response data matches the mock data
        $response->assertJson($mockData);

        $response->assertJsonCount(2);
    }

    /**
     * Test that the getAll method returns the correct data.
     *
     * @return void
     */
    public function test_get_all_directions_zero_elem(): void
    {
        $mockDirectionService = Mockery::mock(DirectionService::class);

        $mockData = [];

        // Set up the expectation for the mock
        $mockDirectionService
            ->shouldReceive('getAllDirections')
            ->once()
            ->andReturn($mockData);

        // Bind the mock to the container
        $this->app->instance(DirectionService::class, $mockDirectionService);

        // Send a GET request to the endpoint
        $response = $this->get('/api/directions');

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response data matches the mock data
        $response->assertJson($mockData);

        $response->assertJsonCount(0);
    }
}
