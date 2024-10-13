<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;

class GroupRoutesTest extends TestCase
{
    protected $app;

    public static function setUpBeforeClass(): void
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => __DIR__ . '/../database.sqlite',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();


        if (!Capsule::schema()->hasTable('groups')) {
            Capsule::schema()->create('groups', function ($table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Capsule::schema()->hasTable('messages')) {
            Capsule::schema()->create('messages', function ($table) {
                $table->id();
                $table->integer('group_id');
                $table->integer('user_id');
                $table->string('content');
                $table->timestamps(); 
            });
        }
    }

    protected function setUp(): void
    {
        $this->app = AppFactory::create();

        (require __DIR__ . '/../app/routes.php')($this->app);

        Capsule::connection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        Capsule::connection()->rollBack();
    }

    // Test creating a group
    public function testCreateGroup()
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', '/groups', [])
            ->withParsedBody([
                'name' => 'testgroup'
            ]);

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals('Group created', $json['message']);

        $group = Capsule::table('groups')->where('name', 'testgroup')->first();
        $this->assertNotNull($group);
    }

    // Test getting all groups
    public function testGetAllGroups()
    {
        // Insert a group into the database within the transaction
        Capsule::table('groups')->insert([
            'name' => 'testgroup2'
        ]);

        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', '/groups', []);

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertIsArray($json);

        $groupNames = array_column($json, 'name');
        $this->assertContains('testgroup2', $groupNames);
    }

    // Test joining a group
    public function testJoinGroup()
    {
        $groupId = Capsule::table('groups')->insertGetId([
            'name' => 'joinablegroup'
        ]);

        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', "/groups/$groupId/join");

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals('Joined group', $json['message']);
    }

    // Test fetching messages in a group
    public function testGetGroupMessages()
    {
        $groupId = Capsule::table('groups')->insertGetId([
            'name' => 'groupWithMessages'
        ]);

        Capsule::table('messages')->insert([
            'group_id' => $groupId,
            'user_id' => 1,
            'content' => 'Hello Group',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', "/groups/$groupId/messages");

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertIsArray($json);
        $this->assertCount(1, $json);
    }

    // Test fetching messages for a non-existing group
    public function testGetNonExistingGroupMessages()
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('GET', "/groups/999/messages");

        $response = $this->app->handle($request);
        $this->assertEquals(404, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals('No messages found in this group', $json['error']);
    }
}
