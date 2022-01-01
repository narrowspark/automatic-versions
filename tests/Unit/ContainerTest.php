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

namespace Narrowspark\Automatic\Versions\Tests\Unit;

use InvalidArgumentException;
use Narrowspark\Automatic\Versions\Container;
use PHPUnit\Framework\TestCase;
use stdClass;
use function is_array;
use function is_object;
use function is_string;

/**
 * @internal
 *
 * @covers \Narrowspark\Automatic\Versions\Container
 *
 * @medium
 */
final class ContainerTest extends TestCase
{
    private Container $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->container = new Container([
            stdClass::class => static fn (): string => stdClass::class,
            'vendor-dir' => static fn (): string => '/vendor',
        ]);
    }

    /**
     * @dataProvider provideContainerInstancesCases
     *
     * @param class-string<object>|mixed[] $expected
     */
    public function testContainerInstances(string $key, $expected): void
    {
        $value = $this->container->get($key);

        if (is_string($value) || (is_array($value) && is_array($expected))) {
            self::assertSame($expected, $value);
        }

        if (! is_object($value)) {
            return;
        }

        if (! is_string($expected)) {
            return;
        }

        self::assertInstanceOf($expected, $value);
    }

    public function testGetThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier [test] is not defined.');

        $this->container->get('test');
    }

    public function testGetCache(): void
    {
        self::assertSame('/vendor', $this->container->get('vendor-dir'));

        $this->container->set('vendor-dir', static fn (): string => 'test');

        self::assertSame('/vendor', $this->container->get('vendor-dir'));
    }

    public function testGetAll(): void
    {
        self::assertCount(2, $this->container->getAll());
    }

    /**
     * @return \Iterator<array<class-string<\stdClass>>>
     */
    public static function provideContainerInstancesCases(): iterable
    {
        yield [stdClass::class, stdClass::class];
    }
}
