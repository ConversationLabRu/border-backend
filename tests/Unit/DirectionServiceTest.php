<?php

namespace Tests\Unit;

use App\Http\directions\Entities\Direction;
use App\Http\directions\Services\DirectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\TestCase;

class DirectionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DirectionService $directionService;

    protected function setUp(): void
    {
        parent::setUp();

        // Создание инстанса сервиса
        $this->directionService = new DirectionService();
    }

    /**
     * Тестирование метода getAllDirections с использованием моков.
     *
     * @return void
     */
    public function test_get_all_directions()
    {
        // Создание мока для класса Direction
        $directionMock = Mockery::mock('alias:'.Direction::class);

        // Определение поведения мока
        $directionMock->shouldReceive('all')
            ->once()
            ->andReturn(collect([
                (object) ['id' => 1, 'name' => 'Direction 1'],
                (object) ['id' => 2, 'name' => 'Direction 2'],
            ]));

        // Вызов метода сервиса
        $result = $this->directionService->getAllDirections();

        // Проверка результата
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals('Direction 1', $result[0]->name);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals('Direction 2', $result[1]->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
