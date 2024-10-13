<?php

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;

class MessageRoutesTest extends TestCase {
    protected $app;

    public static function setUpBeforeClass(): void {
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

    protected function setUp(): void {
        $this->app = AppFactory::create();
        (require __DIR__ . '/../app/routes.php')($this->app);

        Capsule::connection()->beginTransaction();
    }

    protected function tearDown(): void {
        Capsule::connection()->rollBack();
    }

    // Test sending a message to a group
    public function testSendMessageToGroup() {
        $groupId = Capsule::table('groups')->insertGetId([
            'name' => 'groupWithMessages'
        ]);

        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', "/groups/$groupId/messages", [])
            ->withParsedBody([
                'user_id' => 1,
                'content' => 'Hello Group'
            ]);

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $json = json_decode($responseBody, true);
        $this->assertEquals('Message sent', $json['message']);

        $message = Capsule::table('messages')->where('content', 'Hello Group')->first();
        $this->assertNotNull($message);
    }

    // Test fetching all messages in a group
    public function testGetGroupMessages() {
        // Insert a unique group for this test
        $groupId = Capsule::table('groups')->insertGetId([
            'name' => 'uniqueGroupForTesting'
        ]);


        Capsule::table('messages')->where('group_id', $groupId)->delete();

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

   
        foreach ($json as $message) {
            $this->assertEquals($groupId, $message['group_id']); 
        }

   
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
