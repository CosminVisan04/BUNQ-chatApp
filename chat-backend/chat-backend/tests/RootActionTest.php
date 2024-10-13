<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Slim\Factory\AppFactory;

class RootActionTest extends TestCase
{
    public function testRootRoute()
    {
        // Create an instance of the Slim app
        $app = $this->createApp();

        // Create a request to the root URL '/'
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $response = new Response();

        // Execute the application and get the response
        $response = $app->handle($request);

        // Check if the status code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Check if the response body contains 'Welcome to the chat app!'
        $body = (string) $response->getBody();
        $this->assertStringContainsString('Welcome to the chat app!', $body);
    }

    // Create a Slim app instance with routes and settings
    private function createApp(): App
    {
        $app = AppFactory::create();

        // Include the routes from your routes.php
        require __DIR__ . '/../app/routes.php';


        return $app;
    }
}
