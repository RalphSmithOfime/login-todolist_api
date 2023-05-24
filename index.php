<?php

require 'User.php';
require 'Task.php';
require 'Auth.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App;

require 'vendor/autoload.php';

$app = new App();

// Register a new user
$app->post('/register', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];

    $user = new User();

    if ($user->registerUser($username, $password)) {
        $response->getBody()->write(json_encode(array('message' => 'User registered successfully')));
        return $response->withStatus(201);
    } else {
        $response->getBody()->write(json_encode(array('message' => 'Username is already taken, use a different username')));
        return $response->withStatus(400);
    }
});

// User login and get JWT token
$app->post('/login', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];

    $user = new User();
    $loggedInUser = $user->loginUser($username, $password);

    if ($loggedInUser) {
        $token = Auth::generateToken($loggedInUser['id']);

        $response->getBody()->write(json_encode(array('token' => $token)));
        return $response->withStatus(200);
    } else {
        $response->getBody()->write(json_encode(array('message' => 'Invalid username or password')));
        return $response->withStatus(401);
    }
});

// Add a task to the to-do list
$app->post('/tasks', function (Request $request, Response $response) {
    $headers = $request->getHeaders();
    print_r($headers)
    $token = $headers['HTTP_AUTHORIZATION'][0];

    $userId = Auth::verifyToken($token);

    if (!$userId) {
        $response->getBody()->write(json_encode(array('message' => 'Unauthorized')));
        return $response->withStatus(401);
    }

    $data = $request->getParsedBody();
    $task = $data['task'];

    $taskManager = new Task();
    $taskManager->addTask($userId, $task);

    $response->getBody()->write(json_encode(array('message' => 'Task added successfully')));
    return $response->withStatus(201);
});

// Get the user's tasks
$app->get('/tasks', function (Request $request, Response $response) {
    $headers = $request->getHeaders();
    $token = $headers['HTTP_AUTHORIZATION'][0];

    $userId = Auth::verifyToken($token);

    if (!$userId) {
        $response->getBody()->write(json_encode(array('message' => 'Unauthorized')));
        return $response->withStatus(401);
    }

    $taskManager = new Task();
    $tasks = $taskManager->getTasks($userId);

    $response->getBody()->write(json_encode($tasks));
    return $response->withStatus(200);
});

$app->run();