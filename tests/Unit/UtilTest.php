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

use Narrowspark\Automatic\Versions\Util;
use PHPStan\Testing\TestCase;

/**
 * @internal
 *
 * @covers \Narrowspark\Automatic\Versions\Util
 *
 * @small
 */
final class UtilTest extends TestCase
{
    /** @var array<string, int|string> */
    private const BOOK = [
        'title' => 'bar',
        'author' => 'foo',
        'edition' => 6,
    ];

    public function testJsonDump(): void
    {
        $dump = Util::dumpJson(self::BOOK);

        self::assertJsonStringEqualsJsonString('{
    "title": "bar",
    "author": "foo",
    "edition": 6
}', $dump);
    }
}
