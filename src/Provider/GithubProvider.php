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

use Github\Client;
use Narrowspark\Automatic\Versions\Contract\Provider;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class GithubProvider implements Provider
{
    use LoggerAwareTrait;

    public function __construct(private Client $client, private string $organization)
    {
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     *
     * @return array<int|string, mixed[]>
     */
    public function fetch(): array
    {
        $organization = $this->client->organization();
        $repo = $this->client->repo();
        $repositories = $organization->repositories($this->organization);

        $splits = [];

        foreach ($repositories as $repository) {
            $fullName = $repository['full_name'];

            [$owner, $name] = explode('/', $fullName);

            $this->logger->info("Pulling branches from [{$fullName}].\n");

            $branches = $repo->branches($owner, $name);

            $splits[$fullName] = array_map(static fn (array $value) => $value['name'], $branches);
        }

        return $splits;
    }
}
