<?php

namespace App\Http\Controllers\Api;

use App\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function __construct(protected UserService $userService)
    {
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = $data['username'];

        // Create a new user with the provided username
        $user = $this->userService->createUser($username);

        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
