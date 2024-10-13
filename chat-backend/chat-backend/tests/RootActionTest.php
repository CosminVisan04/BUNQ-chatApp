<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use PHPUnit\Framework\TestCase;

class RootActionTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        // Create the Slim app using AppFactory
        $this->app = AppFactory::create();

        // Load the routes
        (require __DIR__ . '/../app/routes.php')($this->app);
    }

    // Test root route (GET /)
    public function testRootRoute()
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', '/');

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertStringContainsString('Welcome to the chat app!', $responseBody);
    }
}
