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

namespace Narrowspark\Automatic\Versions\Contract;

interface Provider
{
    /**
     * Fetch data from a source.
     *
     * @return array<int|string, array>
     */
    public function fetch(): array;
}
