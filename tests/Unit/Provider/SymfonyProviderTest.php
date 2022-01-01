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

use GuzzleHttp\Psr7\Request;
use Http\Client\Curl\Client;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Narrowspark\Automatic\Versions\Provider\SymfonyProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 *
 * @covers \Narrowspark\Automatic\Versions\Provider\SymfonyProvider
 *
 * @medium
 */
final class SymfonyProviderTest extends MockeryTestCase
{
    private MockInterface $clientMock;

    private SymfonyProvider $symfonyProvider;

    protected function setUp(): void
    {
        $this->clientMock = Mockery::mock(Client::class);
        $this->symfonyProvider = new SymfonyProvider($this->clientMock);
    }

    public function testFetch(): void
    {
        $responseMock = Mockery::mock(ResponseInterface::class);

        $streamMock = Mockery::mock(StreamInterface::class);
        $streamMock->shouldReceive('__toString')
            ->once()
            ->andReturn('{ "splits": {} }');

        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($streamMock);

        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->with(Mockery::type(Request::class))
            ->andReturn($responseMock);

        self::assertSame([], $this->symfonyProvider->fetch());
    }
}
