<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Slim\App;
use Slim\Factory\AppFactory;

class UserTest extends TestCase
{
    public function testGetUsers()
    {
        // Create an instance of the Slim app
        $app = $this->createApp();

        // Create a request to the '/users' URL
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/users');
        $response = new Response();

        // Execute the application and get the response
        $response = $app->handle($request);

        // Check if the status code is 200
        $this->assertEquals(200, $response->getStatusCode());

        // Check if the response content type is JSON
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

        // Check if the response contains a list of users
        $body = json_decode((string) $response->getBody(), true);
        $this->assertIsArray($body);  // Check if the response is an array (list of users)
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
