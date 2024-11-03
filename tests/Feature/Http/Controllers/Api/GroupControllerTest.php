<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use Fig\Http\Message\StatusCodeInterface;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    public function testList(): void
    {
        $request = $this->createRequest('GET', '/api/groups');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertResponseContains('Welcome!', $response);
    }
}
