<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use Fig\Http\Message\StatusCodeInterface;
use Tests\BaseFeatureTestCase;
use Tests\Fixtures\GroupFixture;
use Tests\Fixtures\UserFixture;

class GroupControllerTest extends BaseFeatureTestCase
{
    public function testList(): void
    {
        $this->loadGroupFixtures();

        $request = $this->createRequest('GET', '/api/groups');
        $response = $this->app->handle($request);

        $expectedResult = [
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Group 1',
                    'members' => [],
                ],
                [
                    'id' => 2,
                    'name' => 'Group 2',
                    'members' => [],
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
        $this->loadUserFixtures();
        $data = [
            'name' => 'Group 1',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $expectedResult = [
            'id' => 1,
            'name' => 'Group 1',
            'members' => [
                [
                    'id' => 1,
                    'username' => 'editor',
                    'email' => 'editor@no-reply.com',
                ],
            ],
        ];

        $this->assertSame(StatusCodeInterface::STATUS_CREATED, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    public function testCreateWithInvalidInput(): void
    {
        $this->loadUserFixtures();
        $data = [
            'name' => 'Gr',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testCreateWithExistingName(): void
    {
        $this->loadGroupFixtures();
        $this->loadUserFixtures();
        $data = [
            'name' => 'Group 1',
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
    }

    public function testJoinNonExistentUser(): void
    {
        $this->loadGroupFixtures();

        $data = [
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/1/members')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testJoinUserToNonExistentGroup(): void
    {
        $this->loadUserFixtures();

        $data = [
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/1/members')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testJoinExistingMemberToGroup(): void
    {
        $this->loadGroupFixturesWithMember();

        $data = [
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/1/members')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
    }

    public function testJoinMemberToGroup(): void
    {
        $this->loadGroupFixturesWithMember();

        $data = [
            'user_id' => 1,
        ];
        $request = $this->createRequest('POST', '/api/groups/2/members')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    protected function loadUserFixtures(): void
    {
        $userFixture = new UserFixture();
        $userFixture->load($this->entityManager);
    }

    protected function loadGroupFixtures(): void
    {
        $groupFixture = new GroupFixture();
        $groupFixture->load($this->entityManager);
    }

    protected function loadGroupFixturesWithMember(): void
    {
        $groupFixture = new GroupFixture();
        $groupFixture->loadGroupsWithMember($this->entityManager);
    }
}
