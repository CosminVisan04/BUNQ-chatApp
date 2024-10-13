<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;

class UserRoutesTest extends TestCase {
    protected $app;

    protected function setUp(): void {
        $this->app = AppFactory::create();

        (require __DIR__ . '/../app/routes.php')($this->app);

        Capsule::connection()->beginTransaction();
    }

    protected function tearDown(): void {

        Capsule::connection()->rollBack();
    }

    public static function setUpBeforeClass(): void {
        // Set up Capsule (Database)
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../database.sqlite',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function ($table) {
                $table->id();
                $table->string('username');
                $table->string('token');
            });
        }
    }

    public function testCreateUser() {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', '/users', [])
            ->withParsedBody([
                'username' => 'testuser',
                'token' => 'abcd1234'
            ]);

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals('User created', $json['message']);

        $user = Capsule::table('users')->where('username', 'testuser')->first();
        $this->assertNotNull($user);
        $this->assertEquals('abcd1234', $user->token);
    }

    // Test getting all users
    public function testGetAllUsers() {

        Capsule::table('users')->insert([
            'username' => 'testuser2',
            'token' => 'xyz789'
        ]);

        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', '/users', []);

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertIsArray($json);

        // Check that the inserted user is present in the results
        $usernames = array_column($json, 'username');
        $this->assertContains('testuser2', $usernames);
    }


    // Test getting a user by ID
    public function testGetUserById() {
        $id = Capsule::table('users')->insertGetId([
            'username' => 'testuser3',
            'token' => 'hijklmn'
        ]);

        $uriFactory = new UriFactory();
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', "/users/$id");

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals($id, $json['id']);
        $this->assertEquals('testuser3', $json['username']);
    }

    // Test getting a non-existing user
    public function testGetNonExistingUser() {
        $uriFactory = new UriFactory();
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', "/users/999");

        $response = $this->app->handle($request);
        $this->assertEquals(404, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals('User not found', $json['error']);
    }
}
