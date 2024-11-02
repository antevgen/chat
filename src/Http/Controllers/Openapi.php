<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API documentation for the Chat Application backend.",
    title: "Chat Application API"
)]
#[OA\Server(
    url: "http://chat.local:8082/api",
    description: "Development server (HTTP)"
)]
class Openapi
{
}
