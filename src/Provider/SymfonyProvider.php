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

namespace Narrowspark\Automatic\Versions\Provider;

use GuzzleHttp\Psr7\Request;
use Http\Client\Curl\Client;
use Narrowspark\Automatic\Versions\Contract\Provider;
use const JSON_THROW_ON_ERROR;
use function Safe\json_decode;

final class SymfonyProvider implements Provider
{
    public function __construct(private Client $client)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int|string, array>
     */
    public function fetch(): array
    {
        $response = $this->client
            ->sendRequest(new Request(
                'GET',
                'https://flex.symfony.com/versions.json',
                [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Narrowspark Automatic Versions Curl',
                ]
            ));

        $stream = $response->getBody();
        $json = json_decode(trim($stream->__toString()), true, 512, JSON_THROW_ON_ERROR);

        return $json['splits'];
    }
}
