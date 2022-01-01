<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Daniel Bannert
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/narrowspark/automatic-versions
 */

namespace Narrowspark\Automatic\Versions\Tests\Unit\Provider;

use Github\Api\Organization;
use Github\Api\Repo;
use Github\Client;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Narrowspark\Automatic\Versions\Provider\GithubProvider;

/**
 * @internal
 *
 * @covers \Narrowspark\Automatic\Versions\Provider\GithubProvider
 *
 * @medium
 */
final class GithubProviderTest extends MockeryTestCase
{
    /** @var string */
    private const ORGANIZATION = 'test';

    private MockInterface $clientMock;

    private GithubProvider $githubProvider;

    protected function setUp(): void
    {
        $this->clientMock = Mockery::mock(Client::class);
        $this->githubProvider = new GithubProvider($this->clientMock, self::ORGANIZATION);
    }

    public function testFetch(): void
    {
        $organizationApiMock = Mockery::mock(Organization::class);
        $organizationApiMock->shouldReceive('repositories')
            ->with(self::ORGANIZATION)
            ->andReturn([[
                'full_name' => 'test/test',
            ]]);

        $this->clientMock->shouldReceive('organization')
            ->once()
            ->andReturn($organizationApiMock);

        $repoApiMock = Mockery::mock(Repo::class);
        $repoApiMock->shouldReceive('branches')
            ->once()
            ->with('test', 'test')
            ->andReturn([[
                'name' => '1.x',
            ]]);

        $this->clientMock->shouldReceive('repo')
            ->once()
            ->andReturn($repoApiMock);

        self::assertSame([
            'test/test' => [
                '1.x',
            ],
        ], $this->githubProvider->fetch());
    }
}
