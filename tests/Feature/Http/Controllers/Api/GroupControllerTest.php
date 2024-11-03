<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use Fig\Http\Message\StatusCodeInterface;
use Tests\BaseFeatureTestCase;
use Tests\Fixtures\GroupFixture;

class GroupControllerTest extends BaseFeatureTestCase
{
    public function testList(): void
    {
        $this->loadFixtures();

        $request = $this->createRequest('GET', '/api/groups');
        $response = $this->app->handle($request);

        $expectedResult = [
            [
                'id' => 1,
                'name' => 'Group 1',
            ],
            [
                'id' => 2,
                'name' => 'Group 2',
            ],
        ];

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    public function testCreate(): void
    {
        $data = [
            'name' => 'Group 1',
        ];
        $request = $this->createRequest('POST', '/api/groups')
            ->withParsedBody($data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $expectedResult = [
            'id' => 1,
            'name' => 'Group 1',
        ];

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        $this->assertJsonData($expectedResult, $response);
    }

    protected function loadFixtures(): void
    {
        $groupFixture = new GroupFixture();
        $groupFixture->load($this->entityManager);
    }
}
