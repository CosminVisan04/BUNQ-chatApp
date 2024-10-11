<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Illuminate\Database\Capsule\Manager as Capsule;

return function (App $app) {

    // Handle CORS Pre-Flight OPTIONS request
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    // Root route (optional)
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Welcom to the chat app!');
        return $response;
    });

    // User routes
    $app->group('/users', function (Group $group) {

        // Get all users
        $group->get('', function (Request $request, Response $response) {
            $users = Capsule::table('users')->get();
            $response->getBody()->write(json_encode($users));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Create a new user
        $group->post('', function (Request $request, Response $response) {
            $data = $request->getParsedBody();
            Capsule::table('users')->insert([
                'username' => $data['username'],
                'token' => $data['token']
            ]);
            $response->getBody()->write(json_encode(['message' => 'User created']));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Get a specific user by ID
        $group->get('/{id}', function (Request $request, Response $response, array $args) {
            $user = Capsule::table('users')->where('id', $args['id'])->first();
            if (!$user) {
                return $response->withStatus(404)->write(json_encode(['error' => 'User not found']));
            }
            $response->getBody()->write(json_encode($user));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });

    // Group routes
    $app->group('/groups', function (Group $group) {

        // Get all groups
        $group->get('', function (Request $request, Response $response) {
            $groups = Capsule::table('groups')->get();
            $response->getBody()->write(json_encode($groups));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Create a new group
        $group->post('', function (Request $request, Response $response) {
            $data = $request->getParsedBody();
            Capsule::table('groups')->insert(['name' => $data['name']]);
            $response->getBody()->write(json_encode(['message' => 'Group created']));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Join a group
        $group->post('/{group_id}/join', function (Request $request, Response $response, array $args) {
            $group = Capsule::table('groups')->where('id', $args['group_id'])->first();
            if (!$group) {
                return $response->withStatus(404)->write(json_encode(['error' => 'Group not found']));
            }
            $response->getBody()->write(json_encode(['message' => 'Joined group']));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Get all messages in a group
        $group->get('/{group_id}/messages', function (Request $request, Response $response, array $args) {
            $messages = Capsule::table('messages')->where('group_id', $args['group_id'])->get();
            if ($messages->isEmpty()) {
                return $response->withStatus(404)->write(json_encode(['error' => 'No messages found in this group']));
            }
            $response->getBody()->write(json_encode($messages));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Send a message to a group
        $group->post('/{group_id}/messages', function (Request $request, Response $response, array $args) {
            $data = $request->getParsedBody();
            $group = Capsule::table('groups')->where('id', $args['group_id'])->first();

            if (!$group) {
                return $response->withStatus(404)->write(json_encode(['error' => 'Group not found']));
            }

            Capsule::table('messages')->insert([
                'group_id' => $args['group_id'],
                'user_id' => $data['user_id'],
                'content' => $data['content'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response->getBody()->write(json_encode(['message' => 'Message sent']));
            return $response->withHeader('Content-Type', 'application/json');
        });
    });
};
