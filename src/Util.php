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

namespace Narrowspark\Automatic\Versions;

use JsonException;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use function Safe\json_encode;
use function Safe\preg_replace;

final class Util
{
    /**
     * @psalm-param array<array-key, mixed> $data
     *
     * @throws \Safe\Exceptions\JsonException
     * @throws \Safe\Exceptions\PcreException
     * @throws JsonException
     */
    public static function dumpJson(array $data): string
    {
        $json = json_encode($data, JSON_THROW_ON_ERROR + JSON_UNESCAPED_SLASHES);

        $replaceArray = preg_replace('/\[\s+\]/', '[]', $json);

        return preg_replace('/\{\s+\}/', '{}', $replaceArray);
    }
}
