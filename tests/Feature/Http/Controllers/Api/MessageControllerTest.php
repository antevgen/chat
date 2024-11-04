<?php

declare(strict_types=1);

namespace Http\Controllers\Api;

use Fig\Http\Message\StatusCodeInterface;
use Tests\BaseFeatureTestCase;
use Tests\Fixtures\GroupFixture;
use Tests\Fixtures\MessageFixture;

class MessageControllerTest extends BaseFeatureTestCase
{
    public function testList(): void
    {
        $this->loadFixtures();

        $request = $this->createRequest('GET', '/api/groups/1/messages');
        $response = $this->app->handle($request);

        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'subject' => 'Message 1',
                    'content' => 'Content 1',
                    'user' => [
                        'id' => 1,
                        'username' => 'admin',
                        'email' => 'admin@test.com',
                    ],
                ],
            ],
            'meta' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 1,
                'total_pages' => 1,
            ],
        ];

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    public function testCreate(): void
    {
        $this->loadGroupFixturesWithMember();
        $data = [
            'subject' => 'Message 1',
            'content' => 'Content 1',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/1/messages')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $expectedResult = [
            'id' => 1,
            'subject' => 'Message 1',
            'content' => 'Content 1',
            'user' => [
                'id' => 1,
                'username' => 'admin',
                'email' => 'admin@test.com',
            ],
        ];

        $this->assertSame(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    public function testCreateWithInvalidInput(): void
    {
        $this->loadGroupFixturesWithMember();
        $data = [
            'subject' => 'Me',
            'content' => 'Content 1',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/1/messages')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testCreateWithNonExistingGroup(): void
    {
        $this->loadGroupFixturesWithMember();
        $data = [
            'subject' => 'Message1',
            'content' => 'Content 1',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/3/messages')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateWithNonExistingUser(): void
    {
        $this->loadGroupFixturesWithMember();
        $data = [
            'subject' => 'Message1',
            'content' => 'Content 1',
            'user_id' => 3,
        ];
        $request = $this->createRequest('POST', '/api/groups/1/messages')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testCreateWithNonNonMemberGroup(): void
    {
        $this->loadGroupFixturesWithMember();
        $data = [
            'subject' => 'Message 1',
            'content' => 'Content 1',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/2/messages')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
    }

    protected function loadFixtures(): void
    {
        $messageFixture = new MessageFixture();
        $messageFixture->load($this->entityManager);
    }

    protected function loadGroupFixturesWithMember(): void
    {
        $groupFixture = new GroupFixture();
        $groupFixture->loadGroupsWithMember($this->entityManager);
    }
}
