<?php

declare(strict_types=1);

namespace Http\Controllers\Api;

use Fig\Http\Message\StatusCodeInterface;
use Tests\BaseFeatureTestCase;
use Tests\Fixtures\UserFixture;

class UserControllerTest extends BaseFeatureTestCase
{
    public function testList(): void
    {
        $this->loadFixtures();

        $request = $this->createRequest('GET', '/api/users');
        $response = $this->app->handle($request);

        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'username' => 'editor',
                    'email' => 'editor@no-reply.com',
                ],
                [
                    'id' => 2,
                    'username' => 'administrator',
                    'email' => 'administrator@no-reply.com',
                ],
            ],
            'meta' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 2,
                'total_pages' => 1,
            ],
        ];

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    public function testCreate(): void
    {
        $data = [
            'username' => 'administrator',
            'email' => 'administrator@no-reply.com',
        ];
        $request = $this->createRequest('POST', '/api/users')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $expectedResult = [
            'id' => 1,
            'username' => 'administrator',
            'email' => 'administrator@no-reply.com',
        ];

        $this->assertSame(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    public function testCreateWithInvalidInput(): void
    {
        $data = [
            'email' => 'tr.com',
        ];
        $request = $this->createRequest('POST', '/api/users')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    protected function loadFixtures(): void
    {
        $userFixture = new UserFixture();
        $userFixture->load($this->entityManager);
    }
}
