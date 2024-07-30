<?php

namespace Tests\Unit;

use App\Http\Middleware\AuthorizationAPI;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class AuthorizationAPITest extends TestCase
{
    /**
     * Тест на наличие header
     */
    public function test_missing_authorization_header()
    {
        $middleware = new AuthorizationAPI();

        $request = Request::create('/api/test/testMethod');
        $response = $middleware->handle($request, function () {
            return new Response();
        });

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Authentication header is missing']),
            $response->getContent()
        );
    }

    /**
     * Тест на неправильный header с кол-вом элементов в нем не равынм двум
     */
    public function test_invalid_authorization_header()
    {
        $middleware = new AuthorizationAPI();

        $request = Request::create('/api/test/testMethod', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'tma']);
        $response = $middleware->handle($request, function () {
            return new Response();
        });

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invalid authorization header']),
            $response->getContent()
        );
    }

    /**
     * Тест на неправильный header без префикса tma
     */
    public function test_invalid_without_tma_prefix_authorization_header()
    {
        $middleware = new AuthorizationAPI();

        $request = Request::create('/api/test/testMethod', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'bearer some_invalid_token']);
        $response = $middleware->handle($request, function () {
            return new Response();
        });

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invalid authorization header']),
            $response->getContent()
        );
    }

    /**
     * Тест на не пройденную проверку токена
     */
    public function test_invalid_security_token()
    {
        $middleware = Mockery::mock(AuthorizationAPI::class)->makePartial();
        $middleware->shouldReceive('checkSecurityTGBot')->andReturn(false);

        $request = Request::create('/api/test/testMethod', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'tma some_invalid_token']);
        $response = $middleware->handle($request, function () {
            return new Response();
        });

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Доступ к API запрещен']),
            $response->getContent()
        );
    }

    /**
     * Тест на пройденную проверку
     */
    public function test_successful_authorization()
    {
        $middleware = Mockery::mock(AuthorizationAPI::class)->makePartial();
        $middleware->shouldReceive('checkSecurityTGBot')->andReturn(true);

        $request = Request::create('/api/test', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'tma some_valid_token']);
        $response = $middleware->handle($request, function () {
            return new Response('Success', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }
}
